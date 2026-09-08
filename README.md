# The Refillery SA — Zero-Waste Online Store

A full-stack B2C e-commerce website for a zero-waste household & personal care products store.
Individual capstone project — Course 3: eCommerce Development (YouthCode Off-Site Programme).
**Project owner: Shuaib Darries**

## Project Description
The Refillery SA lets customers register, log in, browse a database-driven product catalogue,
add items to a cart, check out through a **simulated PayFast payment flow**, and receive an
order confirmation. All customer, product, and order data is stored live in a MySQL database —
no static/dummy data is hardcoded into the site.

## Technologies Used
| Layer      | Technology |
|------------|------------|
| Front-end  | HTML5, CSS3, Bootstrap 5, vanilla JavaScript |
| Back-end   | PHP 8 (PDO), sessions |
| Database   | MySQL / MariaDB |
| Version control | Git + GitHub |

## Setup Instructions (Local)
1. **Install a local server stack** — XAMPP (or WAMP/MAMP). Ensure Apache and MySQL run.
2. **Create the database** — open phpMyAdmin (`http://localhost/phpmyadmin`), go to the
   **Import** tab, and import `database/refillery.sql`. This creates the `refillery_db`
   database, all tables, and seed product data.
3. **Configure environment variables** — copy `.env.example` to `.env` in the project root
   and update the values if your MySQL credentials differ from the defaults:
   ```env
   DB_HOST=localhost
   DB_NAME=refillery_db
   DB_USER=root
   DB_PASS=
   ```
4. **Move the project** into your web root (e.g. `C:\xampp\htdocs\refillery-sa`).
5. **Open the site** at `http://localhost/refillery-sa/`.

## Key Pages & Their Purpose
| Page | Purpose |
|------|---------|
| `index.php` | Product catalogue with live search & category filtering |
| `product.php` | Single product detail with add-to-cart |
| `register.php` | Customer account creation (passwords hashed with `password_hash`) |
| `login.php` / `logout.php` | Session-based authentication |
| `cart.php` | Shopping cart (update quantities, remove items, totals) |
| `checkout.php` | Shipping details + order review (validated input) |
| `payment.php` | Simulated PayFast gateway — clear success/failure states |
| `order_confirmation.php` | Confirmation with persisted order number & summary |
| `account.php` | Customer order history |

## Payment Approach
The brief allows a real or simulated payment system. I chose a **simulated PayFast flow** as the
core implementation because it keeps the project fully testable offline while mirroring the real
gateway's user experience: the customer is redirected to a "PayFast (Simulated)" page, enters mock
card details, and the system randomly approves or declines the transaction with clear messaging —
declined payments return the customer to checkout without losing their cart; approved payments
insert the order into the database and show a confirmation. Integrating a live gateway (PayFast/Ozow)
is planned as a bonus enhancement using the same checkout hand-off point.

## Security Notes
- Passwords are hashed with `password_hash()` (bcrypt) — never stored in plain text.
- All SQL uses PDO prepared statements (protection against SQL injection).
- All forms are validated server-side with clear error messages.
- Database credentials are read from `.env`, which is excluded from Git via `.gitignore`.

## Author
Shuaib Darries — YouthCode Off-Site Programme, Course 3 Core Project (2026).
