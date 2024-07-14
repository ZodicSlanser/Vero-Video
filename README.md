# Interactive Video Quiz

### This Project has been done as PoC for VERO Advanced Solutions.

This project is a Laravel-based web application that plays a video and prompts the user to answer multiple-choice questions (MCQs) at specific times during the video. The user's answers are then saved for further processing.

# Video Quiz Application

## Features

- **Video Management**: Store and manage video paths.
- **User Management**: Store and manage user information.
- **Quiz Management**: Store and manage quiz questions associated with videos.
- **Interactive Quiz**: Display quiz questions at specific times during video playback.
- **User Answers**: Track and store user answers to quiz questions.

## Technologies Used

- **Backend**: Laravel
- **Frontend**: Vue.js
- **Database**: MySQL (or any other database supported by Laravel)
- **HTTP Client**: Axios

## Setup Instructions

### Prerequisites

- PHP >= 7.4
- Composer
- Node.js & npm
- MySQL (or other database)

### Backend Setup (Laravel)

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ZodicSlanser/video-quiz.git
   cd video-quiz
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Setup environment file:**
   ```bash
   cp .env.example .env
   ```
   Configure your `.env` file with your database credentials.

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Run migrations:**
   ```bash
   php artisan migrate
   ```

6. **Seed the database:**
   ```bash
   php artisan db:seed
   ```

7. **Start the Laravel development server:**
   ```bash
   php artisan serve
   ```

### Frontend Setup (Vue)

1. **Navigate to the frontend directory:**
   ```bash
   cd video-quiz-frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Run the Vue development server:**
   ```bash
   npm run serve
   ```

## Usage

1. **Access the Laravel API:**
   Open your browser and navigate to `http://localhost:8000/api/videos` to access the video data.

2. **Access the Vue frontend:**
   Open your browser and navigate to `http://localhost:8080` to view the interactive video quiz interface.

## API Endpoints

- **Videos:**
  - `GET /api/videos` - Get all videos.
  - `POST /api/videos` - Create a new video.
  - `GET /api/videos/{id}` - Get a specific video and its quiz questions.
  - `PUT /api/videos/{id}` - Update a specific video.
  - `DELETE /api/videos/{id}` - Delete a specific video.

- **Quiz Questions:**
  - `GET /api/quiz-questions` - Get all quiz questions.
  - `POST /api/quiz-questions` - Create a new quiz question.
  - `GET /api/quiz-questions/{id}` - Get a specific quiz question.
  - `PUT /api/quiz-questions/{id}` - Update a specific quiz question.
  - `DELETE /api/quiz-questions/{id}` - Delete a specific quiz question.

- **Users:**
  - `GET /api/users` - Get all users.
  - `POST /api/users` - Create a new user.
  - `GET /api/users/{id}` - Get a specific user.
  - `PUT /api/users/{id}` - Update a specific user.
  - `DELETE /api/users/{id}` - Delete a specific user.

- **User Answers:**
  - `GET /api/user-answers` - Get all user answers.
  - `POST /api/user-answers` - Create a new user answer.
  - `GET /api/user-answers/{id}` - Get a specific user answer.
  - `PUT /api/user-answers/{id}` - Update a specific user answer.
  - `DELETE /api/user-answers/{id}` - Delete a specific user answer.

## Directory Structure

```
video-quiz/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── VideoController.php
│   │   │   ├── QuizQuestionController.php
│   │   │   ├── UserController.php
│   │   │   ├── UserAnswerController.php
│   ├── Models/
│   │   ├── Video.php
│   │   ├── QuizQuestion.php
│   │   ├── User.php
│   │   ├── UserAnswer.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
├── routes/
│   ├── api.php
├── resources/
│   ├── views/
├── .env
├── composer.json
├── artisan
video-quiz-frontend/
├── src/
│   ├── components/
│   │   ├── VideoQuiz.vue
│   ├── App.vue
│   ├── main.js
├── public/
├── package.json
```
