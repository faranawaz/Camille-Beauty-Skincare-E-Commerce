# Camille Beauty Skincare E-Commerce

A web-based skincare e-commerce application developed as a college project.

## Tech stack
- PHP
- MySQL
- HTML, CSS, and JavaScript
- Composer
- vlucas/phpdotenv

## Project setup

1. Install PHP, MySQL, and Composer.
2. Clone or download this repository.
3. Install PHP dependencies:

   ```bash
   composer install
   ```

4. Copy `.env.example` to `.env`.
5. Fill in your own local database credentials in `.env`.
6. Configure your local web server to use this project directory.
7. Import your own database schema/data if available.

## Environment variables

The real `.env` file is intentionally excluded from Git. Do not commit database passwords, API secrets, access tokens, or other credentials.

## Notes

- The `vendor/` directory is excluded; Composer recreates it using `composer install`.
- Private/user-generated upload folders (profile images, payment proofs, and shipping proofs) are excluded from this portfolio copy.
- Product, brand, category, and banner assets are retained where present.
- This repository archive does not include a database export. You will need your own database schema/data to run the application.
