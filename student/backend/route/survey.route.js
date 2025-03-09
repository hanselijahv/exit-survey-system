import express from 'express';
import { fetchSurveyQuestions, getSurveysByUser, submitSurvey, viewResponses, getAccomplishedSurveys } from '../controller/survey.controller.js';
import { checkLoggedIn } from '../middleware/auth.middleware.js';

const surveyRoute = express.Router();

surveyRoute.get('/', checkLoggedIn, getSurveysByUser);
surveyRoute.get('/:surveyId/questions', checkLoggedIn, fetchSurveyQuestions)
surveyRoute.get('/:surveyId/responses', checkLoggedIn, viewResponses)
surveyRoute.post('/submit', checkLoggedIn, submitSurvey)
surveyRoute.get('/accomplished', getAccomplishedSurveys);

export default surveyRoute;