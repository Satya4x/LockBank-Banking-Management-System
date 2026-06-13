# 🏦 LOCKBANK – Bank Management System (BMS)

A full-featured web-based **Bank Management System** built with **PHP & MySQL**, supporting user banking operations and admin controls.

---

## 📋 Project Overview

**LOCKBANK BMS** is a simulated banking portal that allows users to open accounts, perform transactions, and manage their finances — while admins can oversee all operations from a dedicated dashboard.

---

## ✨ Features

### 👤 User Panel
- Account Registration (with Aadhaar, PAN, photo upload)
- Secure Login with Email & Password
- Check Account Balance
- Cash Deposit & Withdrawal
- Money Transfer (via Account Number or Mobile)
- Transaction History
- Notifications & Notices

### 🛡️ Admin Panel
- Admin Login
- Manage Users (View, Edit, Delete)
- Manage Notices & Notifications
- View Deposits, Withdrawals & All Transactions
- Update Transaction Status

---

## 🗂️ Project Structure

```
LockBank-Banking-Management-System-main/
│
├── index.php                  # Landing page
├── login.php                  # User login
├── signup.php                 # New account registration
├── logout.php                 # User logout
│
├── user_dashboard.php         # User home dashboard
├── check_balance.php          # Check account balance
├── cash_deposit.php           # Cash deposit
├── cash_withdrawal.php        # Cash withdrawal
├── transfer_money.php         # Money transfer
├── transaction_history.php    # Full transaction history
├── transactions.php           # Transaction view
├── notifications.php          # User notifications
├── view_notifications.php     # View notification detail
│
├── admin_login.php            # Admin login
├── admin_logout.php           # Admin logout
├── admin_dashboard.php        # Admin control panel
├── manage_users.php           # View & manage all users
├── edit_user.php              # Edit user details
├── view_user.php              # View user profile
├── delete_user.php            # Delete a user
├── deposits.php               # View all deposits
├── withdrawals.php            # View all withdrawals
├── notices.php                # Manage notices
├── edit_notice.php            # Edit notice
├── delete_notice.php          # Delete notice
├── update_transaction.php     # Update transaction status
│
├── db.php                     # Database connection
├── get_user_name.php          # Utility: fetch user name by account
│
├── BMS.sql                    # Database schema & seed data
│
├── uploads/                   # User profile photos
│
└── *.css                      # Stylesheets for each page
```

---

## 🗄️ Database

**Database Name:** `lockbank`

### Tables:
| Table | Description |
|-------|-------------|
| `users` | Stores user account info, balance, LPIN |
| `admins` | Admin credentials |
| `transactions` | All transaction records (deposit, withdrawal, transfer) |
| `notices` | Notices/notifications sent to users |

---

## ⚙️ Setup & Installation

### Prerequisites
- PHP 7.4+
- MySQL / MariaDB
- Apache (XAMPP / WAMP / LAMP recommended)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/Satya4x/LockBank-Banking-Management-System.git
   ```

2. **Move to server root**
   ```bash
   # For XAMPP on Windows
   Move the LB_BMS folder to: C:/xampp/htdocs/

   # For Linux
   Move to: /var/www/html/
   ```

3. **Import the database**
   - Open **phpMyAdmin**
   - Create a new database named `lockbank` (or just import — the SQL creates it automatically)
   - Import `BMS.sql`

4. **Configure DB connection**
   Open `db.php` and update if needed:
   ```php
   $servername = "localhost";
   $username   = "root";
   $password   = "";       // Set your MySQL password
   $dbname     = "lockbank";
   ```

5. **Run the project**
   Open your browser and go to:
   ```
   http://localhost/LockBank-Banking-Management-System/
   ```

---

## 🔐 Default Credentials

> ⚠️ **Change these before deploying anywhere!**

**Admin Login:**
| Field | Value |
|-------|-------|
| Username | `satya_4x` |
| Password | *(set during setup)* |

**Sample User Accounts** are included in `BMS.sql` for testing.

---

## 🔒 Security Notes

> This project is built for **educational purposes**. Before any real-world use, the following must be addressed:

- `login.php` uses raw SQL query — **SQL Injection risk** → use prepared statements
- `signup.php` INSERT query uses raw variables — **SQL Injection risk** → use prepared statements
- LPIN is stored as plain integer in the database → should be hashed
- No CSRF protection on forms
- File upload in `signup.php` has no file type/extension validation on server side
- Sensitive data (Aadhaar, PAN) stored in plaintext

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3 |
| Backend | PHP (Procedural + MySQLi) |
| Database | MySQL |
| Icons | Font Awesome 6 |

---

## 📸 Screenshots

User Homepage

<img width="1349" height="653" alt="image" src="https://github.com/user-attachments/assets/1c464976-3158-4e28-9f08-27d85604d740" />

User Opening Account

<img width="1349" height="1591" alt="image" src="https://github.com/user-attachments/assets/33d3934a-ebef-47b3-9732-b0d4d83f81d9" />

User Dashboard 

<img width="1366" height="653" alt="image" src="https://github.com/user-attachments/assets/e01e231f-ed2b-4d7c-b3c4-4aa658e4f940" />

Admin Login Page

<img width="1366" height="653" alt="image" src="https://github.com/user-attachments/assets/600aa3ed-30c9-43a9-b100-2af869bee583" />

Admin Dashboard

<img width="1366" height="653" alt="image" src="https://github.com/user-attachments/assets/4afe75df-4cc4-4ff2-8861-54a9c009bd10" />


---

## 👨‍💻 Author

**Satyam Jaiswal** — *LockBank BMS Project*

---

## 📄 License

This project is open source and available for educational use.
