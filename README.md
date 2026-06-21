# EstateCore ERP Backend

A Laravel + MySQL backend for EstateCore ERP.

## Requirements

- PHP & Composer
- MySQL

## Installation

**1. Install dependencies**

```bash
cd estatecore-backend
composer install
```

**2. Configure environment**

```bash
cp .env.example .env
php artisan key:generate
```

**3. Set up the database**

Create a MySQL database named `estatecore_backend`, then open `.env` and update the connection settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=estatecore_backend
DB_USERNAME=root
DB_PASSWORD=
```

**4. Run migrations**

```bash
php artisan migrate
```

**5. Start the development server**

```bash
php artisan serve
```

## Documentation

- [API Documentation](DOCUMENTATION.md)

## Contact

[![Email](https://skillicons.dev/icons?i=gmail)](mailto:dev.sasmitha@gmail.com)