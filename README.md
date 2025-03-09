# Exit Survey Program

## Description
The Exit Survey Program is designed for graduates to provide feedback by answering a set of questions. The survey collects responses to analyze graduates' experiences and improve future programs. The system consists of a student interface where graduates can complete the survey and an admin panel for managing survey responses.

## Database Setup
Ensure the provided SQL file is imported into MySQL before running the application. You can do this by logging into MySQL and running:

```sql
mysql -u root -p
CREATE DATABASE exit_survey;
USE exit_survey;
SOURCE path/to/your/sqlfile.sql;
```
## Running the Application

### Student Side
To start the student-side application, navigate to the student directory and run:
```
cd student
npm start
```
The student interface will be available at http://localhost:5000/.

### Admin Side
To start the admin-side application, navigate to the admin directory and run:
```
cd admin
php -S localhost:5050
```
The admin panel will be accessible at http://localhost:5050/.



