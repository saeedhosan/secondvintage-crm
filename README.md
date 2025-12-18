<p align="center">
  <a href="#overview">
    <img alt="status" src="https://img.shields.io/badge/Status-Case Study-blue" />
  </a>
  <a href="#license">
    <img alt="source" src="https://img.shields.io/badge/Soruce-Private-red" />
  </a>
  <a href="https://secondvintage.com">
    <img alt="Ownership" src="https://img.shields.io/badge/Ownership-Secondvintage-orange" />
  </a>
  <a href="#tech-stack">
    <img alt="Tech Stack" src="https://img.shields.io/badge/Tech-Laravel+React-brightgreen" />
  </a>
  <a href="#testing--quality">
    <img alt="Testing and quality" src="https://img.shields.io/badge/Tests-PHPUnit-blueviolet" />
  </a>
</p>

> **Note**: This repository is provided solely for **case study and portfolio review**. All source code and project materials are private and owned by SecondVintage.com (c) 2025 and not licensed for reuse.

## Table of Contents

-   [Overview](#overview)
-   [Challenge / Problem Solved](#challenge--problem-solved)
-   [Solution](#solution)
-   [Features](#features)
-   [Tech Stack](#tech-stack)
-   [Results & Impact](#results--impact)
-   [Installation & Usage](#installation--usage)
-   [Architecture Evidence](#Architecture-Evidence)
-   [Testing & Quality](#testing--quality)
-   [Author & Contact](#author--contact)
-   [License](#license)

## SecondVintage CRM - Watch Management Platform

![Full-stack-development-crm](/example.png)

## Overview

The **SecondVintage CRM** is a high-performance, specialized, and data-intensive **Customer Relationship Management (CRM) and Inventory System** built for the vintage watch market. It is architected as a **Full-stack application**, utilizing a **Laravel for backend** with a **React + Inertia.js for frontend**.

**What:** A centralized, scalable platform for managing a large volume of high-value vintage watch inventory and synchronizing sales across multiple external auction platforms (e.g, Catawiki, Tradera).

**Why & Problem Solved:** Developed to solve critical issues around **fragmented data integrity**, slow performance at high scale (80k+ items), and the excessive manual effort required for listing, logistics, and financial tracking across disparate third-party services.
**Purpose:** To automate complex workflows, maintain data consistency, centralize financial reporting, and ensure a highly responsive user experience despite large datasets and asynchronous API interactions.

## Challenge / Problem Solved

The core challenge was engineering a robust, high-availability system capable of handling the **scale and complexity** of managing high-value assets with diverse external dependencies:

-   **High-Volume Data Integrity:** Safely managing a rapidly growing dataset of **80k+ watch records**, ensuring consistency across financial transactions, inventory counts, and platform-specific listing fields (e.g., Catawiki's 40+ required fields).
-   **Decoupled Multi-Platform Integration:** Designing a resilient mechanism to interact with various third-party services (DHL for logistics, Tradera API for listing, Make.com for AI generation) that require **asynchronous processing** and rigorous logging to prevent data loss or service disruption.
-   **Performance at Scale:** Maintaining sub-second response times for complex queries, full-text search, and large CSV imports/exports, essential for operational efficiency.

## Solution

The system utilizes a **modern, decoupled architecture** to ensure long-term scalability, maintainability, and reliability, adhering to best-in-class development practices.

### Architectural Foundation

The separation of the **Laravel Backend** and the **React/Inertia Frontend (SPA)** provides clear **separation of concerns**, allowing independent scaling and development. Laravel session-based authentication to secure the application.

### Asynchronous Processing and Scalability

-   **High-Volume Queuing:** All resource-intensive tasks—including **AI Description Generation** (via webhooks to Make.com), **API Synchronization** (Catawiki/Tradera), and large **CSV Imports export** are processed asynchronously using **Laravel Queues (Redis Driver)**. This prevents blocking the web server and guarantees a snappy user experience.
-   **Queue Monitoring:** Database log driver is implemented to provide real-time visibility, monitoring, and automated retry management for all queued jobs, ensuring high reliability of background processes.

### Security and Data Management

-   **Role-Based Access Control (RBAC):** We utilize the **Spatie Laravel Permission** package to define granular user roles (Admin, Manager, Finance, Agent, Seller) and enforce security via **Laravel Gates and Policies**, controlling access to sensitive financial and inventory modules.
-   **Optimized Database:** MySQL tables are meticulously designed with essential **Foreign Key Constraints** and strategic indexing (`sku`, `watch_id`, `user_id`) to optimize join queries and read performance at scale.

## Features

-   **Intelligent Inventory Management:** Robust CRUD for max **40 high-resolution images per watch**, automated unique SKU generation, and clear tracking across customizable `stages` and `statuses`.
-   **Asynchronous AI Content Generation:** Integrates via queued jobs & Broadcasting with **Make.com** webhooks for professional watch AI descriptions, enhancing listing efficiency.
-   **Multi-Platform Sales Management:** Dedicated `platform_data` schema (JSON) to store platform-specific listing attributes, enabling seamless **CSV export for Catawiki** and direct **API integration for Tradera**.

Developed by team members

-   **Agent & Seller Financials:** Comprehensive module for tracking **Deposits** and **Payments** with detailed transaction history, enabling accurate calculation of real-time agent/seller balances.
-   **Logistics Automation:** **Batch management** for grouping shipments with **DHL API integration** to provide automated tracking and logistics status updates.

## Tech Stack

| Category       | Technology                                     |
| :------------- | :--------------------------------------------- |
| **Backend**    | Laravel, PHP ^8.2,                             |
| **Frontend**   | React, Inertia.js, Tailwind CSS,               |
| **Database**   | MySQL, SQLite                                  |
| **Security**   | RBAC, Laravel Gates Policies                   |
| **AI/LLM**     | Make.com Webhook, Queue for async and optimize |
| **Realtime**   | Laravel broadcasting with Pusher               |
| **Monitoring** | Telescope, database logger, event-driven logs  |
| **Utilities**  | routes (ziggy), spatie query, Excel (Maatwebsite)|

## Results & Impact

The implementation of the **SecondVintage CRM** delivers critical business value:

-   **60% Reduction in Listing Time:** Automation of AI content generation and multi-platform data preparation significantly cuts manual data entry.
-   **Guaranteed Data Consistency:** Centralized management and strict validation rules ensure integrity across sales platforms and financial records.
-   **Enterprise Scalability:** The decoupled architecture and reliance on asynchronous processing prove the system's readiness to manage inventory scaling beyond 80k+ items without performance degradation.

## 🏗️ Architecture Evidence

The **Second Vintage CRM** is engineered as a high-performance inventory and sales management engine for luxury assets. It centralizes the lifecycle of high-value products—from intake and AI-enhanced enrichment to multi-platform distribution—within a single source of truth.

### 1. The Core Logic: Asset Lifecycle
The system replaces manual data entry with an automated, high-volume pipeline designed for luxury goods:

* **Intake & Batching:** Assets are organized into `Batches` (e.g., by shipment or category). This allows for bulk status updates, location tracking, and mass processing.
* **AI Enrichment Engine:** Integrated via **Make.com**, the system triggers automated webhooks to analyze product images and generate professional technical descriptions and metadata.
* **Multi-Platform Distribution:** A specialized distribution layer pushes validated assets to external marketplaces like **Catawiki**, **Tradera**, and **eBay** via dedicated API bridges and CSV exports.
* **Financial Integrity:** Real-time profit margins are calculated by linking every asset to a `Transaction` ledger that accounts for purchase costs, multi-currency exchange rates, and platform fees.

### 2. High-Level System Flow
The architecture utilizes a "Request-Queue-Notify" pattern. Long-running tasks like AI generation and marketplace syncing are handled asynchronously to ensure a zero-latency user experience.

```mermaid
graph TD
    A[Inventory Intake] --> B{Context Resolver}
    B -->|Batch Processing| C[Data Enrichment]
    C -->|Webhook| D[AI Description Engine]
    D --> E[Quality Control]
    E -->|API Sync| F[External Marketplaces]
    F --> G[Sales & Profit Analytics]
```

### 3. Core Database Architecture
The schema is optimized for 10k+ records, utilizing a relational model that prioritizes auditability, financial precision, and sub-second query speeds.

| Table | Primary Purpose |
| :--- | :--- |
| **`watches`** | The central asset repository containing technical specs, metadata, and status. |
| **`batches`** | Logical grouping for shipment tracking and bulk status updates. |
| **`transactions`** | Immutable financial ledger for deposits and payments linked to specific assets. |
| **`platform_data`** | Stores listing requirements for external APIs (Catawiki, Tradera, etc). |
| **`watch_images`** | Manages high-resolution storage paths and auto-generated thumbnails. |

[DIAGRAM PLACEHOLDER: I will update the Database ERD diagram here later]

### 4. Technical Implementation & Scalability
* **Scoped Access (RBAC):** Strict role-based boundaries (Admin, Finance, Agent, Seller) ensure financial data and administrative settings are only visible to authorized personnel.
* **Async Pipeline:** Integration with **Laravel Horizon** and Redis ensures that heavy API and AI tasks do not block the main application thread.
* **Data Accuracy:** A dedicated `conversion_rates` engine handles multi-currency transactions, ensuring all profit reporting remains accurate across different markets.
* **Search Optimization:** Uses **Meilisearch** for full-text search across the inventory catalog, providing instant results even as the dataset grows.

---

## Installation & Usage

> Restricted to the team member.

-   **Clone:** `git clone [repo].`
-   **Initialize:** `composer setup` _or run setup scripts manually_
-   **Run Application:** `php artisan serve` _or_ `composer run dev`
-   **View Application:** [http://127.0.0.1:8000]

## Testing & Quality

Reliable software is developed quickly through strong testing and fast feedback loops.

-   Feature and unit tests are written using PHPUnit and Pest.
-   PHPStan is used for strict type checking and early issue detection.
-   Laravel Pint keeps the codebase clean, consistent, and easy to maintain.

*   Stress testing is performed using custom seeders to populate the database with **10,000+ realistic records** to validate search, pagination, and queue performance under expected production loads.

```bash
php artisan test      # Laravel tests
composer lint         # Pint lint
composer types        # PHPStan type checks
composer format       # Rector code formatting
composer test         # run all checks
```

## Author & Contact

**Saeed Hosan**  
Email: appsaeed7@gmail.com  
LinkedIn: https://www.linkedin.com/in/saeedhosan

## License

This portfolio documentation is licensed under Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International (CC BY-NC-ND 4.0). This means the content cannot be modified or used for commercial purposes.

All underlying source code, proprietary algorithms, and confidential project data remain the exclusive Intellectual Property of SecondVintage.com (c) 2025. No rights or licenses to the former employer’s protected assets are granted through this documentation.
