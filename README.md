# H&I Equality — Student Tracking

*(repository / folder name: `hms_student_tracking`)*

**A Laravel admin panel for a computer training institute** — manage courses, teachers, student admissions, fee payments and expenses from one dashboard.

Built for **H&I Equality** (Pakistan). The interface is in English, the currency is **PKR (Rs)**, and the student form collects a **NIC** (national identity card number), so it is tailored to a Pakistani training-centre workflow.

> The brand name is driven entirely by the `BRAND_NAME` key in `.env` — see [Branding](#19-branding).

---

## Table of contents

1. [What this project is for](#1-what-this-project-is-for)
2. [Why it is useful](#2-why-it-is-useful)
3. [Features](#3-features)
4. [Tech stack](#4-tech-stack)
5. [Requirements](#5-requirements)
6. [Installation (step by step)](#6-installation-step-by-step)
7. [Seeders — read this before you start](#7-seeders--read-this-before-you-start)
8. [Default login](#8-default-login)
9. [How to use the app](#9-how-to-use-the-app)
10. [Database schema](#10-database-schema)
11. [Routes reference](#11-routes-reference)
12. [Project structure](#12-project-structure)
13. [How it works internally](#13-how-it-works-internally)
14. [Known issues and caveats](#14-known-issues-and-caveats)
15. [Troubleshooting](#15-troubleshooting)
16. [Making it production-ready](#16-making-it-production-ready)
17. [Running tests](#17-running-tests)
18. [FAQ](#18-faq)
19. [Branding](#19-branding)

---

## Branding

The brand name is **not hard-coded anywhere**. It lives in a single environment key.

### Step 1 — Set the value in `.env`

```dotenv
BRAND_NAME="H&I Equality"
```

The same key is already present in `.env.example` for fresh installs.

### Step 2 — Clear the caches

```bash
php artisan optimize:clear
```

### What it controls

| Location | Rendered as |
| --- | --- |
| `resources/views/layout/layout.blade.php` — `<title>` | `H&I Equality \| Student Tracker` |
| `resources/views/layout/layout.blade.php` — footer | `© 2026 H&I Equality` |
| `resources/views/layout/components/sidebar.blade.php` — logo | `H&I Equality` |
| `resources/views/login.blade.php` — `<title>` and card heading | `H&I Equality` |
| `app/Http/Controllers/UserController.php` — login greeting | `{"success":true,"message":"Welcome to H&I Equality"}` |

Change the value once and every one of these updates. The footer year is generated with `date('Y')`, so it never goes stale.

### ⚠️ Always read it via `config()`, never `env()`

`config/app.php` exposes it as:

```php
'brand_name' => env('BRAND_NAME', 'H&I Equality'),
```

Use it like this:

```blade
{{ config('app.brand_name') }}          {{-- correct --}}
{{ env('BRAND_NAME') }}                {{-- WRONG: returns null when config is cached --}}
```

**Why this matters:** once you run `php artisan config:cache` (or deploy with cached config), Laravel stops loading `.env` entirely. Direct `env()` calls then return `null` and your brand silently disappears from the page. `config()` reads from the cached file and always works.

Verified: with the config cached, `config('app.brand_name')` returns `H&I Equality` while `env('BRAND_NAME')` returns `NULL`.

### Note on the `&`

The value contains an ampersand. Blade's `{{ }}` escapes it to `&amp;` in the HTML source, which every browser renders as `&` — this is correct and safe. If you ever need the **raw** value inside a JavaScript string, use `{!! config('app.brand_name') !!}` and make sure the value is trusted.

---

## 1. What this project is for

A training institute or coaching centre that sells **courses** to **students** needs to answer five questions every single day:

1. **Who is enrolled?** — admissions data (name, CNIC, phone, city, class type).
2. **What are we teaching and who teaches it?** — a course catalogue with prices, grouped into categories, plus a teacher roster with salaries.
3. **Who has actually paid?** — per-student fee collection with paid / pending balance.
4. **How are the students progressing?** — a lifecycle status: `pending` → `confirmed` → `intership` (internship).
5. **Where is the money going?** — an expense log feeding a month-by-month breakdown.

This project is a single-purpose **back-office dashboard** that answers all five. It has no public-facing website — every user logs in and works inside the admin panel.

### The core data flow

```
Category  ──<  Course  ──<  Student  ──<  Payment
   │                    │          │
   └──<  Teacher  >─────┘          └──>  Expense (institute-wide)
```

* A **Category** groups courses (e.g. *Web Development*).
* A **Course** belongs to a category and carries the **price** that everything else is valued against.
* A **Teacher** belongs to a category and has a **salary**.
* A **Student** is enrolled in a course, optionally assigned to a teacher, and carries a **status** and a **class type** (`Online` / `Physical`).
* A **Payment** is one instalment against a student. Course price minus the sum of payments = **pending balance**.
* An **Expense** is institute-wide and is aggregated by month for the dashboard.

---

## 2. Why it is useful

| Pain point | How this solves it |
| --- | --- |
| Admission registers are lost or water-damaged | Every student record is in a database — searchable by name, filterable, editable. |
| "How many students are in each course?" | Auto-generated. The sidebar loads the course list via AJAX and gives you a one-click drill-down. |
| "Who still owes fees?" | The payment page shows Total / Paid / Pending live, and caps the input box at the outstanding amount. |
| Students stuck in one flat list | The status field splits them into **Pending** (new admissions), **Confirmed** (active coaching) and **Intern** (finished / on internship), each with its own view and its own revenue total. |
| No idea where the money goes | Expenses are logged with a description and charted month-by-month for the current year. |
| Calculating "how much is my institute worth" | The dashboard sums the price of the course for every enrolled student, per status — an instant pipeline/revenue figure. |
| Teachers on spreadsheets | Teacher roster with category, experience, contact and monthly salary. |

**Who would use this:** the admin/owner of a small training institute, an admissions officer, or a front-desk receptionist. It is a single-tenant internal tool — there is exactly one login and no roles.

---

## 3. Features

### Authentication
* Email + password login, submitted over AJAX, returns JSON and redirects to the dashboard.
* `AuthMiddleware` guards every route except `/` and the login endpoint.
* Unauthenticated requests are bounced back to the login page with a flash message.
* Logout via the profile dropdown in the top bar.

### Dashboard (`/dashboard`)
Four stat cards, each showing a **head count** and a **Net Worth** (the summed course price of everyone in that group):

* Total Students
* Total Pending Students
* Total Confirmed Students
* Total Intern Students

Below them, a **month-by-month expense table for the current year**.

### Category (`/category`)
* Add / edit / delete course categories.
* Active / Unactive status toggle.
* Live name search.

### Teacher (`/teacher`)
* Add / edit / delete teachers.
* Fields: name, experience, phone, email, category, salary, active status.
* Inline add/edit form on the list page (no separate create screen).
* Live name search.

### Courses (`/courses`, `/courses/create`)
* Full CRUD with a dedicated create and edit page.
* Fields: name, description, **price**, duration, languages, category.
* Live name search.

### Admission (`/student`, `/student/create`)
* Full CRUD with dedicated create and edit pages.
* Fields: name, father's name, **NIC**, gender, city, address, phone, email, course, teacher (optional), class type (`Online` / `Physical`), status.
* The default list shows **only `pending`** students — i.e. the admissions inbox.
* Live name search.
* Deleting a course cascades and removes its students (see [Known issues](#14-known-issues-and-caveats)).

### Students → confirmed (`/confirm-student/{courseId}`)
* Sidebar submenu items are generated from the course list at runtime.
* Shows only **confirmed** students of that course.
* Each row has a **Payment** button that opens the fee page for that student.

### Intern (`/intership-student/{courseId}`)
* Same as above but filtered to students whose status is `intership`.

### Payments (`/payment/{studentId}/edit`)
* Header panel showing **Total amount** (course price), **Paid** and **Pending**.
* Add an instalment, edit an existing instalment, or delete one.
* The amount input's `max` is the outstanding balance.
* Per-student paginated payment history.

### Expense (`/expense`)
* Add / edit / delete expenses with a description.
* Fields: expense title, amount, description (e.g. *"repairment of table or chair"*).
* Live name search.
* Feeds the dashboard's monthly expense table.

---

## 4. Tech stack

| Layer | Technology |
| --- | --- |
| Framework | **Laravel 11** (`^11.9`, currently 11.30) |
| Language | **PHP 8.2** (`^8.2`) |
| Database | **MySQL** / MariaDB — **required**, see [note](#mysql-is-mandatory) |
| Auth | Laravel session guard (`users` table), custom `AuthMiddleware` |
| Front-end | **Server-rendered Blade** — no SPA, no React/Vue |
| CSS | **Bootstrap 4** (bundled template in `public/assets/css`) |
| JS | **jQuery 3**, AJAX, **Bootstrap 4** bundle, **SweetAlert2 11** (CDN), FontAwesome (CDN) |
| Admin theme | Pre-built template (sidebar, waves effect, blockUI, jvectormap, Morris) in `public/assets/` |
| Build tooling | Vite + Tailwind — **present but unused** by the app (see [note](#npm-is-optional)) |
| Tests | Pest 3 |

> **MySQL is mandatory.** `DashboardController` uses `whereYear()` and a raw `MONTH(created_at)` aggregate, which do not work on SQLite. Do not switch `DB_CONNECTION` to `sqlite`.

> **npm is optional.** The only file using `@vite` is `resources/views/welcome.blade.php`, which no route renders. Every real page loads its assets from `public/assets/` and CDNs. Skip `npm install` entirely unless you intend to modify `welcome.blade.php`.

---

## 5. Requirements

| Requirement | Version | Notes |
| --- | --- | --- |
| PHP | **8.2 or newer** | The `composer.json` requires `^8.2` |
| Composer | 2.x | |
| MySQL / MariaDB | 5.7+ / 10.3+ | Must be **running** before you migrate |
| Node.js | 18+ | Only if you want to touch `welcome.blade.php` |

**PHP extensions required:** `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`.

On XAMPP/WAMP all of these are enabled by default.

---

## 6. Installation (step by step)

### Step 1 — Start MySQL

The app cannot do anything until MySQL is up. With **XAMPP**: open the Control Panel and **Start MySQL**. Without XAMPP, run `net start MySQL80` or `sudo service mysql start`.

### Step 2 — Install PHP dependencies

```bash
cd hms_student_tracking
composer install
```

### Step 3 — Configure the environment

```bash
cp .env.example .env        # Windows: copy .env.example .env
```

Edit `.env` and point it at your database:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hms_student_tracking
DB_USERNAME=root
DB_PASSWORD=
```

Generate the application key (skip if `APP_KEY` is already filled in):

```bash
php artisan key:generate
```

### Step 4 — Create the database

Either create it in **phpMyAdmin** (New → `hms_student_tracking`, utf8mb4), or just let Laravel prompt you during the next step.

### Step 5 — Run the migrations

```bash
php artisan migrate
```

Laravel creates the `users`, `categories`, `courses`, `teachers`, `students`, `payments` and `expenses` tables.

### Step 6 — ⚠️ Run the required seeder

**This step is mandatory.** See the next section for why. Without it you cannot log in.

```bash
php artisan db:seed
```

### Step 7 — Start the server

```bash
php artisan serve
```

Open **<http://127.0.0.1:8000>** and log in.

> **Do not use `composer run dev` blindly.** That script also starts a queue worker and a log tailer, but this project has **no `jobs` table** (see [Known issues](#14-known-issues-and-caveats)). `php artisan serve` is all you need.

### Optional — Load demo content

```bash
php artisan db:seed --class=DemoDataSeeder
```

Then `php artisan serve` and log in to a fully populated dashboard.

---

## 7. Seeders — read this before you start

### ✅ Yes, a seeder is required to make this project work

This is the single most important setup fact about this project:

* **There is no "Register" page.** Nothing in the UI can create a user account.
* **Login only queries the `users` table** (`UserController::login` → `Auth::attempt`).
* **Every single route is behind `AuthMiddleware`**, including the dashboard.
* Therefore, if the `users` table is empty, **nobody can log in and the entire application is unreachable.** `php artisan migrate` creates an *empty* table.

So the base seeder is not a convenience — it is the only source of a login.

There are **two** seeders. Run the first one always; run the second one only if you want sample content.

| # | Seeder | Command | Required? | What it creates |
| --- | --- | --- | --- | --- |
| 1 | `DatabaseSeeder` | `php artisan db:seed` | **YES — mandatory** | 1 login account |
| 2 | `DemoDataSeeder` | `php artisan db:seed --class=DemoDataSeeder` | No, optional | 4 categories, 8 courses, 6 teachers, 16 students, 9 payments, 8 dated expenses |

### Seeder 1 — `DatabaseSeeder` (the one that makes it start working)

Creates the only account that can log in:

```
Email:    test@example.com
Password: password
```

> **Bug note:** the original `DatabaseSeeder` used `User::factory()`, which inserts an `email_verified_at` column. **The `users` table in this project has no such column**, so `php artisan db:seed` failed with `Column not found: 1054 Unknown column 'email_verified_at'`. This has been fixed — the factory, the model cast and the seeder now all match the real schema.

### Seeder 2 — `DemoDataSeeder` (optional sample content)

Use this if you want to see the dashboard, charts, filters and payment maths with realistic numbers instead of empty tables. It creates:

* **4 categories** — Web Development, Graphic Design, Office & Computer, Digital Marketing
* **8 courses** — with real prices (Rs 8,000 – Rs 45,000), durations and languages
* **6 teachers** — assigned across categories, with salaries
* **16 students** — spread across all three statuses and both class types, Pakistani names, cities and NIC-format numbers, most **partially paid** so the pending-balance maths is visible
* **9 payments** — split into instalments
* **8 expenses** — spread across the months of the current year so the dashboard's month-by-month expense table is populated

#### How to choose

| I want to… | Run this |
| --- | --- |
| Just get the app running on an empty database | `php artisan db:seed` |
| See a fully populated demo / do a client presentation | `php artisan db:seed` **then** `php artisan db:seed --class=DemoDataSeeder` |
| Start completely fresh with demo data | `php artisan migrate:fresh --seed --seeder=DemoDataSeeder` |

> `--seeder` accepts **only one** class name — passing a comma-separated list fails. That is why `DemoDataSeeder` calls `DatabaseSeeder` internally, so `migrate:fresh --seed --seeder=DemoDataSeeder` always creates the login account first and can never leave you locked out.

Both seeders are **safe to run repeatedly** — they match on natural keys (`email`, `name`, `nic`) and update instead of duplicating.

### Useful seeder commands

```bash
# Required seeder only
php artisan db:seed

# Demo content
php artisan db:seed --class=DemoDataSeeder

# Wipe everything and re-run the required seeder
php artisan migrate:fresh --seed

# Wipe everything and re-run everything (login + demo)
php artisan migrate:fresh --seed --seeder=DemoDataSeeder

# Inspect what got created
php artisan tinker
>>> App\Models\User::count()
>>> App\Models\Category::count()
>>> App\Models\Student::where('status','confirmed')->count()
```

### Changing the admin password

The seeded password is `password`, which is fine for local development and unsafe for anything else. To change it:

```bash
php artisan tinker
>>> App\Models\User::where('email','test@example.com')->update(['password' => 'YourNewPassword']);
```

Or add your own admin inside `DatabaseSeeder::run()`.

---

## 8. Default login

| Field | Value |
| --- | --- |
| URL | <http://127.0.0.1:8000> |
| Email | `test@example.com` |
| Password | `password` |

Logging in visits `/` → `POST /login/check`. A successful call returns `{"success":true,"message":"Welcome to H&I Equality"}` and the page redirects to `/dashboard`.

---

## 9. How to use the app

### First-run order (respect the foreign keys)

The schema has real foreign keys, so you **must** create things in this order:

```
1. Category   →   2. Courses   →   3. Teacher   →   4. Student   →   5. Payment / Expense
```

* A course cannot be created before a category exists.
* A teacher cannot be created before a category exists.
* A student cannot be created before a course exists (a teacher is optional).
* Deleting a category **cascades** and deletes its courses, teachers and students.

### Typical daily workflow

1. **Add a course** — `Courses → Courses create`. Set the price; every revenue figure in the app derives from it.
2. **Admit a student** — `Admission → Student create`. Leave the status as `Pending`.
3. The student appears in `Admission → Student list` (which shows pending only).
4. Once the student pays the first instalment, open them and set the status to **`Confirmed`**. They now appear under `Students → <course>` in the sidebar.
5. **Collect fees** — from the confirmed list, click the **Payment** button. Add instalments. The Pending figure updates immediately.
6. **Log expenses** — `Expense`, with a description.
7. **Promote to intern** — edit the student and set the status to `intership`. They move to the `Intern` sidebar list.
8. **Check the dashboard** for head counts, pipeline value and this year's monthly expenses.

### Sidebar map

| Menu | URL | Notes |
| --- | --- | --- |
| Dashboard | `/dashboard` | |
| Category | `/category` | |
| Teacher | `/teacher` | |
| Courses → Courses list | `/courses` | |
| Courses → Courses create | `/courses/create` | |
| Admission → Student list | `/student` | **Pending students only** |
| Admission → Student create | `/student/create` | |
| Students → *(dynamic)* | `/confirm-student/{id}` | Submenu built by AJAX from the course list |
| intern → *(dynamic)* | `/intership-student/{id}` | Same, filtered to interns |
| Expense | `/expense` | |

---

## 10. Database schema

### `users`
| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint | PK |
| `name` | varchar | |
| `email` | varchar | unique |
| `password` | varchar | hashed automatically by the model cast |
| `created_at`, `updated_at` | timestamp | |

> There is **no** `email_verified_at` column — email verification is not used.

### `categories`
| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint | PK |
| `name` | varchar | |
| `status` | boolean | 1 = Active, 0 = Unactive |
| `created_at`, `updated_at` | timestamp | |

### `courses`
| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint | PK |
| `name` | varchar | |
| `description` | varchar | |
| `price` | decimal(10,2) | **Drives all revenue totals** |
| `duration` | varchar | e.g. `"6 Months"` |
| `languages` | varchar | e.g. `"Urdu, English"` |
| `category_id` | FK → `categories.id` | cascade on delete |
| `created_at`, `updated_at` | timestamp | |

### `teachers`
| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint | PK |
| `name` | varchar | |
| `exp` | varchar | Experience, free text |
| `phone` | varchar | |
| `email` | varchar | |
| `category_id` | FK → `categories.id` | cascade on delete |
| `status` | boolean | 1 = Active, 0 = Inactive |
| `salary` | decimal(10,2) | default `1000` |
| `created_at`, `updated_at` | timestamp | |

### `students`
| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint | PK |
| `name` | varchar | |
| `father` | varchar | Father's name |
| `nic` | varchar(max 20) | National identity card number |
| `gender` | boolean | 1 = Male, 0 = Female |
| `city` | varchar | |
| `address` | varchar | |
| `phone` | varchar | |
| `email` | varchar | |
| `course_id` | FK → `courses.id` | cascade on delete, **required** |
| `teacher_id` | FK → `teachers.id` | **nullable**, cascade on delete |
| `class` | boolean | 1 = Online, 0 = Physical |
| `status` | varchar | `pending` / `confirmed` / `intership` |
| `created_at`, `updated_at` | timestamp | |

### `payments`
| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint | PK |
| `student_id` | FK → `students.id` | cascade on delete |
| `amount` | decimal(10,2) | One instalment |
| `created_at`, `updated_at` | timestamp | |

### `expenses`
| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint | PK |
| `expense` | varchar | Title |
| `amount` | decimal(10,2) | |
| `description` | varchar | Added by a later migration |
| `created_at`, `updated_at` | timestamp | Aggregated by `MONTH()` on the dashboard |

---

## 11. Routes reference

All routes below (except the first two) are wrapped in `AuthMiddleware::class`.

### Public

| Method | URI | Name | Action |
| --- | --- | --- | --- |
| GET | `/` | `auth.loginForm` | Redirects to dashboard if logged in, else renders the login form |
| POST | `/login/check` | `auth.login` | Validates credentials, returns JSON |
| GET | `/up` | — | Laravel health check |

### Authenticated

| Method | URI | Name |
| --- | --- | --- |
| GET | `/logout/check` | `auth.logout` |
| GET | `/dashboard` | `dashboard.index` |
| — | `/courses` | `courses.*` (full resource) |
| — | `/category` | `category.*` (full resource) |
| — | `/teacher` | `teacher.*` (full resource) |
| — | `/student` | `student.*` (full resource) |
| — | `/expense` | `expense.*` (full resource) |
| — | `/payment` | `payment.*` (full resource) |
| GET | `/get-category` | `student.category` |
| GET | `/confirm-student/{id}` | `student.conformed` |
| GET | `/intership-student/{id}` | `student.Intership` |
| POST | `/category/filter` | `category.filter` |
| POST | `/teacher/filter` | `teacher.filter` |
| POST | `/course/filter` | `course.filter` |
| POST | `/student/filter` | `student.filter` |
| POST | `/expense/filter` | `expense.filter` |

56 routes in total — inspect them live with `php artisan route:list`.

> `/get-category` is misleadingly named: it returns a list of **courses** (`id`, `name`), not categories. The sidebar uses it to build the dynamic Students and Intern submenus.

---

## 12. Project structure

```
hms_student_tracking/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CategoryController.php        CRUD + filter
│   │   │   ├── ConfirmstudentController.php  confirmed / intern lists, sidebar submenu API
│   │   │   ├── CourseController.php          CRUD + filter
│   │   │   ├── DashboardController.php       stat cards + monthly expense aggregate
│   │   │   ├── ExpenseController.php         CRUD + filter
│   │   │   ├── PaymentController.php         payment page, store/update/destroy
│   │   │   ├── StudentController.php         CRUD + filter
│   │   │   ├── TeacherController.php         CRUD + filter
│   │   │   └── UserController.php            login / logout
│   │   └── Middleware/
│   │       └── AuthMiddleware.php            redirects guests to the login page
│   └── Models/                               Category, Course, Expense, Payment,
│                                             Student, Teacher, User
├── bootstrap/app.php                        Laravel 11 middleware/routing config
├── config/                                   framework config
├── database/
│   ├── factories/UserFactory.php
│   ├── migrations/                           11 migrations
│   └── seeders/
│       ├── DatabaseSeeder.php                ← REQUIRED (login account)
│       └── DemoDataSeeder.php                ← OPTIONAL (demo content)
├── public/
│   ├── assets/                               Bootstrap 4 admin theme (css/js/plugins/pages)
│   └── index.php
├── resources/
│   ├── views/
│   │   ├── layout/layout.blade.php          master layout + global AJAX setup
│   │   ├── layout/components/sidebar.blade.php
│   │   ├── login.blade.php
│   │   ├── dashboard/index.blade.php
│   │   ├── categories/     student/     courses/     teacher/
│   │   ├── expense/        confirm_student/
│   │   └── welcome.blade.php                (unused, the only @vite consumer)
│   ├── css/app.css, js/app.js, js/bootstrap.js
├── routes/web.php                            all routes
├── .env / .env.example
├── composer.json / package.json
└── vite.config.js / tailwind.config.js       present, unused by the app
```

### Naming quirks worth knowing

| Controller | Folder | Why |
| --- | --- | --- |
| `ConfirmstudentController` | `confirm_student/` | no space allowed in a class name |
| Student model | table `students` | migration is named `create_student_table` but the table is plural |
| `intership-student/{id}` | — | "intership" is a typo for "internship", but it is consistent across routes, views and the status enum |

---

## 13. How it works internally

### Request flow

```
Browser
  └─ GET /                     → no session → render login.blade.php
  └─ POST /login/check         → $request->validate()
                               → Auth::attempt(['email','password'])
                               → 200 JSON {success, message}
  └─ page reloads /dashboard   → AuthMiddleware passes (session valid)
                               → DashboardController@index aggregates
                               → dashboard/index.blade.php
```

### AJAX everywhere

The app does **no full-page form posts**. Every Blade page loads `layout.blade.php`, which sets a global `X-CSRF-TOKEN` header:

```js
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
```

From then on, create / update / delete / search are all `$.ajax` calls that hit a resource route and receive JSON, with **SweetAlert2** toasts on success. Validation errors come back as Laravel's standard 422 JSON and are injected next to the offending field.

### Dashboard aggregation

```php
Student::count();                                             // total students
Student::where('status', 'pending')->count();                 // pending
Student::where('status', 'confirmed')->count();               // confirmed
Student::where('status', 'internship')->count();              // interns  ← see Known issues

Student::with('course')->get()->sum('course.price');          // pipeline value

Expense::whereYear('created_at', $currentYear)
    ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
    ->groupBy('month')->orderBy('month')->get();               // monthly expenses
```

Note that the "Net Worth" figure is a **pipeline value** (sum of course prices for enrolled students), **not** collected cash. Collected cash lives in the `payments` table.

### Database relationships

| Model | Relationship |
| --- | --- |
| `Course` | `belongsTo(Category)` |
| `Teacher` | `belongsTo(Category)` |
| `Student` | `belongsTo(Course)`, `belongsTo(Teacher)` |
| `Payment` | *(no defined relation; queried via `student_id`)* |
| `Expense` | standalone |

### Pagination

Every list uses `->paginate(10)` rendered with `pagination::bootstrap-4`.

---

## 14. Known issues and caveats

### ✅ Already fixed in this repo

| Issue | Fix |
| --- | --- |
| `php artisan db:seed` crashed with `Unknown column 'email_verified_at'` — the factory inserted a column the `users` table doesn't have, meaning **the project could never be seeded out of the box**. | Removed `email_verified_at` from `UserFactory` and the `User` model cast. |
| `/student` (and the confirmed/intern lists) returned **HTTP 500** for any student without an assigned teacher — `teacher_id` is nullable, but the views did `$student->teacher->name`. | Now uses `$student->teacher?->name ?? 'Unassigned'` (and the equivalent in the AJAX filter rows). |
| The **Teacher dropdown** on the student create/edit forms listed *course names*, because `StudentController::create/edit` assigned `$teachers = Course::orderBy(...)`. | Now assigns `$teachers = Teacher::orderBy(...)`. |

### ⚠️ Still present — be aware

**1. The "Total Intern Students" card is always 0.**
The dashboard counts `where('status', 'internship')`, but every other part of the app stores and filters on the misspelling **`intership`**. Change line 16 of `DashboardController` to match:

```php
$total_intern_students = Student::where('status', 'intership')->count();
// and for the money figure below it:
$total_sum_internship = Student::where('status', 'intership')->with('course')->get()->sum('course.price');
```

**2. There is no `jobs` table, but `QUEUE_CONNECTION=database`.**
There is no migration creating a `jobs` table and no job is ever dispatched, so nothing breaks — but the `composer run dev` script starts `php artisan queue:listen`, which will error. Use `php artisan serve` instead, or add the jobs table:

```bash
php artisan queue:table
php artisan migrate
```

**3. Payment validation is malformed.**
`PaymentController::store` calls `$request->validate(['student_id','amount'])` — a list of strings instead of `field => rules`, so **no validation actually runs** on payments. Rewrite as:

```php
$request->validate([
    'student_id' => 'required|exists:students,id',
    'amount'     => 'required|numeric|min:1',
]);
```

**4. Deleting a course silently deletes its students** (`cascadeOnDelete` on `students.course_id`), and their payments go too. Add a confirm dialog or `restrictOnDelete` if that is not what you want.

**5. The student list and the student search disagree.**
`/student` shows only `pending` students, but `/student/filter` searches **all** students regardless of status. So results can include rows that are not on the page being searched.

**6. There are no roles or permissions.** Any authenticated user is a full administrator, and there is only one account. There is also no password reset, no user management screen, and no registration.

**7. Dead routes.** `payment.index`, `payment.create`, `category.edit`, `*.show` and `*.edit` for teacher/expense return empty bodies. Nothing in the UI links to them, so they are harmless — but do not link to them.

**8. `resources/views/welcome.blade.php` is dead code.** It is the only file using `@vite`, so it will throw if you ever render it without running `npm run build`.

**9. `class` and `gender` are booleans with inverted-looking semantics** (`1 = Online` for `class`, `0 = Female` for `gender`). Easy to get wrong when querying from the console.

**10. The filters are unescaped `LIKE` searches.** `%` and `_` typed into a search box act as wildcards, and the search is not paginated — a broad term like `a` loads every matching row.

**11. There is essentially no test coverage.** The only test is Laravel's stock `ExampleTest` that asserts `GET /` returns 200.

---

## 15. Troubleshooting

| Symptom | Cause | Fix |
| --- | --- | --- |
| `SQLSTATE[HY000] [2002] ... Connection refused` | MySQL is not running | Start MySQL in XAMPP / `net start MySQL80` |
| `SQLSTATE[HY000] [1049] Unknown database 'hms_student_tracking'` | Database does not exist | Create it in phpMyAdmin, or let `php artisan migrate` prompt you |
| `SQLSTATE[HY000] [1698] Access denied for user 'root'` | Root uses `auth_socket` / a password | Set the real `DB_PASSWORD` in `.env` |
| `Illuminate\Encryption\MissingAppKeyException` | No `APP_KEY` | `php artisan key:generate` |
| Login always says *"Invalid email or password"* | Seeder never ran | `php artisan db:seed` |
| Every page redirects back to `/` | Not logged in, or `SESSION_DRIVER` is broken | Log in again; if it persists run `php artisan optimize:clear` |
| 500 error on `/student` | A student row has no teacher | **Fixed in this repo** — otherwise set `teacher_id` to a valid teacher |
| `Column not found: email_verified_at` | A stale `UserFactory` | **Fixed in this repo** |
| Page loads unstyled | You are rendering `welcome.blade.php` | Log in properly — real pages use `public/assets` |
| Changes to `.env` have no effect | Config cache is stale | `php artisan optimize:clear` |
| 419 / "CSRF token mismatch" | Session expired or cookie blocked | Hard-refresh, clear cookies, confirm `APP_KEY` and `APP_URL` are set |
| Changes to Blade/CSS not showing | Blade view cache | `php artisan view:clear` |
| Deleting a course deleted students | `cascadeOnDelete` FK | Expected behaviour — see [Known issues](#14-known-issues-and-caveats) |

---

## 16. Making it production-ready

This is a solid internal tool, but it was built for a trusted LAN. Before putting it on the internet:

- [ ] **Add real roles** (`admin` / `staff`) with a permission package such as `spatie/laravel-permission`; gate the destructive routes.
- [ ] **Change the seeded password** and add a forced password change on first login.
- [ ] **Add CSRF/rate limiting on login** — `throttle:5,1` on the login route to stop brute force.
- [ ] **Fix payment validation** (see [Known issues](#14-known-issues-and-caveats)).
- [ ] **Fix the `internship` / `intership` mismatch**, then add a `CHECK` constraint or an enum so it cannot recur.
- [ ] **Stop cascading course deletes**, or warn the user first.
- [ ] **Escape the filter inputs** and paginate the filter results.
- [ ] **Add a `NOT NULL` / FK-aware migration for `students.teacher_id`** if every student must have a teacher.
- [ ] Serve over **HTTPS**, set `APP_DEBUG=false`, `APP_ENV=production`, and a real `APP_URL`.
- [ ] `composer install --no-dev --optimize-autoloader` and `npm ci && npm run build`.
- [ ] Add a **backup routine** (`mysqldump` on a schedule).
- [ ] Add **audit logging** for deletes.

---

## 17. Running tests

The project ships with Pest:

```bash
composer install
php artisan test
```

Be aware the only test is the framework default:

```php
it('returns a successful response', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});
```

`.env.example` does not define test-specific credentials. If you add tests that touch the database, add a `phpunit.xml` `<env>` block pointing at a **separate** test database so you never wipe real data.

---

## 18. FAQ

**Q: Do I really need the seeder?**
Yes. There is no registration page, login reads only the `users` table, and every route is authenticated. An empty `users` table makes the app completely unreachable.

**Q: Which seeder do I run?**
Run `php artisan db:seed` to get in. Add `php artisan db:seed --class=DemoDataSeeder` only if you want sample content.

**Q: Can I run a seeder twice?**
Yes. Both use `updateOrCreate` / `firstOrCreate` on natural keys.

**Q: Can I use SQLite to avoid installing MySQL?**
Not without changing `DashboardController`, which uses `whereYear()` and a raw `MONTH(created_at)` aggregate. Both are MySQL-specific. Use MySQL/MariaDB.

**Q: Do I need Node.js / `npm install`?**
No. Only `welcome.blade.php` uses `@vite`, and nothing renders it. Run `npm install && npm run build` only if you edit that file.

**Q: Why does `composer run dev` error?**
It starts a queue worker, but there is no `jobs` table. Use `php artisan serve`.

**Q: Why is the intern count zero on the dashboard?**
The dashboard searches for `internship` while the rest of the app stores `intership`. Fix in `DashboardController`.

**Q: How do I create another admin user?**
```bash
php artisan tinker
>>> App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'secret123']);
```
(The `password` cast hashes it automatically.)

**Q: How do I reset everything and start over?**
```bash
php artisan migrate:fresh --seed
```

**Q: How do I change the brand name?**
Set `BRAND_NAME` in `.env` — it drives the browser tab title, the sidebar logo, the footer, the login screen and the login greeting. Then run `php artisan optimize:clear`. See [Branding](#branding).

**Q: Where are the logs?**
`storage/logs/laravel.log` — check it whenever you get a 500.