import 'dotenv/config';
import express from "express";
import path from 'path';
import session from 'express-session';
import { checkNotLoggedIn, checkLoggedIn } from './middleware/auth.middleware.js';
import { connectToDatabase, closeDatabaseConnection } from "./config/db.js";
import authRoute from './route/auth.route.js';
import surveyRoute from './route/survey.route.js';

const app = express();
const PORT = process.env.PORT || 5000;
const rootDir = path.resolve();

app.use(express.json());

app.use(session({
    secret: process.env.SESSION_SECRET,
    resave: false,
    saveUninitialized: false,
    cookie: {
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        maxAge: 1000 * 60 * 60 * 24
    }
}));

app.use('/api/auth', authRoute);
app.use('/api/surveys', surveyRoute);

if (process.env.NODE_ENV === "development") {
    app.use(express.static(path.join(rootDir, "frontend", "src")));
    app.use('/public', express.static(path.join(rootDir, "frontend", "public")));
    app.use('/node_modules', express.static(path.join(rootDir, "frontend", "node_modules")));
}

function sendHTML(res, page) {
    res.sendFile(path.join(rootDir, "frontend", "src", "pages", `${page}.html`));
}

app.get("/", checkNotLoggedIn, (req, res) => sendHTML(res, "LoginPage"));
app.get("/surveys", checkLoggedIn, (req, res) => sendHTML(res, "SurveysPage"));
app.get("/history", checkLoggedIn, (req, res) => sendHTML(res, "HistoryPage"));
app.get("/home", checkLoggedIn, (req, res) => sendHTML(res, "HomePage"));

const server = app.listen(PORT, async () => {
    await connectToDatabase();
    console.log(`Server listening on port ${PORT}`);
});

process.on('SIGINT', async () => {
    console.log('SIGINT received. Closing resources.');
    try {
        await closeDatabaseConnection();
        server.close(() => {
            console.log('Server shut down gracefully.');
            process.exit(0);
        });
    } catch (error) {
        console.error('Error during shutdown:', error);
        process.exit(1);
    }
});
