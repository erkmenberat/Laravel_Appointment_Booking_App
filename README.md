# Appointment Booking App

A web-based appointment booking application for hair salons and similar businesses, built with **Laravel 12** and **Tailwind CSS**.

---

## Features

### Customer Side
- Browse available services and select a date
- Fetch available time slots in real time (AJAX)
- Submit appointment requests with contact details

### Admin Panel
- Dashboard with appointment stages: requested, confirmed, completed
- Accept or reject appointment requests
- Edit appointment details and cancel bookings
- Manage admin accounts, business hours, and services

### Availability System
- Slot calculation based on service duration, business hours, and existing appointments
- Pessimistic database locking to prevent double-bookings
- Automatic cancellation of overlapping requests upon confirmation

---

## Tech Stack

| Layer      | Technology                          |
|------------|-------------------------------------|
| Backend    | PHP 8.2+, Laravel 12                |
| Frontend   | Tailwind CSS v4, DaisyUI v5, Vite 7 |
| Database   | MySQL                               |
| Testing    | Pest / PHPUnit                      |
| Build      | Vite, Axios                         |

---

## Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL 5.7+
- Git

---

## Installation

```bash
# 1. Clone the repository
git clone <repository-url>
cd Appointment_Booking_App

# 2. Install PHP dependencies
composer install

# 3. Set up environment file
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=appointment_booking_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

```bash
# 4. Run migrations
php artisan migrate

# 5. Install and build frontend assets
npm install
npm run build

# 6. Start the development server
php artisan serve
npm run dev
```

> **Quick setup:** `composer run setup`

---

## Database Structure

| Table            | Description                                     |
|------------------|-------------------------------------------------|
| `users`          | Admin/staff accounts (role, active status)      |
| `customers`      | Customer data (name, phone, email)              |
| `services`       | Offered services (duration in minutes, price)   |
| `appointments`   | Appointments with full status lifecycle         |
| `business_hours` | Business hours per weekday                      |
| `time_off`       | Staff absence/time-off periods                  |
| `notifications`  | Notification dispatch log                       |
| `sessions`       | Database-backed Laravel sessions                |

**Appointment Status Lifecycle:**

```
requested --> confirmed --> completed
          \             \
           cancelled     cancelled
```

---

## Routes

| Method    | Route                                       | Description                          |
|-----------|---------------------------------------------|--------------------------------------|
| GET       | `/`                                         | Landing page                         |
| GET/POST  | `/login`                                    | Admin login                          |
| POST      | `/logout`                                   | Logout                               |
| GET/POST  | `/customer`                                 | Customer booking form                |
| GET       | `/availability`                             | Available time slots (JSON/AJAX)     |
| GET/POST  | `/register`                                 | Register new admin account           |
| GET       | `/dashboard`                                | Admin dashboard                      |
| GET/PUT   | `/dashboard/appointments/{id}`              | Edit appointment                     |
| POST      | `/dashboard/appointments/{id}/{action}`     | Accept / reject / cancel appointment |

---

## Security

- Admin routes protected by `auth` + `admin` middleware
- `EnsureUserIsAdmin` middleware checks role and active status
- New admins can only be registered by existing logged-in admins
- CSRF protection on all POST routes
- Transaction-based database operations

---

## Project Structure

```
Appointment_Booking_App/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # AppointmentController, CustomerController, etc.
│   │   └── Middleware/         # EnsureUserIsAdmin
│   └── Models/                 # Appointment, Customer, Service, User, ...
├── database/
│   └── migrations/             # All database migrations
├── resources/
│   ├── css/                    # Tailwind / custom styles
│   ├── js/                     # Axios, frontend logic
│   └── views/                  # Blade templates
├── routes/
│   └── web.php                 # All routes
├── public/                     # Public assets
└── vite.config.js              # Frontend build configuration
```

---

## License

This is a private development project and is not released for public use or redistribution.
