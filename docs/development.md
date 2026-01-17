# CRM – Development Overview

## 1 Project Setup & Scaffolding

- Setup Laravel 11 project
- Setup React + Vite frontend (or Inertia if SPA hybrid)
- Configure Laravel Breeze or Jetstream (auth scaffolding)
- Setup Laravel Sanctum for SPA auth
- Setup `.env`, `.editorconfig`, `.gitignore`
- Setup GitHub repo and CI workflow
- Setup TailwindCSS + ESLint/Prettier

## 2 Database Design & Migrations

- Define all tables (users, watches, transactions, etc.)
- Add foreign keys, indexes, unique constraints
- Create migrations & seeders for core data (roles, statuses, etc.)
- Setup relationships in Eloquent models

## 3 Authentication & User System

- Login / Register / Forgot password
- Role-based redirection after login
- Laravel Sanctum token-based auth for SPA
- Implement email verification

## 4 Roles & Permissions

- Install and configure Spatie Laravel Permission
- Create predefined roles (Admin, Manager, Agent, etc.)
- Setup role-based gates & policies
- Role-based menu access and route guards

## 5 Watch Management

- CRUD: Add / Edit / Delete Watches
- Image upload with SKU-based filenames
- Image counter (max 40)
- Generate SKU logic
- AI Description generator integration (Make.com)
- Asynchronous queue handling
- Full-text search & pagination for 10K+ watches

## 6 Platform Sales Integration

- Per-platform schema (Catawiki, Tradera)
- CSV export for Catawiki
- Tradera API integration
- "Fill with AI" logic
- Listing status update and logs

## 7 Batch Management

- Create / Edit batch
- Link watches to batches
- Auto-update watch locations by batch status
- DHL API for tracking

## 8 Sales History

- Upload & parse Excel / CSV
- Match by SKU
- Update watch with buyer data
- Price audit info (currency, sold price, etc.)

## 9 Wish List Module

- CRUD: Add / Edit / Delete items
- Image resizer (1024x1024)
- Currency display (user default and EUR)
- Search and pagination

## 10 Agent & Seller Modules

- Agent Watches (limited access)
- Seller Watches (limited access)
- Balance & Deposit tracking
- Payment types with linked watches
- Refunded logic for balances

## 11 Full Data View

- Admin-only view
- Search / sort all fields
- Export CSV
- Dynamic filters

## 13 User Management

- Admin-only user CRUD
- Role-based access control
- Reset passwords / change roles

## 14 System Settings

- General settings
- Appearance (light/dark mode fixes)
- Webhook / API keys (Make.com, DHL)
- Notifications (optional / future)

## 15 Logs & Activity

- Watch logs (create, update, delete)
- AI error logs
- User activity tracking

## 16 Job Queues & Async Processing

- Laravel Queues (Redis)
- Laravel Horizon setup
- Background AI requests
- Batch updates, CSV import jobs

## 16 Testing & QA

- Feature tests (PHPUnit)
- Frontend tests (React Testing Library)
- Seed data (10K+ for stress tests)
- Validation + error handling
- Cross-browser / mobile testing

## 17 Optimization & Scaling

- Laravel Scout + Meilisearch for full-text search
- Indexing critical DB fields
- Lazy loading / virtual lists
- Load more vs pagination toggles

## 18 Deployment & DevOps

- Configure Laravel Forge / VPS / Docker
- CI / CD pipelines
- SSL, caching, queue workers
- Daily backups & log pruning
- Monitoring (Telescope, Sentry, etc.)

# Second Vintage CRM – A to Z Implementation Overview

## Phase 1: Project Foundation

### Project Setup & Scaffolding

- Initialize Laravel 11 project
- Setup Laravel Breeze or Jetstream (SPA + API auth)
- Setup frontend stack (React + Vite + TailwindCSS)
- Configure `.env` (DB, Mail, Queue, Storage)
- Install core packages:
    - `spatie/laravel-permission` (roles/permissions)
    - `laravel/scout` + Meilisearch (search)
    - `maatwebsite/excel` (CSV import/export)
    - `laravel/horizon` (queues)
    - `intervention/image` (image resize)
- Setup git repo + CI workflow (GitHub, GitLab, etc.)

### Database Design

- Create migrations for:
    - users, roles, permissions
    - watches, batches, brands, locations
    - transactions, platform_data, statuses, stages
    - watch_images, watch_logs, descriptions
    - wishlist_items, settings, job_statuses
- Add foreign keys and indexing
- Run seeders for initial roles, statuses, platforms

---

## Phase 2: Authentication & Access Control

### Authentication

- Email/password login + forgot/reset
- Sanctum API auth for frontend
- Redirect by role to proper dashboard

### Role & Permission System

- Roles: Admin, Manager, Finance, Agent, Seller
- Permissions: CRUD by module
- Middleware and Gates for route/component protection
- Role-based menu and access UI

---

## Phase 3: Core Modules Implementation

### Watch Management Module

- CRUD: Add/Edit/Delete/Clone Watches
- Dynamic dropdowns: Brand, Location, Batch
- Image upload (max 40), naming by SKU
- AI: “Generate Description”, “Reset AI”
- Async queue system for Make.com
- Previous/Next watch navigation
- SKU generation logic
- Full search, pagination, stress test 10,000+

### Multi-Platform Sales Module

- Per-platform data fields (Catawiki, Tradera, eBay)
- “Fill out data with AI” function
- CSV export: Catawiki template
- API integrations:
    - Tradera: item creation
    - eBay: listing integration
- Approve/Next watch navigation
- Platform status + ready for listing

### Batch Management

- Create/Edit Batches
- Default destination: Denmark
- Status: “In Transit”, “Delivered” → update watch locations
- DHL API integration
- Grid/List toggle + filters

### Sales History / Statistics

- Import Excel/CSV from Catawiki
- Match on SKU
- Extract buyer data (name, price, location, etc.)
- Update related watches
- Full-text search and pagination

### Wish List Module

- Create/Edit Wish Items
- Image upload + resize to 1024x1024
- Show local currency + Euro
- Grid/List toggle + filters
- 10,000+ records support

### Agent Balance Module

- View balances by agent (Admin only)
- Add Deposit/Payment
- Payment types: Watches, Shipping, Watchmaker, Fee, Bonus
- Watch-linking if "Watches" type
- Refunded watches don’t affect balance

### Agent Watches Module

- CRUD for agent watches
- Image uploads (40 max)
- Agent dropdown visible to Admin only
- Editable rules for Seller-created watches

### Seller Watches Module

- Seller default currency only
- Edit restrictions (e.g., Agent dropdown only for Admins)
- 10,000+ entries support

### Users Module

- Admin-only access
- CRUD users + reset passwords
- Role assignment + permission validation
- Search and pagination

### Full Data View

- Global Watch search with filter
- Export filtered data to CSV
- Admin-only access

### Log Module

- Show all activity logs
- Include AI error logs
- Filter by watch, user, action
- Pagination + index support

### Settings Module

- General settings (name, color themes)
- Integration settings (Make, DHL)
- Appearance fix for dark mode
- Notification system (future-ready)

---

## Phase 4: Integration & Async Jobs

### Make.com AI Integration

- Webhook POST request (form JSON + images)
- Async tracking:
    - Option 1: local job map
    - Option 2: polling
- Match response to watch via `threadId` or `watchId`
- Update description + log response

### DHL Integration

- Batch status → call DHL webhook/API
- Auto update tracking info

### eBay & Tradera Integration

- Auth via API keys
- Watch listing via endpoint
- Log response + errors

---

## Phase 5: Data Flow & Import/Export

### CSV / Excel Export

- Export platform data (Catawiki template)
- Format: SKU, brand, description, image URLs, etc.

### CSV / Excel Import

- Import Catawiki sales report
- Match Reference Label → SKU
- Update Watch with buyer, country, ISO, etc.

### Image File Handling

- Store images as:
    - `WatchImages/SKU/001.jpg`
    - `WatchImages/SKU/001_s.jpg`
- Accessible via public URL (`php artisan storage:link`)
- Use Intervention Image to resize

---

## Phase 6: Testing, Optimization & Deployment

### Stress Testing

- Seeders for 10K+ watches, batches, users
- Load testing for search, listing, queues

### Testing

- Unit Tests (PHPUnit)
- Feature Tests (Form Requests, Queues)
- Frontend tests (Jest / React Testing Library)

### Performance Optimization

- Indexing on SKU, watch_id, user_id, platform
- Chunked import
- Job queue monitoring via Laravel Horizon
- Laravel Scout + Meilisearch

### Deployment & Monitoring

- Docker setup / Laravel Forge
- Redis for queue/cache
- Telescope for API monitoring
- Sentry for error tracking
