# TrafficMapper POC

A minimal Proof-of-Concept in Laravel 11 that maps traffic parameters (`keyword`, `src`, `creative`) to an opaque `our_param`, supporting affiliate redirects, reverse lookups, forced refresh, caching, versioning, validation, and structured logging.

---

## ✨ Features

SPA with Laravel + React + Inertia

Single Page Application for smooth UX

Fully dynamic frontend with Vite

✅ Admin Dashboard

Visual stats: success/error count, action-based breakdown (redirect, retrieve, refresh)

Interactive charts: Pie, daily bars, and stacked per-action types

Drill-down on failures with modal popups showing full params and metadata

✅ Scalable Logging

request_logs table capturing: endpoint, params, action, ip, user_id, status, and timestamp

Asynchronous logging via Laravel Queues (LogApiRequest job)

Designed for scaling to ClickHouse or ElasticSearch

✅ Mapping Management

Unique our_param for reverse lookup

Mapping model with full validation and introspection


---

## 📋 Prerequisites

* PHP 8.2
* Composer
* MySQL (or compatible) database server
* \[Optional] Docker & Docker Compose

---

## ⚙️ Environment Setup

1. **Clone the repository**

   ```bash
   git clone https://github.com/Freedomnet2000/traffic-mapper.git
   cd traffic-mapper
   ```
2. **Install dependencies**

   ```bash
   composer install --optimize-autoloader
   ```
3. **Environment variables**

   * Copy example file:

     ```bash
     cp .env.example .env
     ```
   * Update `.env` with your local database credentials and settings:

     ```dotenv
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=traffic_mapper
     # Production affiliate endpoint:
     # AFFILIATE_URL=https://affiliate-network.com
     # Local/mock affiliate endpoint:
     AFFILIATE_URL=http://localhost/traffic-mapper/public/mock-affiliate
     CACHE_DRIVER=array
     MAPPING_LOG_LEVEL=info
     ```

---

## 🚀 Database Initialization

1. **Create the database**

   * **Manually** in your DB server:

     ```sql
     CREATE DATABASE traffic_mapper;
     ```
   * **Or via Docker** (if using Docker Compose): ensure the DB service is up before migrating.

2. **Run migrations**

   ```bash
   php artisan migrate
   ```

---

## 📑 API Endpoints

| Method | Endpoint                             | Parameters                            | Success Response                                                             | Error Response                    |
| ------ | ------------------------------------ | ------------------------------------- | ---------------------------------------------------------------------------- | --------------------------------- |
| GET    | `/redirect`                          | Query: `keyword`, `src`, `creative`   | **302** Redirect with `Location` header: `<AFFILIATE_URL>?our_param=<value>` | **400** validation errors as JSON |
| GET    | `/api/retrieve_original/{our_param}` | Path: `our_param` (6–12 alphanumeric) | **200** JSON: `{ "keyword": "...", "src": "...", "creative": "..." }`        | **422** validation or not found   |
| POST   | `/api/refresh`                       | JSON body: `{ "our_param": "..." }`   | **200** JSON: `{ "new_param": "..." }`                                       | **422** validation or conflict    |
| POST   | `/api/pingback`                       | JSON body: `{ "track_id": "..." }`   | **200** JSON: `{ "OK" }`                                       | **422** validation or conflict    |

---

## 🧪 Running Tests

```bash
# Run all tests
php artisan test

# Run specific test class
php artisan test --filter=RetrieveMappingValidationTest
```

---

## 🛠️ Future Improvements & Extensions


✅ API Security Enhancements

Restrict access based on IP whitelist

Apply rate limiting per endpoint and IP address

🎨 Dashboard UI Enhancements

Add usage statistics charts (e.g., request types, traffic sources)

Enable filtering and highlighting of anomalies (e.g., repeated failures, unknown sources)

📈 Advanced Logging and Data Analytics

Integrate with ClickHouse or ElasticSearch for scalable log analysis

Add a unique trace_id per request for easier debugging and tracing

🔄 Multi-Version Support

Allow retrieval of previous versions of our_param

Support comparing and rolling back to earlier versions
