# SurveySystem - CodeIgniter 4

Anonymous Survey System using CodeIgniter 4 Framework

## 📌 Project Overview

This project is an **Anonymous Survey System** built using **CodeIgniter 4**.  
Admins can upload a CSV file to generate web-based questionnaires. Each questionnaire gets a unique URL. Admins can turn URLs on/off, view results, and download them. Users can access and answer surveys without any login.

## ⚙️ Technologies Used
- PHP
- CodeIgniter 4 Framework
- MySQL
- HTML, CSS, JavaScript

## Installation

1. Download CodeIgniter 4  
Download the framework from the official site:  
https://codeigniter.com/download

2. Extract to your local server directory  
For XAMPP, for example:  
- C:\xampp\htdocs\ci4-survey

3. Create the some fil in controllers,models,views,filters
4. Create the database and tables  
Open phpMyAdmin and run the database.sql file provided in the project.  
This will create the survey_db database with all required tables and a default admin account.

5. Configure database in .env and set your database settings:
- database.default.hostname = localhost
- database.default.database = survey_db
- database.default.username = root
- database.default.password = your_password
- database.default.DBDriver = MySQLi
- database.default.DBPrefix =
- database.default.port = 3306

And change CI_ENVIRONMENT to development:
- CI_ENVIRONMENT = development

Also set your base URL:
- app.baseURL = 'http://localhost:8080/'

CodeIgniter automatically uses these .env settings for database connections.

6. Run the project using PHP Spark  
Open your terminal in your project folder and run:
- php spark serve

You should see:
- CodeIgniter development server started on http://localhost:8080

Now open your browser and access the project via:
- http://localhost:8080

## Default Admin Account

| Field    | Value    |
|----------|----------|
| Username | admin    |
| Password | admin123 |

> **Important:** Change the password after first login.

## Database Structure

```
admins          — Admin login accounts
surveys         — Each uploaded CSV becomes one survey
questions       — Each CSV row becomes one question
options         — Correct and wrong answers for each question
responses       — One row per user submission (answers stored as JSON)
```

## Models

### AdminModel (app/Models/AdminModel.php)
The model interacts with the admins table:

```php
protected $table         = 'admins';
protected $primaryKey    = 'id';
protected $allowedFields = ['username', 'password'];
```

Key points:
- $table specifies the database table (admins)
- $primaryKey is id
- $allowedFields defines which fields can be inserted or updated
- findByUsername() finds an admin by their username

---

### SurveyModel (app/Models/SurveyModel.php)
The model interacts with the surveys table:

```php
protected $table         = 'surveys';
protected $primaryKey    = 'id';
protected $allowedFields = ['topic_name', 'slug', 'status'];
```

Key points:
- findBySlug() finds a survey by its unique URL slug
- getAllSurveys() returns all surveys newest first
- toggleStatus() switches a survey between active and inactive
- generateSlug() creates a unique URL-safe slug from the topic name
- countResponses() and countQuestions() return counts for the dashboard

---

### QuestionModel (app/Models/QuestionModel.php)
The model interacts with the questions and options tables:

```php
protected $table         = 'questions';
protected $primaryKey    = 'id';
protected $allowedFields = ['survey_id', 'question'];
```

Key points:
- insertWithOptions() saves a question and all its answer options at once
- getWithOptions() returns questions with shuffled options for the survey page
- getWithOptionsOrdered() returns questions with options in order for the results page

---

### ResponseModel (app/Models/ResponseModel.php)
The model interacts with the responses table:

```php
protected $table         = 'responses';
protected $primaryKey    = 'id';
protected $allowedFields = ['survey_id', 'answers', 'submitted_at'];
```

Key points:
- saveResponse() saves the full set of answers as a JSON string
- getBySurvey() returns all responses for a survey with answers decoded

## Controllers

### AuthController (app/Controllers/Admin/AuthController.php)
Handles admin login and logout. Main functions:

1. loginForm() — Show the login form
2. login() — Validate credentials, verify password hash, set session
3. logout() — Destroy session and redirect to login

---

### DashboardController (app/Controllers/Admin/DashboardController.php)
Handles all survey management. Main functions:

1. index() — Show all surveys with question and response counts
2. upload() — Handle CSV upload, parse questions, save to database
3. toggle() — Switch survey status between active and inactive
4. delete() — Permanently delete a survey and all its data

---

### ResultsController (app/Controllers/Admin/ResultsController.php)
Handles viewing and downloading results. Main functions:

1. index() — Show per-question statistics and individual responses
2. download() — Stream results as a downloadable CSV file

---

### SurveyController (app/Controllers/SurveyController.php)
Handles the public user-facing survey. Main functions:

1. take() — Show the questionnaire with shuffled answer options
2. submit() — Save the user's answers anonymously to the database
3. thankyou() — Show the thank you page after submission

## Routes

Routes for all operations:

```php
// Public user routes
$routes->get('survey/(:segment)',          'SurveyController::take/$1');
$routes->post('survey/(:segment)/submit',  'SurveyController::submit/$1');
$routes->get('survey/(:segment)/thankyou', 'SurveyController::thankyou/$1');

// Admin auth routes
$routes->get('admin/login',  'Admin\AuthController::loginForm');
$routes->post('admin/login', 'Admin\AuthController::login');
$routes->get('admin/logout', 'Admin\AuthController::logout');

// Admin protected routes
$routes->group('admin', ['filter' => 'adminauth'], function ($routes) {
    $routes->get('dashboard',                       'Admin\DashboardController::index');
    $routes->post('surveys/upload',                 'Admin\DashboardController::upload');
    $routes->post('surveys/toggle/(:num)',          'Admin\DashboardController::toggle/$1');
    $routes->get('surveys/delete/(:num)',           'Admin\DashboardController::delete/$1');
    $routes->get('surveys/(:num)/results',          'Admin\ResultsController::index/$1');
    $routes->get('surveys/(:num)/results/download', 'Admin\ResultsController::download/$1');
});
```

## CSV Format

Each row of the CSV includes: Question, CorrectAnswer, WrongOptions (at least 1, can be more)

```
Question, CorrectAnswer, WrongOption1, WrongOption2, WrongOption3
```

Example:
```
Question,CorrectAnswer,WrongOption1,WrongOption2,WrongOption3
CPU stands for Central Processing Unit.,TRUE,FALSE,,
Which data structure follows LIFO?,Stack,Queue,Array,Linked List
What does CSS stand for?,Cascading Style Sheets,Creative Style System,Computer Style Sheets,
```

Rules:
- First row is the header and is skipped automatically
- Minimum 3 columns per row (Question + CorrectAnswer + at least 1 WrongOption)
- Extra wrong option columns are optional
- Blank cells in wrong option columns are ignored

## Testing the System

### Admin Side

1. Admin Login
- URL: http://localhost:8080/admin/login
- Username: your username
- Password: your password

2. Admin Dashboard
- URL: http://localhost:8080/admin/dashboard
- Upload a CSV file with a topic name to create a new survey

3. View Results
- URL: http://localhost:8080/admin/surveys/1/results
- Shows per-question breakdown and individual responses

4. Download Results
- URL: http://localhost:8080/admin/surveys/1/results/download
- Downloads a CSV file with all responses and scores

5. Toggle Survey ON/OFF
- Click the Active/Inactive button on the dashboard
- Active = users can access and submit the survey
- Inactive = users see a Survey Closed page

6. Delete Survey
- Click Delete on the dashboard
- Permanently removes the survey and all its data

---

### User Side

1. Take Survey
- URL: http://localhost:8080/survey/YOUR-SLUG-HERE
- No login required
- Answer all questions and click Submit

2. Thank You Page
- URL: http://localhost:8080/survey/YOUR-SLUG-HERE/thankyou
- Shown automatically after submission

3. Survey Closed Page
- Shown when admin has set the survey to Inactive
