# Vehicle Inspection System (VIS)

A comprehensive Laravel-based system for managing vehicle inspections with customizable templates, scoring, grading, PDF report generation, and full audit logging.

## Features

- **Dashboard** — Real-time statistics, grade distribution charts, monthly trends, and quick actions
- **Vehicle Management** — CRUD for vehicles with plate numbers, VIN, make/model/year tracking
- **Inspection Templates** — Customizable templates with sections and weighted questions, critical failure detection
- **Inspection Workflow** — Create → Assign Inspector → Conduct → Score → Grade → Generate Report
- **Scoring & Grading** — Weighted scoring system with 4 grade levels (Excellent, Good, Needs Attention, Critical)
- **PDF Reports** — Professional inspection reports with DomPDF, downloadable and streamable
- **User Management** — Role-based access (Admin, Inspector, Viewer) via Spatie Permissions
- **Audit Logging** — Full activity tracking with old/new values, IP address, and user agent

## Architecture

The project follows **Clean Architecture** principles:

```
app/
├── Domain/           # Business logic core
│   ├── Models/       # Eloquent models (User, Vehicle, Inspection, etc.)
│   ├── Enums/        # InspectionStatus, InspectionGrade, QuestionType
│   ├── DTOs/         # Data Transfer Objects
│   └── Services/     # ScoringService
├── Application/      # Use cases / Application services
│   └── Services/     # InspectionService, VehicleService, TemplateService, etc.
├── Infrastructure/   # External interfaces
│   └── Repositories/ # BaseRepository, InspectionRepository, VehicleRepository
└── Http/             # Presentation layer
    ├── Controllers/  # Organized by domain (Auth/, Dashboard/, Vehicle/, etc.)
    ├── Requests/     # Form validation (VehicleRequest, UserRequest, etc.)
    └── Policies/     # Authorization (InspectionPolicy)
```

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+ or PostgreSQL
- Node.js & npm (optional, for asset compilation)

## Installation

```bash
# Clone the repository
git clone <repo-url> vis
cd vis

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=vis
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed roles, permissions, and demo data
php artisan db:seed

# Create storage symlink for file uploads
php artisan storage:link

# Start the development server
php artisan serve
```

## Default Users (after seeding)

| Email | Password | Role |
|-------|----------|------|
| admin@vis.test | password | Admin |
| inspector@vis.test | password | Inspector |

## Configuration

Application-specific configuration is in `config/vis.php`:

- Scoring thresholds for grade levels
- Upload limits and allowed file types
- Inspection workflow settings

## Key Routes

| Route | Description |
|-------|-------------|
| `/login` | Authentication |
| `/dashboard` | Main dashboard with statistics |
| `/vehicles` | Vehicle management (CRUD) |
| `/inspections` | Inspection management |
| `/inspections/{id}/conduct` | Conduct an inspection |
| `/templates` | Inspection templates |
| `/templates/{id}/sections` | Manage template sections |
| `/users` | User management (Admin only) |
| `/audit-logs` | System audit logs (Admin only) |
| `/reports/{id}/pdf` | Download inspection PDF |
| `/reports/{id}/view` | View inspection PDF in browser |

## Inspection Grading

| Grade | Score Range | Description |
|-------|-----------|-------------|
| Excellent | 90%+ | Vehicle passes with flying colors |
| Good | 75–89% | Vehicle passes inspection |
| Needs Attention | 50–74% | Issues found, follow-up needed |
| Critical | < 50% or Critical Failure | Immediate action required |

**Critical Failures:** Questions marked as "critical" that receive a failing score will automatically flag the entire inspection as a critical failure, regardless of the overall percentage.

## Tech Stack

- **Framework:** Laravel 11
- **Database:** MySQL/PostgreSQL with UUID primary keys
- **Authentication:** Laravel built-in auth
- **Authorization:** Spatie Laravel-Permission
- **PDF Generation:** barryvdh/laravel-dompdf
- **Frontend:** Blade templates with custom CSS (no build step required)
- **Soft Deletes:** Enabled on all major models

## File Structure

```
resources/views/
├── layouts/app.blade.php      # Main layout with sidebar
├── auth/login.blade.php       # Login page
├── dashboard/index.blade.php  # Dashboard with stats
├── vehicles/                  # Vehicle CRUD views
├── inspections/               # Inspection views + conduct form
├── templates/                 # Template management views
├── users/                     # User management views
├── audit-logs/index.blade.php # Audit log viewer
└── reports/inspection-pdf.blade.php  # PDF report template
```

## License

Proprietary — All rights reserved.
