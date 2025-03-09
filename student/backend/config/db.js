import mysql from "mysql2";

let connection;

export const connectToDatabase = async () => {
    if (!connection) {
        try {
            connection = await mysql.createConnection({
                host: process.env.MYSQL_HOST,
                user: process.env.MYSQL_USER,
                password: process.env.MYSQL_PASSWORD,
                database: process.env.DATABASE_NAME,
            });
            console.log(`MySQL Connected: ${connection.config.host}`);
        } catch (error) {
            console.error(`Error: ${error.message}`);
            process.exit(1);
        }
    }
    return connection;
};

export const closeDatabaseConnection = async () => {
    if (connection) {
        try {
            await connection.end();
            console.log("MySQL connection closed.");
        } catch (error) {
            console.error("Error closing MySQL connection:", error);
        }
    }
};
