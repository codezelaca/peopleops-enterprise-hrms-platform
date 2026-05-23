# PeopleOps Enterprise HRMS Platform

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)
![Vue](https://img.shields.io/badge/Vue-3.x-42B883?logo=vuedotjs&logoColor=white)
![Inertia](https://img.shields.io/badge/Inertia.js-2.x-9553E9?logo=inertia&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?logo=typescript&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?logo=tailwindcss&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Neon-4169E1?logo=postgresql&logoColor=white)
![Cloudflare R2](https://img.shields.io/badge/Storage-Cloudflare_R2-F38020?logo=cloudflare&logoColor=white)
![Redis](https://img.shields.io/badge/Cache%20%26%20Queues-Redis-DC382D?logo=redis&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-Pest%20%2B%20Playwright-7A3EF2)
![Status](https://img.shields.io/badge/Status-Enterprise%20Build-6D28D9)
![Licence](https://img.shields.io/badge/Licence-Proprietary-111827)

**PeopleOps Enterprise HRMS Platform** is a production-grade Human Resource Management and Recruitment Management system for modern organisations.

It is designed to manage the full people operations lifecycle: recruitment, candidate tracking, onboarding, employee records, attendance, leave, payroll preparation, performance, documents, assets, offboarding, approvals, reporting, audit logs and secure self-service portals.

Repository: <https://github.com/codezelaca/peopleops-enterprise-hrms-platform>

---

## Product Scope

PeopleOps is built as a real enterprise system, not a basic CRUD dashboard.

The platform covers:

- Workforce administration
- Recruitment and applicant tracking
- Candidate self-service
- Employee self-service
- Department and manager workflows
- Leave and attendance operations
- Payroll preparation
- Performance management
- Document governance
- Asset allocation
- Offboarding
- HR analytics
- Audit and compliance tracking
- Secure file storage
- Role-based access control
- Real-time notifications

---

## Core Modules

### 1. Authentication and Access Control

- Secure login and logout
- Password reset
- Email verification
- Role-based access control
- Permission-based authorisation
- Protected admin, HR, manager, employee and candidate areas
- Session security
- Activity and access tracking

### 2. Organisation Management

- Company profile
- Branches and office locations
- Departments
- Designations
- Employment types
- Work modes
- Reporting hierarchy
- Manager assignments
- Organisation structure views

### 3. Employee Management

- Employee directory
- Employee profiles
- Personal details
- Employment details
- Contact information
- Emergency contacts
- Salary profile access control
- Work history
- Probation tracking
- Contract status
- Document records
- Profile completion tracking
- Employee lifecycle timeline

### 4. Recruitment Management

- Job requisitions
- Job approvals
- Job posts
- Public career listings
- Candidate applications
- CV and document uploads
- Applicant tracking pipeline
- Candidate shortlisting
- Interview scheduling
- Interview scorecards
- Offer management
- Candidate status history
- Talent pool
- Candidate-to-employee conversion

### 5. Candidate Portal

- Public job browsing
- Online job applications
- Application status visibility
- Interview response handling
- Requested document uploads
- Offer response handling
- Candidate communication history

### 6. Onboarding

- Onboarding templates
- Role-based onboarding checklists
- HR onboarding tasks
- IT onboarding tasks
- Manager onboarding tasks
- Document collection
- Asset requests
- Welcome notifications
- Onboarding progress tracking

### 7. Attendance

- Daily check-in and check-out
- Attendance calendar
- Manual attendance corrections
- Late and early departure tracking
- Remote, hybrid and office status
- Manager approval for corrections
- Monthly attendance summaries
- Attendance export for payroll

### 8. Leave Management

- Leave types
- Leave policies
- Leave balances
- Leave requests
- Multi-step approval flow
- Manager and HR approvals
- Leave calendar
- Leave cancellation
- Medical document uploads
- Balance restoration rules
- Department absence visibility

### 9. Payroll Preparation

- Salary profiles
- Salary components
- Monthly payroll runs
- Attendance and leave impact
- Adjustments
- Draft payroll review
- Finance approval
- Payslip generation
- Payroll export
- Payroll locking

### 10. Performance Management

- Review cycles
- Probation reviews
- Goal tracking
- Self-reviews
- Manager reviews
- Review scorecards
- Peer feedback
- Performance history
- Improvement plans
- Review reports

### 11. Document Management

- Employee documents
- Candidate documents
- HR templates
- Offer letters
- Agreements
- Certificates
- Payslips
- Private file access
- File versioning
- Document expiry reminders
- Permission-based downloads

### 12. Asset Management

- Asset register
- Asset categories
- Asset assignment
- Asset returns
- Asset condition tracking
- Serial number tracking
- Asset handover records
- Asset history

### 13. Offboarding

- Resignation requests
- Notice period tracking
- Exit checklist
- Asset return workflow
- Final payroll notes
- Exit interviews
- Experience letter generation
- Account deactivation

### 14. Announcements and Communication

- Company announcements
- Department announcements
- Targeted notices
- Read receipts
- Email notifications
- In-app notifications

### 15. Reporting and Analytics

- Executive dashboard
- HR dashboard
- Recruitment funnel
- Headcount reports
- Attendance reports
- Leave reports
- Payroll summaries
- Performance reports
- Document expiry reports
- Asset reports
- Exportable reports

---

## Technology Stack

### Backend

- Laravel 13
- PHP 8.3+
- PostgreSQL via Neon
- Redis for cache, queues and rate limiting
- Laravel Reverb for real-time events
- Laravel Scout with Meilisearch for search
- Laravel Sanctum where token-based access is required
- Spatie Permission for roles and permissions
- Spatie Activitylog for audit trails
- Scribe for API documentation
- Pest for backend testing

### Frontend

- Vue 3
- Inertia.js 2
- TypeScript
- Vite
- Tailwind CSS 4
- shadcn-vue
- Reka UI
- Lucide Vue
- TanStack Table
- FullCalendar
- ApexCharts
- Sonner Vue
- VueUse
- Vue Draggable

### Storage

- Cloudflare R2
- S3-compatible Laravel filesystem disk
- Private object storage
- Permission-checked downloads through the application

### DevOps

- Docker-ready local development
- GitHub Actions
- Automated tests
- Static analysis
- Code formatting
- Queue workers
- Scheduled commands
- Staging and production environments

---

## Architecture Direction

PeopleOps uses a modular monolith architecture.

This keeps the system clean, deployable and maintainable while avoiding unnecessary microservice complexity.

Expected engineering standards:

- Thin controllers
- Form request validation
- Policy-based authorisation
- Service classes for business logic
- Action classes for workflow operations
- Events and listeners for side effects
- Queued jobs for heavy tasks
- Notifications for user communication
- Enum-based statuses
- Query scopes for filters
- Strict audit logging for sensitive actions
- Tests for business-critical workflows

---

## User Roles

Default roles:

- Super Admin
- HR Manager
- Recruiter
- Department Manager
- Finance Officer
- Employee
- Interviewer
- System Auditor
- Candidate

Access must be permission-driven. Do not rely only on role names inside business logic.

---

## Enterprise UX Standards

The product must feel like a modern SaaS platform.

Required UX behaviour:

- Clean dashboard layout
- Collapsible sidebar
- Global search
- Clear breadcrumbs
- Command-friendly navigation
- Responsive screens
- Fast server-side filtering
- Proper empty states
- Loading states
- Form validation messages
- Success and error toasts
- Confirmation dialogs
- Safe destructive actions
- Accessible components
- Keyboard-friendly interactions
- Clear approval states
- Timeline views for lifecycle records
- Kanban pipeline for recruitment
- Calendar views for leave, attendance and interviews

Primary UI system: **shadcn-vue**.

---

## Security Standards

The platform handles sensitive HR data. Security is mandatory.

Required controls:

- Server-side validation
- Policy-based access control
- Private document storage
- Signed file access
- Rate-limited public forms
- Secure password handling
- Protected salary and payroll data
- Protected document access
- Audit logs for critical actions
- Soft deletion for sensitive records
- Upload type and size validation
- Session hardening
- Environment-based secrets
- Least-privilege access rules

Sensitive events must be logged, including:

- Employee profile changes
- Salary changes
- Role and permission changes
- Document downloads
- Leave approvals
- Payroll approvals
- Candidate stage changes
- Offer creation
- Account deactivation

---

## Local Development

### Requirements

- PHP 8.3 or newer
- Composer
- Node.js 22 LTS or newer
- npm
- PostgreSQL-compatible database
- Redis
- Meilisearch
- Cloudflare R2 credentials for file storage
- Git

### Clone

```bash
git clone https://github.com/codezelaca/peopleops-enterprise-hrms-platform.git
cd peopleops-enterprise-hrms-platform
```

### Install dependencies

```bash
composer install
npm install
```

### Environment

```bash
cp .env.example .env
php artisan key:generate
```

Configure the environment values for:

- Application URL
- PostgreSQL database
- Redis
- Queue connection
- Mail
- Cloudflare R2
- Meilisearch
- Reverb
- Session domain
- Cache driver
- Log channel

The first successful `/register` request is intentionally a one-time bootstrap path. It creates the initial administrator role assignment automatically. After any user exists, public registration closes and future users must be managed from the authenticated admin user-management workflow.

### Database

```bash
php artisan migrate --seed
```

Use the direct Neon connection for migrations and the pooled connection for normal application runtime.

The seed step creates roles and permissions only. It does not create a default user, so a fresh database can still use the first-admin bootstrap flow.

### Frontend build

```bash
npm run dev
```

### Laravel server

```bash
php artisan serve
```

### Queue worker

```bash
php artisan queue:work
```

### Scheduler

```bash
php artisan schedule:work
```

### Reverb

```bash
php artisan reverb:start
```

---

## Quality Commands

### Backend tests

```bash
php artisan test
```

### Pest

```bash
./vendor/bin/pest
```

### Frontend checks

```bash
npm run lint
npm run types:check
npm run format:check
```

### Frontend build

```bash
npm run build
```

### Browser tests

```bash
npx playwright test
```

### Full quality check

```bash
php artisan test
./vendor/bin/pint --test
npm run lint
npm run types:check
npm run build
npx playwright test
```

---

## Environment Notes

### Neon PostgreSQL

Use Neon as the primary database provider.

Recommended setup:

- Direct connection for migrations
- Pooled connection for runtime
- Separate databases or branches for local, staging and production
- SSL enabled
- Backups enabled
- No production secrets committed to the repository

### Cloudflare R2

Use R2 for private document storage.

Required storage rules:

- No public HR document buckets
- No direct exposure of sensitive file URLs
- Access files through Laravel after permission checks
- Validate all uploads
- Store file metadata in the database

Company logos are stored on the configured private disk and served through authenticated Laravel routes. Set `FILESYSTEM_DISK=s3` with the Cloudflare R2 S3-compatible credentials when moving beyond local storage.
- Keep document downloads auditable

### Redis

Use Redis for:

- Cache
- Queues
- Rate limiting
- Broadcast scaling where needed

### Meilisearch

Use Meilisearch for fast search across:

- Employees
- Candidates
- Job posts
- Departments
- Documents metadata
- Assets

Do not index sensitive document contents unless explicitly approved.

---

## Testing Expectations

Tests are required for all critical workflows.

Priority coverage:

- Authentication
- Permissions
- Employee creation
- Employee update
- Candidate application
- Recruitment stage movement
- Interview scheduling
- Leave request submission
- Leave approval
- Leave balance updates
- Attendance correction
- Payroll generation
- Document upload
- Document access control
- Asset assignment
- Offboarding
- Audit logging

Browser tests should cover the main user journeys:

- HR creates an employee
- Candidate applies for a job
- Recruiter moves candidate through stages
- Employee requests leave
- Manager approves leave
- HR generates payroll
- Employee views a payslip

---

## Git Workflow

Default branch:

```bash
main
```

Recommended branches:

```bash
feature/module-name
fix/issue-name
refactor/scope-name
release/version-name
```

Commit style:

```bash
feat: add employee document upload flow
fix: prevent leave approval beyond available balance
refactor: move payroll calculation into service
test: cover candidate-to-employee conversion
docs: update storage setup notes
```

Pull request requirements:

- Clear summary
- Linked issue or task
- Screenshots for UI changes
- Migration notes when relevant
- Test results
- No unrelated formatting changes
- No secrets or generated files

---

## Code Standards

Backend:

- Follow Laravel conventions
- Use strict validation
- Keep controllers small
- Use policies for access checks
- Use services/actions for business rules
- Use transactions for multi-step writes
- Use queues for emails, PDFs and heavy exports
- Use enums for statuses
- Avoid duplicated query logic
- Log critical business events

Frontend:

- Use Vue Composition API
- Use TypeScript types
- Use shadcn-vue components
- Keep components small
- Avoid repeated form layouts
- Use reusable table and filter components
- Use server-side pagination for large datasets
- Use accessible labels and controls
- Keep destructive actions confirmed

---

## Documentation

Repository documentation:

- `README.md` — project overview and setup
- `AGENTS.md` — coding agent instructions
- `DESIGN.md` — product UI and design standards
- Product specification document — enterprise scope and feature expectations

All new features should include enough documentation for another developer to understand the workflow, permissions and business rules.

---

## Deployment Expectations

Production deployment must include:

- HTTPS
- Queue workers
- Scheduler
- Reverb process
- Redis
- Meilisearch
- PostgreSQL backups
- R2 private storage
- Proper log rotation
- Environment-based configuration
- Error monitoring
- Uptime monitoring
- Database migration process
- Rollback plan

Never deploy production using local-only development assumptions.

---

## Roadmap

### Phase 1: Platform Foundation

- Authentication
- Roles and permissions
- Organisation setup
- Base layout
- Dashboard shell
- Audit logging
- Core settings

### Phase 2: Employee Operations

- Employee directory
- Employee profiles
- Documents
- Departments
- Managers
- Work history
- Employee self-service

### Phase 3: Recruitment

- Job requisitions
- Job posts
- Candidate applications
- Recruitment pipeline
- Interviews
- Offers
- Candidate conversion

### Phase 4: Workforce Management

- Attendance
- Leave
- Holidays
- Approval workflows
- HR calendar

### Phase 5: Payroll and Performance

- Payroll preparation
- Payslips
- Review cycles
- Goals
- Performance reports

### Phase 6: Enterprise Completion

- Assets
- Onboarding
- Offboarding
- Advanced reports
- Realtime notifications
- Browser tests
- Production hardening

---

## Maintainers

This repository is maintained by **Codezela Career Accelerator**.

Organisation: Codezela Technologies  
Repository: <https://github.com/codezelaca/peopleops-enterprise-hrms-platform>

---

## Licence

This project is proprietary software owned by Codezela Career Accelerator / Codezela Technologies unless a separate licence file states otherwise.

Do not copy, distribute, resell or reuse the source code outside authorised project use.
