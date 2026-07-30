# PageTraffic SERP — Laravel Coding Test

Laravel application that queries the [ValueSERP](https://www.valueserp.com/) API for multiple keywords, displays organic results (title, link, snippet), and exports them to CSV.

## Requirements

- PHP 8.2+
- Composer
- PostgreSQL
- PHP extensions: `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `curl`

## Setup

1. Clone the repository and install dependencies:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

2. Configure PostgreSQL in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pagetraffic
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

3. Create the database (if it does not exist):

```bash
createdb -U postgres pagetraffic
# or: psql -U postgres -c "CREATE DATABASE pagetraffic;"
```

4. Add your ValueSERP API key:

```env
VALUESERP_API_KEY=your_api_key_here
VALUESERP_BASE_URL=https://api.valueserp.com
```

5. Run migrations:

```bash
php artisan migrate
```

6. Start the app:

```bash
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Usage

1. Enter 1–5 search queries on the home page.
2. Click **Run Search** — each query is sent to ValueSERP and organic results are stored.
3. Review titles, links, and snippets on the results page.
4. Click **Download CSV** to export all aggregated results for that search batch.

CSV columns: `Query`, `Position`, `Title`, `Link`, `Snippet`, `Displayed Link`.

## Features

- Multi-query search (up to 5 keywords)
- ValueSERP API integration with error handling
- Input validation (length, characters, duplicates)
- Results persisted in PostgreSQL
- CSV export of aggregated results
