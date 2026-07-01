<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



# 🚀 Task Management System

A modern **Task Management System** built with **Laravel 12** to streamline project planning, task assignment, team collaboration, and progress tracking.

This application provides role-based access, project management, Kanban workflow, meeting minutes, activity logging, and daily stand-up reporting in a centralized dashboard.

---

# 📌 Features

### 👤 Authentication & User Management
- Secure Login & Logout
- Profile Management
- User CRUD
- Role-Based Access Control (RBAC)
- Permission Management

### 📁 Project Management
- Create, Update & Delete Projects
- Project Categories
- Project Manager Assignment
- Team Member Assignment
- Project Status Tracking
- Project Priority
- Budget Management

### ✅ Task Management
- Create & Assign Tasks
- Task Categories
- Task Priorities
- Due Dates
- Task Status Tracking
- Task Filtering & Search

### 📋 Kanban Board
- Drag-and-drop style workflow
- Pending Tasks
- In Progress Tasks
- Completed Tasks

### 📝 Meeting Minutes (MOM)
- Create Meeting Minutes
- Store Decisions
- Store Action Items
- Track Attendees
- Link Meetings with Projects

### 📅 Daily Stand-up Reports
- Daily Progress Updates
- Yesterday's Work
- Today's Plan
- Blockers Tracking

### 📊 Dashboard
- Total Projects
- Total Tasks
- Active Projects
- Completed Projects
- Statistics Cards

### 📜 Activity Log
- Project Activity Tracking
- User Actions
- Status Changes
- History Management

---

# 🛠 Tech Stack

| Technology | Version |
|------------|----------|
| Laravel | 12 |
| PHP | 8.2 |
| MySQL | Database |
| Bootstrap | UI Framework |
| AdminLTE | Admin Dashboard |
| HTML5 | Frontend |
| CSS3 | Styling |
| JavaScript | Client-side |
| Blade | Laravel Templating |

---

# 📂 Project Modules

- Authentication
- Dashboard
- Users
- Projects
- Project Categories
- Tasks
- Task Categories
- Kanban Board
- Meeting Minutes
- Daily Stand-up Reports
- Activity Logs
- Profile Management

---

# 🗄 Database Relationships

- One User → Many Projects
- One Project → Many Tasks
- One Project → Many Meeting Minutes
- One Category → Many Projects
- One Task Category → Many Tasks
- Many Users ↔ Many Projects (Team Members)

---

# 🔄 Application Flow

```
Login
      │
      ▼
Dashboard
      │
      ▼
Create Project
      │
      ▼
Assign Team Members
      │
      ▼
Create Tasks
      │
      ▼
Assign Tasks
      │
      ▼
Update Task Status
      │
      ▼
Kanban Board
      │
      ▼
Meeting Minutes
      │
      ▼
Daily Stand-up Reports
      │
      ▼
Activity Logs
```

---

# 🚀 Installation

## Clone Repository

```bash
git clone https://github.com/anam933/task_management_v2.git
```

## Go to Project

```bash
cd task_management_v2
```

## Install Dependencies

```bash
composer install
```

## Create Environment File

```bash
cp .env.example .env
```

## Generate Application Key

```bash
php artisan key:generate
```

## Configure Database

Update your **.env** file:

```env
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=
```

## Run Migrations

```bash
php artisan migrate
```

## Start Development Server

```bash
php artisan serve
```

Application will run at:

```
http://127.0.0.1:8000
```

---

# 📸 Screenshots

> Add screenshots here after deployment.

- Login Page
- Dashboard
- Projects
- Tasks
- Kanban Board
- Meeting Minutes
- User Management

---

# 📈 Future Improvements

- Email Notifications
- File Attachments
- Calendar Integration
- Gantt Chart
- Time Tracking
- Project Reports
- Chat Module
- Mobile Responsive UI
- API Integration

---

# 👩‍💻 Developed By

**Anam Mariya**

Laravel Developer | Full Stack Learner

GitHub:
https://github.com/anam933

---

# ⭐ If you like this project

Please give this repository a ⭐ on GitHub.
