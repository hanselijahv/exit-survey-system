import express from "express";
import { getUserFullName, login, logout } from "../controller/auth.controller.js";

const authRoute = express.Router();

authRoute.post("/login", login);
authRoute.post('/logout', logout);
authRoute.get('/getName', getUserFullName);

export default authRoute;