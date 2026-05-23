# AGENTS.md

This file provides operating instructions for AI coding agents working on **PeopleOps**, an enterprise-grade HRMS + Recruitment Management System.

Treat this repository as a real production product, not a demo, tutorial, scaffold, or student CRUD app. Every change must preserve enterprise quality, security, maintainability, performance, and user experience.

---

## 1. Product Context

**Product name:** PeopleOps  
**Product type:** Enterprise HRMS + Recruitment Management Platform  
**Primary users:** HR teams, recruiters, department managers, finance officers, employees, interviewers, candidates, administrators, auditors, and leadership teams.  
**Business purpose:** Manage the full employee and recruitment lifecycle from workforce setup, hiring, candidate management, onboarding, attendance, leave, documents, assets, payroll preparation, performance reviews, offboarding, reporting, notifications, and auditability.

The system must feel like a polished SaaS product suitable for real SMEs, agencies, institutes, BPOs, software companies, and service businesses.

Do not build placeholder-level features. All major flows must behave like real enterprise workflows with validation, permissions, activity tracking, clear states, and smooth UX.

---

## 2. Canonical Technology Stack

Use the current stable versions compatible with the project at the time of implementation.

### Backend

- Laravel 13 preferred for new builds.
- PHP 8.3 or newer.
- PostgreSQL through Neon.
- Redis for queues, cache, rate limiting, and background processing.
- Laravel Queues for heavy or delayed tasks.
- Laravel Scheduler for recurring HR tasks.
- Laravel Reverb + Echo for real-time notifications where needed.
- Laravel Scout + Meilisearch for fast global search.
- Cloudflare R2 for private document and media storage.
- Pest PHP for backend tests.
- Scribe or OpenAPI-compatible documentation for APIs where APIs are exposed.

### Frontend

- Vue 3 with Composition API.
- Inertia.js.
- TypeScript.
- Tailwind CSS.
- shadcn-vue as the primary UI component system.
- Reka UI for accessible primitives where shadcn-vue depends on or benefits from them.
- Lucide Vue for icons.
- Sonner Vue for toast notifications.
- TanStack Table for advanced tables.
- FullCalendar for HR calendars.
- ApexCharts or ECharts for analytics dashboards.
- VueUse for reusable Vue utilities.
- Vue draggable library only when drag-and-drop is required, such as recruitment pipelines.

### DevOps and Quality

- Docker or Laravel Sail for local development consistency.
- GitHub Actions for CI.
- Playwright for browser/end-to-end tests.
- Static analysis should be used where practical, preferably Larastan/PHPStan.
- Laravel Pint must be used for PHP formatting.
- Prettier must be used for frontend formatting.
- ESLint must be used for frontend code quality.

Do not introduce alternative frameworks, state managers, UI kits, databases, or storage systems without a strong technical reason and explicit approval.

---

## 3. Setup Commands

Use these commands as the default local workflow. Adjust only if the repository has already defined a different package manager or script naming convention.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev
php artisan serve
```

When queues, realtime, and scheduled jobs are needed locally, run:

```bash
php artisan queue:work
php artisan schedule:work
php artisan reverb:start
```

Before opening a pull request or marking a task complete, run:

```bash
php artisan test
./vendor/bin/pint
npm run lint
npm run build
```

If Playwright is configured, also run:

```bash
npx playwright test
```

Never claim the project is ready if validation commands have not been run. If a command cannot be run in the current environment, state that clearly in the final response and explain what remains unverified.

---

## 4. Environment and Infrastructure Rules

### Neon PostgreSQL

- Use PostgreSQL-native strengths such as constraints, indexes, transactions, JSONB where useful, and relational integrity.
- Use direct database connections for migrations when required.
- Use pooled connections for application runtime when appropriate.
- Never write database logic that assumes MySQL-only behaviour.
- Avoid raw SQL unless it is justified and covered by tests.

### Cloudflare R2

- Treat all HR, employee, candidate, payroll, medical, identity, and contract documents as private by default.
- Do not expose public R2 URLs for sensitive files.
- Serve private files through Laravel routes that check permissions before generating signed access.
- Validate file type, size, ownership, and access scope on upload and download.
- Store document metadata in the database and the binary object in R2.

### Redis and Queues

Use queues for:

- Email notifications.
- Document generation.
- Payslip generation.
- Import/export jobs.
- Bulk candidate or employee operations.
- Search indexing.
- Long-running reports.

Do not block HTTP requests with heavy processing.

---

## 5. Product Scope and Feature Expectations

Build features as connected enterprise workflows, not isolated CRUD screens.

### Core Enterprise Modules

The product should support the following functional areas:

- Authentication and account management.
- Roles, permissions, and policy-based access control.
- Company, branch, department, designation, employment type, and reporting-line management.
- Employee information management.
- Employee self-service portal.
- Recruitment requisitions.
- Public careers pages.
- Candidate application portal.
- Candidate database and talent pool.
- Recruitment pipeline with stages and status history.
- Interview scheduling and feedback.
- Offer management.
- Candidate-to-employee conversion.
- Onboarding checklists.
- Attendance management.
- Leave management with balances and approval workflows.
- Payroll preparation and finance-ready summaries.
- Payslip generation where required.
- Performance review cycles.
- Goals, KPIs, probation reviews, and improvement plans.
- Document management.
- HR letter generation.
- Asset management.
- Resignation and offboarding.
- Announcements and internal communication.
- HR calendar.
- Notifications and reminders.
- Reports and analytics.
- Audit logs and activity logs.
- System settings.

If a feature touches sensitive HR data, it must include permissions, validation, auditability, and clear user feedback.

---

## 6. Critical Business Workflows

The system must preserve clean state transitions and realistic business rules.

### Hiring Workflow

A typical hiring flow should support:

1. Department or manager creates a hiring request.
2. HR reviews the requisition.
3. Approval is granted or rejected.
4. Job post is published internally and/or publicly.
5. Candidate applies through a public candidate experience.
6. Recruiter screens the application.
7. Candidate moves through pipeline stages.
8. Interview is scheduled.
9. Interviewer submits scorecard and feedback.
10. Offer is created and sent.
11. Candidate accepts or rejects.
12. Accepted candidate is converted into an employee.
13. Onboarding starts automatically.

### Leave Workflow

A typical leave flow should support:

1. Employee creates a leave request.
2. System validates eligibility and balance.
3. Manager reviews the request.
4. HR performs final review where configured.
5. Leave is approved, rejected, or cancelled.
6. Balance and calendar are updated.
7. Notifications are sent.
8. Activity is logged.

### Payroll Preparation Workflow

A typical payroll flow should support:

1. HR creates the payroll period.
2. System pulls salary profiles, attendance, leave, and adjustments.
3. HR reviews draft payroll.
4. Finance reviews and approves.
5. Payslips or exports are generated.
6. Payroll period is locked.

### Offboarding Workflow

A typical offboarding flow should support:

1. Employee resigns or HR starts offboarding.
2. Manager acknowledgement is recorded.
3. HR exit checklist starts.
4. IT and asset return tasks are created.
5. Finance records final payroll notes.
6. Exit interview is completed.
7. Experience or service letters are generated where appropriate.
8. Account access is disabled according to policy.

---

## 7. Architecture Principles

Use a modular monolith approach.

- Keep controllers thin.
- Put business logic in Actions, Services, domain classes, or dedicated workflow handlers.
- Use Form Requests for validation.
- Use Policies and Gates for authorisation.
- Use Events and Listeners for workflow side effects.
- Use Jobs for background work.
- Use Notifications for user-facing alerts.
- Use Enums for statuses and fixed business states.
- Use database transactions for multi-step workflow changes.
- Use explicit status transitions instead of random string updates.
- Avoid duplicating business rules between frontend and backend.
- Backend validation and authorisation are always the source of truth.

Do not create giant controllers, unstructured helper files, or business logic hidden inside Vue components.

---

## 8. Frontend and UX Rules

The UI must feel smooth, modern, responsive, and enterprise-grade.

### Visual Direction

- Clean SaaS dashboard style.
- Mostly white background.
- Purple as the primary accent colour.
- Subtle borders, shadows, and spacing.
- Strong information hierarchy.
- No cluttered admin-template look.
- No raw default browser controls.
- No inconsistent component styles.

### Component Rules

- Use shadcn-vue components as the primary design system.
- Use TypeScript for Vue components.
- Prefer reusable components for tables, filters, cards, modals, empty states, forms, tabs, upload areas, and status badges.
- Use Lucide Vue icons consistently.
- Use TanStack Table for complex tables with filters, sorting, pagination, column visibility, row actions, and bulk actions.
- Use FullCalendar for leave, attendance, interview, holiday, contract-expiry, and review calendars.
- Use charts only when the data supports decision-making.

### UX Requirements

Every major screen should include:

- Clear page title and description.
- Breadcrumbs where useful.
- Relevant primary action.
- Search and filters where data lists exist.
- Loading states.
- Empty states.
- Error states.
- Success feedback.
- Confirmation dialogs for destructive actions.
- Responsive behaviour.
- Accessible labels and keyboard-friendly interactions.

### Do Not

- Do not create static fake dashboards.
- Do not hardcode numbers in analytics cards unless explicitly seeding demo data.
- Do not create tables without filtering and pagination for large datasets.
- Do not rely only on frontend validation.
- Do not expose salary, payroll, personal, or document data in general search results without permission checks.

---

## 9. Security and Compliance Expectations

HRMS data is sensitive. Treat security as a product requirement.

### Required Security Practices

- Enforce authentication on all private routes.
- Enforce policy-based authorisation on sensitive actions.
- Use permission checks for every role-sensitive feature.
- Protect employee salary, bank, identity, contract, medical, and document data.
- Validate all input server-side.
- Use rate limiting for login, password reset, public job applications, and sensitive endpoints.
- Use CSRF protection for Inertia forms.
- Avoid mass assignment vulnerabilities.
- Avoid exposing internal IDs where public tokens or UUIDs are more suitable.
- Never log sensitive personal data, passwords, tokens, documents, or payroll details.
- Use soft deletes where recovery or audit history matters.
- Add audit logs for critical actions.

### Audit Critical Actions

Audit at minimum:

- User role or permission changes.
- Employee profile changes.
- Salary or payroll changes.
- Leave approvals and rejections.
- Candidate stage movements.
- Interview feedback submissions.
- Offer creation and acceptance.
- Document uploads, downloads, deletes, and replacements.
- Asset assignment and return.
- Account suspension or deactivation.

---

## 10. Backend Coding Standards

- Follow Laravel conventions unless there is a clear reason not to.
- Use strict, readable naming.
- Keep methods small and intention-revealing.
- Prefer explicit classes over hidden magic for business workflows.
- Use Eloquent relationships carefully and avoid N+1 queries.
- Add indexes for fields used in filtering, searching, and reporting.
- Wrap multi-record workflow updates in transactions.
- Return users to meaningful pages after actions.
- Always flash or return user-friendly success/error messages.
- Use pagination for large datasets.
- Use queued jobs for slow operations.
- Do not add packages casually. Prefer first-party Laravel features and already-approved packages.

---

## 11. Frontend Coding Standards

- Use Vue 3 Composition API.
- Use TypeScript for props, emitted events, page props, and shared types.
- Keep Vue components focused and reusable.
- Use composables for repeated logic.
- Use Inertia `useForm` for forms unless another approach is clearly better.
- Keep server state authoritative.
- Do not duplicate backend permission logic; pass permissions from backend when needed.
- Use accessible component patterns from shadcn-vue and Reka UI.
- Use consistent spacing, typography, badges, buttons, form controls, and table patterns.
- Avoid one-off inline styles unless necessary.

---

## 12. Testing Requirements

Every meaningful change should include or update tests.

### Backend Tests

Use Pest for:

- Authentication flows.
- Permission and policy behaviour.
- Employee creation and update flows.
- Candidate application and pipeline flows.
- Leave request and approval rules.
- Attendance calculations.
- Payroll preparation rules.
- Document access permissions.
- Notification dispatching.
- Audit logging.

### Browser Tests

Use Playwright for critical user journeys:

- Login.
- Employee creation.
- Candidate application.
- Candidate pipeline movement.
- Interview scheduling.
- Leave request submission.
- Leave approval.
- Document upload.
- Payroll draft review.

### Test Behaviour

- Do not only test happy paths.
- Test permission denial cases.
- Test validation failures.
- Test workflow edge cases.
- Use factories and seeders rather than brittle hardcoded data.

---

## 13. Performance Requirements

- Avoid N+1 queries.
- Paginate large tables.
- Use server-side filtering for large datasets.
- Queue slow jobs.
- Cache expensive reports only where freshness rules allow.
- Keep dashboards fast by using summarised queries or cached aggregates where suitable.
- Do not load large employee, candidate, or document datasets into the browser unnecessarily.
- Optimise file upload and download handling.
- Build frontend assets before deployment.

---

## 14. Accessibility and Responsiveness

- Build accessible forms with labels, helper text, error messages, and keyboard support.
- Preserve colour contrast, especially for purple accents and status badges.
- Do not rely on colour alone to communicate status.
- Ensure modals, dropdowns, and command menus are keyboard-friendly.
- Ensure main HR workflows work on common laptop and tablet screen sizes.
- Mobile support is required for employee self-service flows such as leave requests, attendance, announcements, and document viewing.

---

## 15. Data, Privacy, and Seed Content

- Use realistic seed data, but never use real personal information.
- Demo data should feel like a real company, with departments, managers, employees, candidates, job posts, leaves, interviews, and payroll examples.
- Do not seed offensive, joke, or obviously fake names.
- Avoid storing sensitive information in plain text.
- Do not commit `.env`, credentials, access keys, R2 secrets, Neon credentials, or third-party API keys.

---

## 16. Documentation Expectations

Whenever a feature is introduced or changed, update relevant documentation.

Good documentation should cover:

- Purpose of the feature.
- User roles involved.
- Main workflow.
- Business rules.
- Important permissions.
- Known limitations.
- Setup requirements if any.
- Test command or validation notes.

Avoid writing long theory. Keep documentation practical and usable for developers, QA, and product owners.

---

## 17. Pull Request Standards

Before completing work, confirm:

- The feature satisfies the product workflow, not just the screen UI.
- Validation exists on the backend.
- Permissions are enforced.
- Sensitive data is protected.
- UX states are handled.
- Tests were added or updated.
- Formatting, linting, tests, and build pass or any unverified command is clearly reported.
- No unrelated files were changed.
- No secrets or generated local files were committed.

Suggested PR summary format:

```md
## Summary

- What changed
- Why it changed

## Product Impact

- Which HRMS/recruitment workflow this affects

## Validation

- Commands run
- Tests added/updated

## Screenshots

- Include for UI changes

## Notes

- Risks, limitations, or follow-up work
```

---

## 18. Agent Behaviour Rules

When working in this repository:

1. Read this file before making changes.
2. Understand the existing code before editing.
3. Prefer small, safe, reviewable changes.
4. Do not rewrite large areas without a clear reason.
5. Do not introduce new architectural patterns without aligning with this file.
6. Do not remove security, validation, audit, or permission logic to make implementation easier.
7. Do not downgrade the UI to basic scaffolding.
8. Do not leave TODOs for core business behaviour unless the task explicitly asks for a placeholder.
9. Explain trade-offs when making product or technical decisions.
10. If requirements are ambiguous, make the safest enterprise-grade assumption and state it clearly.

The default expectation is: **production-minded, secure, polished, tested, and maintainable.**

---

## 19. Approved Package Direction

Prefer these package categories and tools unless the repository already uses a different approved choice.

### Laravel

- spatie/laravel-permission for roles and permissions.
- spatie/laravel-activitylog for activity logs.
- maatwebsite/excel for imports and exports.
- laravel/scout with Meilisearch for search.
- pestphp/pest for tests.
- laravel/reverb for real-time features.
- barryvdh/laravel-dompdf or a browser-based PDF generator for PDFs, depending on quality needs.
- knuckleswtf/scribe for API documentation where required.

### Vue / Frontend

- shadcn-vue for UI components.
- Reka UI for accessible primitives.
- Tailwind CSS for styling.
- Lucide Vue for icons.
- TanStack Table for complex data tables.
- FullCalendar for calendars.
- ApexCharts or ECharts for reporting charts.
- Sonner Vue for toasts.
- VueUse for utilities.

Do not add overlapping libraries that solve the same problem unless replacing an existing tool intentionally.

---

## 20. Definition of Done

A task is only done when:

- The workflow works end to end.
- Backend validation exists.
- Permissions are enforced.
- Sensitive data is protected.
- UX is polished with loading, empty, success, and error states.
- Relevant tests pass.
- Code formatting and linting pass.
- Build passes.
- Documentation or notes are updated where needed.
- The final response states what was changed and what was validated.

If any part is not complete, say so clearly.
