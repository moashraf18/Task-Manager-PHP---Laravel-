# ✅ Task Manager — Laravel Edition

A full-stack **Task Manager Single Page Application** rebuilt using the **Laravel** framework. This is the Laravel-powered version of the original [Web-Project](https://github.com/moashraf18/Web-Project), which was built with vanilla PHP. It preserves all the original features — CRUD task management, priority & status filtering, search, and ZenQuotes API integration — while replacing the raw PHP backend with Laravel's structured MVC architecture, Eloquent ORM, and Blade templating engine.

> **IS333 Web-Based Information Systems – Spring 2026**
> Faculty of Computers and Artificial Intelligence, Cairo University

🔗 **Live Demo (original version):** [taskorganizer.infinityfreeapp.com](https://taskorganizer.infinityfreeapp.com/Web-Project-main/index.php)

---

## 🔗 Project Versions

| Version | Stack | Repo |
|---------|-------|------|
| v1 — Vanilla PHP | PHP + MySQL + Vanilla JS + CSS | [Web-Project](https://github.com/moashraf18/Web-Project) |
| v2 — Laravel | Laravel + Blade + Eloquent + Vite | ← You are here |

---

## ✨ Features

- **Task CRUD** — Create, read, update, and delete tasks without page reloads
- **Priority Filtering** — Filter tasks by Low / Medium / High priority
- **Status Filtering** — Filter by Completed / Pending
- **Search** — Find tasks by title or description
- **Mark Complete / Pending** — Toggle task status in one click
- **ZenQuotes API** — Daily motivational quotes with a refresh button
- **Blade Templating** — Clean server-side rendered views via Laravel's Blade engine
- **Eloquent ORM** — Structured database interactions replacing raw SQL queries
- **Database Migrations** — Full schema version control via Laravel migrations
- **Vite Asset Bundling** — Modern frontend asset pipeline

---

## 🛠️ Tech Stack

| Layer | v1 (Original) | v2 (This repo — Laravel) |
|-------|--------------|--------------------------|
| Framework | None (raw PHP) | Laravel |
| Templating | Plain `.php` files | Blade |
| Backend Logic | `DB_Ops.php`, `API_Ops.php` | Controllers + Eloquent ORM |
| Database | Raw SQL + phpMyAdmin | Laravel Migrations |
| Frontend | Vanilla JS + CSS | JS + CSS via Vite |
| API | ZenQuotes (fetch in JS) | ZenQuotes (via Laravel HTTP client) |
| Assets | Static files | Compiled via Vite |

---

## 🗄️ Database Schema

```
tasks
─────────────────────────────────
id          INT, PK, AUTO_INCREMENT
title       VARCHAR
description TEXT
priority    ENUM (low, medium, high)
status      ENUM (pending, completed)
due_date    DATE
created_at  TIMESTAMP
updated_at  TIMESTAMP
```

---

## 📁 Project Structure

```
Task-Manager-PHP---Laravel-/
├── app/
│   ├── Http/
│   │   └── Controllers/        # Task controller (CRUD + filter + search)
│   └── Models/                 # Task Eloquent model
├── database/
│   └── migrations/             # tasks table migration
├── resources/
│   ├── views/                  # Blade templates (index, create, edit...)
│   ├── css/                    # Stylesheets
│   └── js/                     # JavaScript (AJAX / Fetch API calls)
├── routes/
│   └── web.php                 # RESTful resource routes
├── public/                     # Entry point + compiled assets
├── config/                     # App configuration
├── storage/                    # Logs, cache
├── tests/                      # Feature and unit tests
├── .env.example                # Environment template
├── artisan                     # Laravel CLI
├── composer.json               # PHP dependencies
├── package.json                # Node.js dependencies
└── vite.config.js              # Vite build config
```

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- Node.js & npm
- MySQL

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/moashraf18/Task-Manager-PHP---Laravel-.git
   cd Task-Manager-PHP---Laravel-
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Set up the environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your database** in `.env`
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_manager
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets**
   ```bash
   npm run dev
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. Open `http://127.0.0.1:8000` in your browser.

---

## ⚙️ Useful Artisan Commands

```bash
# Reset and re-run all migrations
php artisan migrate:fresh

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run tests
php artisan test
```

---

## 🔐 Key Environment Variables

| Variable | Description |
|----------|-------------|
| `APP_KEY` | Auto-generated encryption key |
| `APP_ENV` | `local` for dev, `production` for live |
| `APP_DEBUG` | Set to `false` in production |
| `DB_*` | Database connection settings |

---

## 👥 Team Members

| Name | GitHub |
|------|--------|
| Mohamed Ashraf | [@moashraf18](https://github.com/moashraf18) |
| Moamen Wael | [@MoamenWael04](https://github.com/MoamenWael04) |
| Mohamed Ashraf | [@mohamedashraf2004mmm](https://github.com/mohamedashraf2004mmm) |
| Mariam Hesham | [@mariam0905](https://github.com/mariam0905) |
| Haneen Ayman | [@Haneen30605](https://github.com/Haneen30605) |
| Habiba Hany | [@habiba25-4](https://github.com/habiba25-4) |
| Nada Atef | [@nadarefai](https://github.com/nadarefai) |
| Aseel Mohamed | [@aseelAlqadhi](https://github.com/aseelAlqadhi) |
