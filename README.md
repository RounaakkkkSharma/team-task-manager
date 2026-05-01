# Team Task Manager

A full-stack Laravel app for managing team projects, assigning tasks, and tracking delivery progress with Admin and Member access.

## Features

- Signup, login, logout, profile, and password flows through Laravel Breeze
- Admin and Member roles
- Admin project management with team assignment
- Task creation, assignment, priority, due date, and status tracking
- Member dashboard for assigned work and status updates
- Dashboard metrics for projects, total tasks, status counts, and overdue tasks
- SQL database relationships: users, projects, project members, and tasks
- REST-style Laravel routes with validation and access checks
- Session-authenticated JSON endpoints under `/api/projects` and `/api/tasks/{task}`
- Railway-ready deployment files

## Tech Stack

- Laravel 12
- Blade + Tailwind CSS
- MySQL locally, PostgreSQL or MySQL on Railway
- Vite

## Local Setup

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
mysql -u root -e "CREATE DATABASE IF NOT EXISTS team_task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --seed
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`.

Demo accounts after seeding:

- Admin: `admin@example.com` / `password`
- Member: `member@example.com` / `password`

`admin@example.com` is the only admin account. The signup page is enabled for new users, and every new signup is assigned the Member role automatically.

## Role Rules

- `admin@example.com` is the admin user.
- Every other user is a member.
- Admins can create, update, and delete projects.
- Admins can add team members to projects.
- Admins can create, assign, edit, and delete tasks.
- Members can view projects they belong to.
- Members can update the status of tasks assigned to them.

## Railway Deployment

1. Push this project to GitHub.
2. Create a new Railway project from the GitHub repo.
3. Add a MySQL database service in Railway.
4. Set these app variables from the MySQL service:

```env
APP_NAME="Team Task Manager"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

5. Generate `APP_KEY` locally with `php artisan key:generate --show` and paste it into Railway.
6. Deploy. `railway.toml` runs migrations automatically before starting the app.

## Submission Checklist

- Live Railway URL
- GitHub repo URL
- README
- 2-5 minute demo video showing signup/login, admin project creation, team assignment, task assignment, member status updates, and dashboard progress
