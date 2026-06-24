# 🏪 Mazaya Mall — Enterprise E-Commerce Solution

[![PHP Platform](https://img.shields.io/badge/PHP-%E2%89%A5%207.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![Database Engine](https://img.shields.io/badge/MySQL-%E2%89%A5%205.7-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/doc/)
[![Frontend Framework](https://img.shields.io/badge/Bootstrap-v5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](https://opensource.org/licenses/MIT)

A premium, high-performance, and fully responsive enterprise e-commerce system tailored for managing **household tools, plastics, and home furnishings**. The platform features a seamless shopping experience for customers and a secure, powerful administration control panel.

[Features](#-features) • [Screenshots](#-screenshots) • [Installation](#-installation) • [Usage](#-usage) • [Database](#-database) • [Structure](#-structure) • [License](#-license)

---

## ✨ Features

| Feature | Description | Status |
| :--- | :--- | :---: |
| 🔐 Secure Authentication | Session-based secure admin access with absolute separation of routing contexts. | Ready |
| 📝 Full CRUD Operations | Complete Create, Read, Update, and Delete products and categories seamlessly. | Ready |
| 🎨 Premium UI Design | Modern dashboard, dynamic responsive grid, and native Arabic RTL layout support. | Ready |
| 📊 Live Dashboard | Real-time administrative statistics mapping total orders, products, and categories. | Ready |
| 🛒 Smart Cart System | Client-side state persistence driven completely by optimized `LocalStorage` pipelines. | Ready |
| 📦 Status Tracking | Live transactional workflow tracking (Pending → Processing → Completed). | Ready |
| 🛡️ Architecture Security | Native parameters binding guarding database layers against malicious exploits. | Ready |
| 📱 Mobile Responsive | Multi-device fluid adaptation ensuring high conversion metrics on all viewpoints. | Ready |

---

## 📸 Screenshots

### 🏠 Dashboard
Real-time stats & order telemetry summary indicators.

### 🛒 Product Catalog
Categorized directory with responsive item view layouts.

### 🔐 Admin Gateway
Secure session-based portal access control form.

---

## 🛠️ Tech Stack

* **Frontend:** Bootstrap v5.3 Framework | Font Awesome v6.4 Vector Library | Google Fonts (Tajawal, Cairo) | Custom CSS Grid
* **Backend:** PHP v7.4+ Production Runtime Engine | Stateful Authentication Middleware
* **Database Layer:** MySQL v5.7+ Engine | Prepared SQL Statements Precompilation Architecture
* **Security Baselines:** SQL Injection Prevention | Dynamic Contextual Escaping (XSS Protection) | Session Isolation

---

## ⚡ Quick Start

### Prerequisites
* PHP `7.4` or higher with active database extensions.
* MySQL `5.7` or higher / MariaDB Server.
* Apache Web Server with `mod_rewrite` enabled (`.htaccess` support).
* Modern web browser with client-side JavaScript execution enabled.

### 1. Clone & Setup
Move the project to your local web root server directory (e.g., `htdocs` for XAMPP or `/var/www/html/` for Linux):

```bash
# Move into server target directory
cd /path/to/webroot

# Extract production distribution package
unzip mazaya_mall_final.zip
cd mazaya_mall
```

Alternatively, test or launch locally via the built-in server engine:

```bash
php -S localhost:8000
```

### 2. Relational Schema Provisioning
Create a dedicated relational data database wrapper and execute the structural schema injection script:

```sql
-- Compile main e-commerce database container
CREATE DATABASE mazaya_mall CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mazaya_mall;

-- Table Structure: Product Categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    cover VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table Structure: Product Listings
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table Structure: Customer Invoices/Orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone1 VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table Structure: Individual Relational Order Items
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table Structure: Backoffice Administrative Credentials
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Alternatively, you can automate this by executing the script in your browser:

```
http://localhost/mazaya_mall/setup.php
```

### 3. Connection Configuration Bridge
Configure the low-level infrastructure properties inside `config/db.php`:

```php
<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';         // Infrastructure Username
$DB_PASS = '';             // Infrastructure Password
$DB_NAME = 'mazaya_mall';   // Database Target System Identifier

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Data Core Connection Interrupted: " . $conn->connect_error);
}
?>
```

### 4. Direct Upload Permissions

```bash
# Initialize target location for product assets binaries
mkdir -p uploads
chmod 755 uploads
```

### 5. Launch & Authentication Details 🚀

**Storefront URL:** `http://localhost/mazaya_mall/`

**Administrative Control Panel URL:** `http://localhost/mazaya_mall/admin/login.php`

**Default Administrative Username:** `admin`

**Default Administrative Password:** `admin123`

---

## 📖 Operational Usage Guide

### Application Access Flow Layout

```
  ┌────────────────┐       ┌────────────────┐       ┌────────────────┐
  │   index.php    │──────>│   cart.php     │──────>│  checkout.php  │
  │ (Marketplace)  │       │ (Client Cart)  │       │(Invoice Entry) │
  └────────────────┘       └────────────────┘       └───────┬────────┘
                                                            │
                           ┌────────────────┐       ┌───────v────────┐
                           │admin/orders.php│<──────│  success.php   │
                           │(Backoffice Ops)│       │ (Receipt View) │
                           └───────┬────────┘       └────────────────┘
                                   │
                           ┌───────v────────┐
                           │admin/dashboard.php
                           │(Telemetry Hub) │
                           └────────────────┘
```

### Product Catalog Execution Lifecycle

- **Catalog Browsing:** End-users interact with `index.php` or filter items dynamically using categorical tags in `products.php`.
- **Cart Accumulation:** Items pass instantly into the user's LocalStorage database core from `product.php` details page.
- **Checkout Validation:** The client fills out delivery metadata inside `checkout.php`, which transmits transactional payloads via asynchronous structures directly to `api/orders.php`.

---

## 🗄️ Normalized Database Schema Design

```
  ┌────────────────────────┐               ┌────────────────────────┐
  │       categories       │               │        products        │
  ├────────────────────────┤               ├────────────────────────┤
  │ id (PK)          [INT] │<──┐           │ id (PK)          [INT] │
  │ name         [VARCHAR] │   │           │ name         [VARCHAR] │
  │ slug         [VARCHAR] │   └───────────┼ category_id (FK) [INT] │
  │ cover        [VARCHAR] │          1:M  │ price          [DECIMAL] │
  └────────────────────────┘               │ image        [VARCHAR] │
                                           └───────────┬────────────┘
                                                       │ 1
                                                       │
                                                       │ M
  ┌────────────────────────┐               ┌───────────v────────────┐
  │         orders         │               │      order_items       │
  ├────────────────────────┤               ├────────────────────────┤
  │ id (PK)          [INT] │<──┐           │ id (PK)          [INT] │
  │ phone1       [VARCHAR] │   │           │ order_id (FK)    [INT] │
  │ address         [TEXT] │   └───────────┼ product_id (FK)  [INT] │
  │ city         [VARCHAR] │          1:M  │ quantity         [INT] │
  │ status          [ENUM] │               └────────────────────────┘
  └────────────────────────┘
```

---

## 📁 Repository Directory Blueprint

```
mazaya_mall/
├── admin/                 # Backoffice control codebase directories
│   ├── dashboard.php      # Business analytics data view container
│   ├── login.php          # Admin portal access controller gateway
│   ├── logout.php         # Token clearing logic loop script
│   ├── products.php       # Core inventory CRUD grid template layout
│   ├── orders.php         # Logistics and delivery tracking router
│   └── includes/          # Backoffice micro structural snippets
│       ├── auth.php       # Middleware session lifecycle validation guard
│       └── sidebar.php    # Administrative dashboard component sidebar navigation
├── api/                   # Async endpoints handlers root
│   └── orders.php         # Core gateway logic for processing checkout operations
├── assets/                # Static assets layout structures pipeline
│   ├── css/               # Modular stylesheet architecture distributions
│   ├── js/                # Active application cart management controllers
│   └── images/            # Standard design icons package
├── config/                # Environment configurations variables
│   ├── db.php             # Low-level system driver parameters settings pool
│   └── helpers.php        # String sanitation engine utilities filter
├── includes/              # Shared multi-view customer UI headers & footers
├── uploads/               # Dynamic directory structure archiving product binaries
├── index.php              # Public customer entrance directory view
├── products.php           # Catalog filtration matrix distribution system
├── product.php            # Contextual item renderer mapping dynamic inventories
├── cart.php               # Local storage processing shopping cart view
├── checkout.php           # High efficiency order checkout form setup
├── success.php            # Final verified invoice generation template view
└── database.sql           # Raw schema creation scripts container
```

---

## 🛡️ Security Feature Matrix

| Attack Surface Threat Vector | Project Mitigation Security Strategy Implementation |
| :--- | :--- |
| SQL Injection (SQLi) | Native Prepared Statements parsing parameterized structural commands preventing runtime database script executions. |
| Cross-Site Scripting (XSS) | Recursive application of output filter layers stripping rendering contexts using `htmlspecialchars()`. |
| Privilege Hijacking | Access routing validation protocols enforcing rigid backend script file evaluations via authorization guards (`auth.php`). |
| Form Data Infiltration | Strict custom input trimming and context sanitization loops deployed globally via `helpers.php`. |

---

## 🛣️ Engineering Roadmap & Iteration Pipeline

- [ ] Interactive customer feedback tracking fields with sentiment star ratings.
- [ ] Direct webhooks mapping local payment collection providers (Paymob, Stripe).
- [ ] Account authentication profile modules for client shoppers.
- [ ] Multi-currency support alongside precise discount/coupon coupon engines.

---

## 🤝 Contribution Guidelines

We highly value codebase improvements! Follow this pipeline to open pull requests:

1. Fork the codebase repository tracking.
2. Build an explicit feature development tracking branch: `git checkout -b feature/amazing-feature`.
3. Commit optimizations meeting structural standards: `git commit -m 'Add amazing feature'`.
4. Upstream elements directly to origin: `git push origin feature/amazing-feature`.
5. Open a formal Pull Request matching target repositories.

---

## 📝 License Summary

Distributed under the terms of the MIT License.

```
Copyright (c) 2026 Mazaya Mall

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
```

---

## 🙏 Core Acknowledgments

- **Bootstrap Development Core:** Modern responsive user interface foundation layout elements.
- **Font Awesome Asset Pipeline:** Unified high quality functional graphical design iconography.
- **Google Fonts API Engine:** Beautiful typography pathways optimizations rendering Premium Arabic experiences.

Built with passion for high-capacity real-world retail deployment and professional software engineering.
