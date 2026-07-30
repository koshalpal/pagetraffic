Prerequisites
PHP 8.2+
Composer
PostgreSQL
PHP extensions: pdo_pgsql, pgsql, mbstring, openssl, curl
Steps
1. Clone the repo

git clone git@github.com:koshalpal/pagetraffic.git
cd pagetraffic
2. Install PHP dependencies

composer install
3. Create env file and app key

cp .env.example .env
php artisan key:generate
4. Create the database

PGPASSWORD=postgres psql -h 127.0.0.1 -U postgres -p 5432 -c "CREATE DATABASE pagetraffic;"
5. Update .env

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pagetraffic
DB_USERNAME=xxxx
DB_PASSWORD=xxxx
VALUESERP_API_KEY=AC9F01B6DADA44B894984E5B55ED48F9
VALUESERP_BASE_URL=https://api.valueserp.com
6. Run migrations

php artisan migrate
7. Start the server

php artisan serve
Open http://127.0.0.1:8000