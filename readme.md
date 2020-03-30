# _PayU_
---
## Server Requirements

* PHP >= 7.2.*
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Ctype PHP Extension
* JSON PHP Extension


## Getting this up and running

### 1. _Cloning the project_
* CD into your working directory
* Open Terminal and run : git clone https://github.com/ascodelab/payU-Mac-Test
* cd payU-Mac-Test
### 2. Installing Laravel
* composer install
### 3. Installing frontend dependencies 
* npm install
* npm run dev
### 4. Database Configuration
edit .env and set database credentials ( Create database named payu_test)

    DB_DATABASE=payu_test
    DB_USERNAME=anil
    DB_PASSWORD=123456

### 5. Clear laravel cache

    php artisan config:cache


### 6. Importing Database ( We must use migrations/seeding but this is just a POC)

    Import : sql_dump.sql

### 7. Run Laravel application

    $ php artisan serve


### 8. Acces the url

open brower and visit: https://localhost:8000/login and login using provided credentials.

### 9. Thirdparty libraries used

1. Jquery
2. CSS Bootstrap
3. Datatables





