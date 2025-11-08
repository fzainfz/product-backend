# Laravel Project

A brief description of your project goes here.

---

## Requirements

- php: ^8.2,
- laravel/framework: ^12.0
- Composer
- MySQL


---

## Installation

1. **Clone the repository**

```bash
git clone https://github.com/fzainfz/product-backend.git
cd your-repo

2. Install PHP dependencies
   composer install
3. Set up environment variables
   APP_NAME=LaravelApp
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

4. Generate application key
   php artisan key:generate
5. Run database migrations
   php artisan migrate
6. Seed the database
   php artisan db:seed
7. Generate Storage Link
   php artisan storage:link
8. Generate Swagger Link
   php artisan l5-swagger:generate
9. Generate JWT Secret Key
   php artisan jwt:secret
10. Run the development server
   php artisan serve
   http://localhost:8000
11. open swagger api documents.
   http://localhost:8000/api/documentation


