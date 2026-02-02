# E-Beauty - Online Beauty Product Catalog

<p align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

## About The Project

**E-Beauty** is a modern web-based beauty product catalog application built with **Laravel 10** and **Bootstrap 5**. It helps customers find the perfect beauty products tailored to their needs and allows administrators to manage the inventory efficiently.

### Key Features
*   **Customer Side**:
    *   Browse products with advanced filters (Category, Brand, Skin Type, Price).
    *   Detailed product information (Ingredients, How to Use).
    *   User Authentication (Login/Register).
    *   Product Reviews and Ratings.
*   **Admin Side**:
    *   Dashboard with key statistics.
    *   Product, Brand, and Category Management (CRUD).
    *   Stock Management & Low Stock Alerts.
    *   Review Moderation.
    *   Promo & Discount Management.

### Tech Stack
*   **Backend**: Laravel 10 (PHP 8.2+)
*   **Frontend**: Blade Templates, Bootstrap 5.3
*   **Database**: MySQL
*   **Server**: Local Development (Laragon/XAMPP)

---

## Installation Guide

Follow these steps to set up the project locally.

### Prerequisites
*   [PHP](https://www.php.net/downloads.php) >= 8.1
*   [Composer](https://getcomposer.org/)
*   [MySQL](https://www.mysql.com/)

### Steps

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/Shiroizx/E-Beauty.git
    cd E-Beauty
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Environment Setup**
    *   Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    *   Configure your database credentials in `.env`:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=e_beauty
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

5.  **Database Migration & Seeding**
    *   Run migrations and seed the database with sample data:
        ```bash
        php artisan migrate --seed
        ```

6.  **Link Storage**
    *   Create a symbolic link for storage access:
        ```bash
        php artisan storage:link
        ```

7.  **Run the Application**
    ```bash
    php artisan serve
    ```
    Access the app at: `http://127.0.0.1:8000`

### Default Credentials

*   **Admin**:
    *   Email: `admin@ebeauty.com`
    *   Password: `password`

*   **Customer**:
    *   Email: `customer@example.com`
    *   Password: `password`

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
