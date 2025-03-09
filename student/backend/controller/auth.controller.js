import { connectToDatabase } from "../config/db.js";

const MAX_ATTEMPTS = 3;
const LOCK_TIME = 60 * 1000; // 1 minute lock time

export const login = async (req, res) => {
    const { email, password } = req.body;
    const query = 'SELECT * FROM users WHERE email = ?';

    try {
        const db = await connectToDatabase();

        // Check if login attempt record exists for the user
        const attemptsQuery = 'SELECT * FROM login_attempts WHERE email = ?';
        db.query(attemptsQuery, [email], (err, attemptResults) => {
            if (err) {
                return res.status(500).json({ message: 'Database query failed', error: err });
            }

            let attempts = 0;
            let lastAttemptTime = null;

            if (attemptResults.length > 0) {
                attempts = attemptResults[0].attempts;
                lastAttemptTime = new Date(attemptResults[0].last_attempt).getTime();
            }

            // If attempts exceed MAX_ATTEMPTS and the lock time hasn't passed, block login
            if (attempts >= MAX_ATTEMPTS && Date.now() - lastAttemptTime < LOCK_TIME) {
                const remainingTime = Math.floor((LOCK_TIME - (Date.now() - lastAttemptTime)) / 1000); // remaining time in seconds
                return res.status(403).json({
                    message: 'Too many login attempts. Please try again later.',
                    lockoutTimeRemaining: remainingTime
                });
            }

            // Perform the actual login check
            db.query(query, [email], (err, results) => {
                if (err) {
                    return res.status(500).json({ message: 'Database query failed', error: err });
                }

                if (results.length > 0) {
                    const user = results[0];

                    // Check if the user is a student based on user_type
                    if (user.user_type !== 'student') {
                        return res.status(403).json({ message: 'Access denied. Only students can log in.' });
                    }

                    // Directly compare the plain text password (not secure in production)
                    if (user.password === password) {

                        const resetAttemptsQuery = 'UPDATE login_attempts SET attempts = 0 WHERE email = ?';
                        db.query(resetAttemptsQuery, [email], (err) => {
                            if (err) {
                                console.log('Error resetting attempts:', err);
                            }
                        });

                        // Save user data to session
                        req.session.user = { email: user.email, userType: user.user_type };
                        return res.status(200).send({ message: 'Login successful' });
                    } else {
                        // Incorrect password, increment attempts
                        const incrementAttemptsQuery = `INSERT INTO login_attempts (email, attempts, last_attempt)
                                                        VALUES (?, 1, NOW())
                                                        ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt = NOW()`;
                        db.query(incrementAttemptsQuery, [email], (err) => {
                            if (err) {
                                console.log('Error incrementing attempts:', err);
                            }
                        });

                        return res.status(401).json({ message: 'Invalid credentials' });
                    }
                } else {
                    return res.status(404).json({ message: 'User not found' });
                }
            });
        });
    } catch (error) {
        return res.status(500).json({ message: 'Server error', error: error.message });
    }
};

// Logout function
export const logout = async (req, res) => {
    req.session.destroy((err) => {
        if (err) {
            return res.status(500).json({ message: 'Could not log out', error: err });
        }
        res.clearCookie('connect.sid');
        return res.status(200).json({ message: 'Logout successful' });
    });
};

// Get full name of the user
export const getUserFullName = async (req, res) => {
    const email = req.session.user?.email;

    if (!email) {
        return res.status(401).json({ message: 'User is not logged in' });
    }

    const query = 'SELECT first_name, last_name FROM users WHERE email = ?';

    try {
        const db = await connectToDatabase();

        db.query(query, [email], (err, results) => {
            if (err) {
                console.log('Error:', err);
                return res.status(500).json({ message: 'Database query failed', error: err });
            }

            if (results.length > 0) {
                const user = results[0];
                const fullName = `${user.first_name} ${user.last_name}`;
                return res.status(200).json({ fullName });
            } else {
                return res.status(404).json({ message: 'User not found' });
            }
        });
    } catch (error) {
        console.log('Server Error:', error);
        return res.status(500).json({ message: 'Server error', error: error.message });
    }
};

