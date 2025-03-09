import { connectToDatabase } from "../config/db.js";
import { JSDOM } from 'jsdom';
import DOMPurify from 'dompurify';
import sanitizeHtml from 'sanitize-html'; 

const window = new JSDOM('').window;
const purify = DOMPurify(window);

export const getSurveysByUser = async (req, res) => {
    const userEmail = req.session.user?.email;

    if (!userEmail) {
        return res.status(401).json({ message: 'Unauthorized - User session is invalid or email is missing' });
    }

    const query = `
        SELECT DISTINCT
            s.survey_id,
            s.survey_name,
            s.academic_year
        FROM
            surveys s
                LEFT JOIN
            restricted_surveys rs ON s.survey_id = rs.survey_id
                LEFT JOIN
            user_classes uc_restricted ON rs.class_code = uc_restricted.class_code
                LEFT JOIN
            classes c_user ON c_user.class_code = uc_restricted.class_code
                LEFT JOIN
            programs p ON s.program_id = p.program_id
        WHERE
            s.is_published = 1
          AND s.is_closed = 0
          AND NOT EXISTS (
            SELECT 1
            FROM accomplished_surveys ac
            WHERE ac.survey_id = s.survey_id
              AND ac.user_id = (SELECT user_id FROM users WHERE email = ?)
        )
          AND (
            (rs.class_code IS NULL
                AND EXISTS (
                    SELECT 1
                    FROM user_classes uc_user
                             JOIN classes c_user ON uc_user.class_code = c_user.class_code
                    WHERE c_user.program_id = s.program_id
                      AND uc_user.user_id = (SELECT user_id FROM users WHERE email = ?)
                ))
                OR (
                rs.class_code IS NOT NULL
                    AND uc_restricted.user_id = (SELECT user_id FROM users WHERE email = ?)
                )
            );
    `;

    try {
        const db = await connectToDatabase();
        db.query(query, [userEmail, userEmail, userEmail], (err, results) => {
            if (err) {
                return res.status(500).json({ message: 'Database query failed', error: err });
            }

            if (results.length > 0) {
                return res.status(200).json({ surveys: results });
            } else {
                return res.status(404).json({ message: 'No surveys available for the user' });
            }
        });
    } catch (error) {
        console.error('Error fetching surveys:', error);
        res.status(500).json({ message: 'Server error', error: error.message });
    }
};

export const fetchSurveyQuestions = async (req, res) => {
    const { surveyId } = req.params;  // Get the surveyId from the URL parameter

    try {
        const db = await connectToDatabase();

        // Query to get all questions for the given surveyId
        const queryQuestions = 'SELECT * FROM questions WHERE survey_id = ?';
        db.query(queryQuestions, [surveyId], async (err, questions) => {
            if (err) {
                console.error('Error fetching questions:', err);
                return res.status(500).json({ message: 'Error fetching survey questions', error: err });
            }

            // For each question, we need to fetch the choices if the question type is 'multiple_choice'
            for (const question of questions) {
                if (question.question_type === 'multiple_choice' || question.question_type === 'satisfaction' ||
                    question.question_type === 'relevance' || question.question_type === 'quality') {
                    const queryChoices = 'SELECT * FROM choices WHERE question_id = ?';
                    question.choices = await new Promise((resolve, reject) => {
                        db.query(queryChoices, [question.question_id], (err, choices) => {
                            if (err) reject(err);
                            resolve(choices);
                        });
                    });  // Add choices to the question object
                }
            }

            // Return the survey questions and their choices
            res.status(200).json({ questions });
        });
    } catch (error) {
        console.error('Error in fetching survey questions:', error);
        return res.status(500).json({ message: 'Server error', error: error.message });
    }
};


export const submitSurvey = async (req, res) => {
    const { surveyId, responses } = req.body;
    const userEmail = req.session.user?.email;

    if (!userEmail) {
        return res.status(401).json({ message: 'Unauthorized - User email is missing' });
    }

    try {
        const db = await connectToDatabase();

        // Get userId from the email
        const userQuery = 'SELECT user_id FROM users WHERE email = ?';
        const [userResult] = await new Promise((resolve, reject) => {
            db.query(userQuery, [userEmail], (err, result) => {
                if (err) reject(err);
                resolve(result);
            });
        });

        if (!userResult) {
            return res.status(404).json({ message: 'User not found' });
        }

        const userId = userResult.user_id;

        // Check if the user has already completed this survey
        const checkQuery = `
            SELECT * FROM accomplished_surveys
            WHERE user_id = ? AND survey_id = ?
        `;
        const [existingSurvey] = await new Promise((resolve, reject) => {
            db.query(checkQuery, [userId, surveyId], (err, result) => {
                if (err) reject(err);
                resolve(result);
            });
        });

        // If the survey has already been completed, return an error
        if (existingSurvey) {
            return res.status(400).json({ success: false, message: 'You have already completed this survey' });
        }

        // Ensure the survey exists
        const surveyCheckQuery = 'SELECT * FROM surveys WHERE survey_id = ?';
        const [surveyExists] = await new Promise((resolve, reject) => {
            db.query(surveyCheckQuery, [surveyId], (err, result) => {
                if (err) reject(err);
                resolve(result);
            });
        });

        if (!surveyExists || surveyExists.length === 0) {
            return res.status(404).json({ success: false, message: 'Survey not found' });
        }

        // Insert responses into the responses table
        const responseQuery = `
            INSERT INTO responses (user_id, question_id, short_answer, choice_id)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE short_answer = VALUES(short_answer), choice_id = VALUES(choice_id)
        `;

        for (let response of responses) {
            let { question_id, short_answer, choice_id } = response;

            if (short_answer) {
                short_answer = purify.sanitize(short_answer);
            }

            const choice = choice_id || null;
            const sanitizedShortAnswer = sanitizeHtml(short_answer, { allowedTags: [], allowedAttributes: {} });

            await new Promise((resolve, reject) => {
                db.query(responseQuery, [userId, question_id, sanitizedShortAnswer, choice], (err, result) => {
                    if (err) reject(err);
                    resolve(result);
                });
            });
        }

        const accomplishedQuery = `
            INSERT INTO accomplished_surveys (user_id, survey_id)
            VALUES (?, ?)
        `;
        await new Promise((resolve, reject) => {
            db.query(accomplishedQuery, [userId, surveyId], (err, result) => {
                if (err) reject(err);
                resolve(result);
            });
        });

        res.status(200).json({ success: true, message: "Survey submitted successfully" });

    } catch (error) {
        console.error('Error submitting survey:', error);
        res.status(500).json({ success: false, message: 'Error submitting survey', error: error.message });
    }
};

export const viewResponses = async (req, res) => {
    const { surveyId } = req.params;
    const userEmail = req.session.user?.email; // Get the email of the logged-in user.

    if (!userEmail) {
        return res.status(401).json({ message: 'Unauthorized - User session is invalid or email is missing' });
    }

    try {
        const db = await connectToDatabase();

        // Fetch the questions for the given survey
        const questionQuery = `
            SELECT q.question_id, q.question, q.question_type
            FROM questions q
            WHERE q.survey_id = ?
        `;

        const questions = await new Promise((resolve, reject) => {
            db.query(questionQuery, [surveyId], (err, results) => {
                if (err) reject(err);
                resolve(results);
            });
        });

        if (!questions || questions.length === 0) {
            return res.status(404).json({ message: 'No questions found for this survey' });
        }

        // Fetch the user_id using the logged-in user's email
        const userIdQuery = `
            SELECT user_id
            FROM users
            WHERE email = ?
        `;

        const [userIdResult] = await new Promise((resolve, reject) => {
            db.query(userIdQuery, [userEmail], (err, results) => {
                if (err) reject(err);
                resolve(results);
            });
        });

        if (!userIdResult || !userIdResult.user_id) {
            return res.status(404).json({ message: 'User not found' });
        }

        const userId = userIdResult.user_id;

        // Query to fetch responses for the logged-in user
        const responseQuery = `
            SELECT r.user_id, r.question_id, r.short_answer, r.choice_id, c.choice, u.email AS user_email
            FROM responses r
                     LEFT JOIN choices c ON r.choice_id = c.choice_id
                     LEFT JOIN users u ON r.user_id = u.user_id
            WHERE r.user_id = ? AND r.question_id IN (SELECT question_id FROM questions WHERE survey_id = ?)
        `;

        const responses = await new Promise((resolve, reject) => {
            db.query(responseQuery, [userId, surveyId], (err, results) => {
                if (err) reject(err);
                resolve(results);
            });
        });

        // Organize responses by question
        const organizedResponses = questions.map((question) => {
            const questionResponses = responses.filter(
                (response) => response.question_id === question.question_id
            ).map((response) => {
                const answerText = response.choice ? response.choice : response.short_answer;
                return {
                    answer: answerText,
                    user: response.user_email, // Return the email instead of the userId
                };
            });

            return {
                question_id: question.question_id,
                question: question.question,
                question_type: question.question_type,
                responses: questionResponses,
            };
        });

        res.status(200).json({ surveyId, questions: organizedResponses });
    } catch (error) {
        console.error('Error fetching survey responses for logged-in user:', error);
        res.status(500).json({ message: 'Server error', error: error.message });
    }
};

export const getAccomplishedSurveys = async (req, res) => {
    const userEmail = req.session.user?.email;

    if (!userEmail) {
        return res.status(401).json({ message: 'Unauthorized - User session is invalid or email is missing' });
    }

    try {
        const db = await connectToDatabase();

        // Query to get accomplished surveys by the user
        const query = `
            SELECT s.survey_id, s.survey_name, s.academic_year
            FROM surveys s
            JOIN accomplished_surveys asu ON s.survey_id = asu.survey_id
            WHERE asu.user_id = (SELECT user_id FROM users WHERE email = ?)
        `;

        db.query(query, [userEmail], (err, results) => {
            if (err) {
                console.error('Error fetching accomplished surveys:', err);
                return res.status(500).json({ message: 'Database query failed', error: err });
            }

            if (results.length > 0) {
                return res.status(200).json({ surveys: results });
            } else {
                return res.status(404).json({ message: 'No completed surveys found for this user' });
            }
        });
    } catch (error) {
        console.error('Error fetching accomplished surveys:', error);
        res.status(500).json({ message: 'Server error', error: error.message });
    }
};