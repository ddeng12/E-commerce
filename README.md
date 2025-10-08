# E-commerce Platform - MVC Structure

## 📁 Project Structure

```
E-commerce-activity1/
├── index.php                 # Main entry point (redirects to views/)
├── config.php               # Database configuration
├── core.php                 # Session management & user privileges
├── db.php                   # Database schema & sample data
│
├── models/                  # Data Models (M)
│   ├── customer_class.php   # Customer data operations
│   └── category_class.php   # Category data operations
│
├── controllers/             # Controllers (C)
│   ├── customer_controller.php  # Customer business logic
│   └── category_controller.php  # Category business logic
│
├── views/                   # Views (V)
│   ├── index.php           # Home page with dynamic menu
│   ├── login.php           # User login form
│   ├── register.php        # User registration form
│   ├── admin.php           # Admin panel
│   ├── dashboard.php       # User dashboard
│   └── category.php        # Category management (admin only)
│
├── actions/                 # API Endpoints
│   ├── login_customer_action.php
│   ├── register_customer_action.php
│   ├── fetch_category_action.php
│   ├── add_category_action.php
│   ├── update_category_action.php
│   └── delete_category_action.php
│
└── assets/                  # Static Assets
    ├── login.js            # Login form validation
    ├── register.js         # Registration form validation
    ├── category.js         # Category CRUD operations
    └── logout.php          # Logout handler
```

## 🚀 How to Access

- **Main Site**: `http://localhost/E-commerce-activity1/`
- **Admin Panel**: `http://localhost/E-commerce-activity1/views/category.php`
- **Login**: `http://localhost/E-commerce-activity1/views/login.php`
- **Register**: `http://localhost/E-commerce-activity1/views/register.php`

## 🔐 Default Credentials

- **Admin**: email: `admin@example.com`, password: `admin123`
- **Customer**: Register a new account

## 📋 Features Implemented

### Part 1: Customer Registration
- ✅ User registration with validation
- ✅ Country dropdown with all countries
- ✅ Password encryption
- ✅ Form validation (client & server-side)

### Part 2: Customer Login
- ✅ User authentication
- ✅ Session management
- ✅ Password verification
- ✅ Dynamic menu based on login status

### Part 3: Session Management & Admin Privileges
- ✅ Session handling
- ✅ Admin role checking
- ✅ Protected routes
- ✅ User privilege management

### Part 4: Category Management (CRUD)
- ✅ Create categories
- ✅ Read/Display categories
- ✅ Update categories
- ✅ Delete categories
- ✅ Admin-only access
- ✅ AJAX operations

## 🛠️ Technical Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **Architecture**: MVC Pattern
- **Authentication**: PHP Sessions
- **Validation**: Client-side (JS) + Server-side (PHP)
