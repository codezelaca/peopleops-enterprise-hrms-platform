# DESIGN.md

## Product

**PeopleOps** is an enterprise HRMS and Recruitment Management System for real companies, not a demo app.

The interface must feel secure, calm, fast, and premium. Every screen should help HR teams, managers, recruiters, finance users, employees, and candidates complete work with minimum friction.

## Design Goals

- Enterprise-grade, not decorative.
- Clear before clever.
- Fast workflows over heavy visuals.
- Consistent layouts, spacing, states, and language.
- Accessible by default.
- Beautiful, but never noisy.
- Mobile-friendly where work naturally happens on mobile.
- Optimised for dense business data without feeling crowded.

## Frontend Stack

Use this stack unless there is a strong technical reason not to.

- Vue 3
- Inertia.js
- TypeScript
- Tailwind CSS
- shadcn-vue
- Reka UI
- Lucide Vue
- TanStack Table
- FullCalendar
- ApexCharts
- VueUse
- Sonner Vue
- VueDraggable
- Day.js
- Zod where useful

## Visual Direction

### Brand Feel

- Clean SaaS interface.
- White-first design.
- Purple used as the primary accent.
- Soft borders.
- Light shadows.
- Rounded corners.
- Calm status colours.
- High readability.
- Minimal gradients.
- No childish illustrations.
- No overdesigned cards.

### Primary Colour

Use purple as the main brand accent.

Recommended direction:

- Primary: purple
- Background: white / off-white
- Text: near-black
- Borders: soft neutral
- Success: green
- Warning: amber
- Danger: red
- Info: blue

Do not use saturated colours everywhere. Purple should guide attention, not dominate the UI.

## Layout

### App Shell

Use a stable enterprise layout:

- Left sidebar
- Top header
- Main content area
- Breadcrumbs
- Page title
- Page actions
- Notification centre
- User profile menu

### Sidebar

The sidebar must support:

- Collapsed and expanded states
- Clear active state
- Grouped navigation
- Icons with labels
- Role-based visibility
- Smooth transitions
- Keyboard accessibility

Recommended navigation groups:

- Dashboard
- Employees
- Recruitment
- Attendance
- Leave
- Payroll
- Performance
- Documents
- Assets
- Reports
- Settings

### Page Structure

Every main page should follow this order:

1. Breadcrumb
2. Page title
3. Short description when needed
4. Primary action
5. Filters or summary cards
6. Main content
7. Empty/loading/error states

## Typography

Use a modern sans-serif font.

Recommended:

- Inter
- Geist
- Instrument Sans

Rules:

- Page titles: clear and strong
- Body text: simple and readable
- Labels: short
- Helper text: useful only
- Avoid long paragraphs inside the app
- Avoid all caps except small tags

## Spacing

Use consistent spacing.

- Page padding: 24px desktop, 16px mobile
- Card padding: 20px to 24px
- Form field gap: 16px
- Section gap: 24px to 32px
- Table density: comfortable, not cramped

Do not manually guess spacing for every screen. Reuse layout components.

## Components

Use shadcn-vue as the main UI system.

Required shared components:

- Button
- Input
- Textarea
- Select
- Checkbox
- Radio
- Switch
- Date picker
- Dialog
- Drawer
- Sheet
- Dropdown menu
- Tooltip
- Popover
- Tabs
- Card
- Badge
- Avatar
- Alert
- Toast
- Data table
- Pagination
- Empty state
- Loading skeleton
- Confirmation dialog
- Stepper
- Command palette
- File uploader
- Timeline
- Status chip

Do not create one-off UI unless the shared component cannot handle the use case.

## Buttons

### Variants

- Primary
- Secondary
- Outline
- Ghost
- Destructive
- Link

### Rules

- One primary action per section.
- Destructive actions require confirmation.
- Loading buttons must show disabled state.
- Button labels must be action-based.

Good:

- Add Employee
- Approve Leave
- Schedule Interview
- Generate Payslips

Avoid:

- Submit
- Click Here
- Go
- Okay

## Forms

Forms must be clean, grouped, and forgiving.

Required behaviour:

- Inline validation
- Server-side validation
- Clear required fields
- Save/loading state
- Dirty state protection
- Helpful empty defaults
- Success toast
- Error summary for large forms

Large forms should use sections or tabs.

Examples:

- Basic Information
- Employment Details
- Salary Details
- Documents
- Emergency Contact
- Access & Permissions

Never place 40 fields in one long unstructured form.

## Tables

Use TanStack Table for serious data views.

Required features where relevant:

- Search
- Filters
- Sorting
- Pagination
- Column visibility
- Row actions
- Bulk actions
- Empty state
- Loading skeleton
- Export action when needed
- Sticky header for dense pages

Tables must not become unreadable on mobile. Use responsive cards or reduced columns.

## Filters

Filters should be visible, fast, and reusable.

Common filters:

- Department
- Branch
- Role
- Status
- Date range
- Employment type
- Manager
- Job post
- Candidate stage

Use saved filters for complex reporting screens where useful.

## Status Design

Statuses must be visually consistent.

Use badges/chips for:

- Active
- Probation
- Suspended
- Resigned
- Pending
- Approved
- Rejected
- Draft
- Published
- Overdue
- Expired
- Hired

Status colour must match meaning across the whole app.

## Dashboards

Dashboards must show real decision data.

Use:

- Metric cards
- Small trend indicators
- Simple charts
- Action queues
- Upcoming items
- Risk alerts
- Recent activity

Avoid vanity dashboards with meaningless numbers.

Each dashboard must be role-specific.

### Admin Dashboard

Focus on system health, headcount, approvals, recruitment, attendance, payroll status, and risks.

### HR Dashboard

Focus on employees, hiring, leave, onboarding, documents, and compliance.

### Manager Dashboard

Focus on team attendance, leave approvals, interviews, performance reviews, and pending actions.

### Employee Dashboard

Focus on attendance, leave balance, documents, announcements, payslips, and personal tasks.

### Candidate Portal

Focus on open jobs, application status, interviews, document requests, and offers.

## Recruitment UX

Recruitment must feel like a real ATS.

Required screens:

- Careers page
- Job detail page
- Candidate application
- Candidate profile
- Candidate pipeline
- Interview schedule
- Interview feedback
- Offer management
- Hiring conversion

Pipeline must support clear stages and drag-and-drop only where permissions allow it.

Every candidate action must create a timeline entry.

## Employee Profile UX

Employee profile must be a 360-degree view.

Recommended tabs:

- Overview
- Job
- Attendance
- Leave
- Payroll
- Documents
- Performance
- Assets
- Activity

The profile header should show:

- Name
- Photo/avatar
- Employee ID
- Designation
- Department
- Status
- Manager
- Quick actions

## Leave UX

Leave flow must be simple.

Employee view:

- Leave balance
- Request leave
- Request history
- Team calendar where permitted

Manager/HR view:

- Pending approvals
- Leave conflicts
- Attachments
- Approval history
- Comments

Do not hide policy logic. Show why a request is blocked or warned.

## Attendance UX

Attendance must be easy to read.

Required views:

- Today status
- Check-in/check-out
- Monthly calendar
- Exceptions
- Correction requests
- Monthly summary

Use warnings for late, absent, missing checkout, and pending correction.

## Payroll UX

Payroll must feel controlled and auditable.

Use a step-based flow:

1. Select month
2. Generate draft
3. Review items
4. Add adjustments
5. Approve
6. Lock
7. Export / generate payslips

Locked payroll must not be edited without an authorised unlock flow.

## Documents UX

Documents must be secure and organised.

Required behaviour:

- Private access
- Upload progress
- File type validation
- File size validation
- Preview where possible
- Expiry date support
- Version history where needed
- Clear permission handling

Document categories must be easy to scan.

## Reports UX

Reports should support decision-making.

Required behaviour:

- Filters
- Date ranges
- Export
- Charts where useful
- Drill-down where useful
- Clear empty state
- Print-friendly layout where needed

Do not overload one report with every metric.

## Notifications

Use notifications for meaningful events only.

Channels:

- In-app
- Email
- Real-time where useful

Notification centre must show:

- Title
- Short message
- Related record
- Time
- Read/unread state
- Action link

Avoid notification spam.

## Search

Provide global search for:

- Employees
- Candidates
- Jobs
- Documents
- Departments
- Reports where useful

Search results must be grouped by type.

## Motion

Use motion lightly.

Allowed:

- Sidebar collapse
- Modal open/close
- Toasts
- Row expansion
- Drag-and-drop
- Skeleton loading
- Subtle hover states

Avoid:

- Heavy animations
- Long transitions
- Decorative motion
- Moving backgrounds

Motion should make state changes clearer.

## Loading States

Use skeletons for:

- Dashboards
- Tables
- Cards
- Profiles

Use spinners only for small actions.

Never show a blank page while loading.

## Empty States

Every empty state must explain what happened and what to do next.

Examples:

- No employees found
- No candidates in this stage
- No leave requests pending
- No documents uploaded yet

Add a primary action when useful.

## Error States

Errors must be clear and calm.

Show:

- What went wrong
- What can be retried
- What needs user action
- Support message only when useful

Do not expose technical exceptions to users.

## Accessibility

Minimum standard: WCAG 2.2 AA direction.

Required:

- Keyboard navigation
- Visible focus rings
- Proper labels
- Correct heading order
- Sufficient contrast
- 44px minimum touch targets
- Semantic buttons and links
- ARIA only when needed
- No colour-only meaning
- Form errors linked to fields

All modals, menus, popovers, and calendars must be keyboard usable.

## Responsive Rules

Desktop is primary for HR/admin work.

Mobile must support:

- Employee self-service
- Candidate applications
- Leave requests
- Attendance
- Notifications
- Simple profile updates

Dense admin tables may become stacked cards on mobile.

## Content Style

Use clear, human product language.

Rules:

- Short labels
- Direct actions
- No technical jargon
- No filler text
- No fake corporate wording
- No unexplained acronyms
- Use British English

Good:

- Leave request approved
- Candidate moved to Interview
- Payroll run locked
- Document expires soon

Avoid:

- Operation successful
- Data processed
- Entity updated
- User action completed

## Security UX

Security should be visible without creating fear.

Show:

- Permission-based locked actions
- Audit history for sensitive records
- Last updated by
- Document access status
- Payroll locked status
- Role warnings before permission changes

Never silently fail permission checks.

## Data Density

This is an enterprise product. Dense data is acceptable when organised.

Use:

- Tabs
- Accordions
- Filters
- Summary cards
- Sticky actions
- Progressive disclosure

Avoid:

- Huge single-page forms
- Overloaded cards
- Too many charts
- Hidden critical actions

## Design Tokens

Use semantic tokens, not random colours.

Recommended token groups:

- background
- foreground
- primary
- secondary
- muted
- border
- input
- ring
- destructive
- success
- warning
- info
- card
- popover

Do not hardcode colours inside feature pages.

## Component Ownership

All repeated UI must live in shared components.

If a pattern appears three times, create a component.

Examples:

- StatusBadge
- PageHeader
- FilterBar
- DataTable
- EmptyState
- ConfirmDialog
- FileUpload
- UserAvatar
- MetricCard
- Timeline
- ApprovalCard

## Quality Checklist

Before a screen is accepted:

- Looks consistent with the app shell
- Uses shared components
- Handles loading state
- Handles empty state
- Handles error state
- Has validation
- Has permission checks
- Works on desktop
- Works acceptably on mobile
- Uses British English
- Has accessible labels
- Has no hardcoded demo data
- Has no broken dark/light contrast
- Has no one-off styling without reason

## Do Not Do

- Do not use random UI libraries per page.
- Do not mix design styles.
- Do not build default-looking Laravel screens.
- Do not use fake charts.
- Do not use public links for private documents.
- Do not use colour as the only status indicator.
- Do not hide validation errors.
- Do not create destructive actions without confirmation.
- Do not create screens with only placeholder data.
- Do not add animation that slows work.

## Final Standard

The product should feel like a polished internal SaaS used by real HR, recruitment, finance, managers, employees, and candidates.

Every screen must answer:

- What is happening?
- What needs attention?
- What can the user do next?
- Is the data trustworthy?
- Is the action safe?
