# Firefly Task ( Transaction Tracker ) 

## Overview

Simple CRUD-based application for managing and keeping records of Transactions made by using filamentphp

Task Demo : https://transactiontracker.bpnpdl.com.np/

## Features

-   **Transaction Management**: Create, view, edit, and delete financial transactions
-   **Transaction Categories**: Categorize transactions as either Income or Expense
-   **Dashboard**: Visual overview of your financial status with recent activity
-   **Filtering**: Filter transactions by type (Income/Expense), According to days, months and year and Trash Datas.
-   **Data Validation**: Comprehensive validation for all transaction data
-   **User Authentication**: Secure user accounts with Laravel's authentication system

## Screenshots

### Dashboard View

![image](https://github.com/user-attachments/assets/5681749f-2ff1-4316-b1f7-4267299d2464)
_The main dashboard provides an overview of financial status with recent transactions_

### Transaction List

![image](https://github.com/user-attachments/assets/cadfdf4b-cdd4-402a-8789-442ed93516b8)
_List of all transactions with filtering capability_

### Add Transaction Form

![image](https://github.com/user-attachments/assets/6caa02f5-e418-4fe8-89c4-a417f08e082b)
_Form for adding new income or expense transactions_

### Monthly Report Generation
![image](https://github.com/user-attachments/assets/e3c769b6-1b6e-448f-8113-be6d9786e4d2)
_Monthly Report Exported to pdf


## Technology Stack

-   **Development Tool**: Filamentphp: 
-   **Database**: MySQL
-   **Authentication**: FilamentInBuild Authentication
-   **Build Tool**: Vite
-   **Development Environment**: Laravel Herd

## Requirements

-   PHP 8.1 or higher
-   Composer
-   Node.js & NPM
-   MySQL database
-   Laravel Herd (recommended for local development)

## Installation

### 1. Clone and Configure the Project

```bash
# Clone the repository
git clone https://github.com/bpnpdl1/fireflyfilamenttask.git
cd fireflyfilamenttask

# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate
```

### 2. Database Configuration

Update your `.env` file with your database credentials:

```
APP_NAME=FireFlyTask
APP_ENV=local
APP_KEY=base64:oM8Mxe1MTm6Ys50sPnkbiHpuQi2Ig4Nhy7GiWAMBn0A=
APP_DEBUG=true
APP_URL=https://fireflyfilamenttask.test
```

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fireflytask
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Set Up the Application

```bash
# Run migrations
php artisan migrate --seed

# Compile frontend assets
npm run dev
```

### 4. Launch with Laravel Herd (Recommended)

-   Open Laravel Herd
-   Add your project to Herd
-   Click "Start Site" in the Herd interface
-   Click "View Site" to open your project in the browser

## Usage Guide

1. I had set login credentials in the form so that easy to view the demo of transaction tracker
   - **Login credentials:**  
     **Email:** `testuser@testmail.com`  
     **Password:** `password`
2. Navigate to the dashboard to see your financial overview
3. Add new transactions through the **"Create Transaction"** form
4. View and manage all transactions on the **Transaction Index** page
5. Use filters to view specific transaction types, dates and trash records.
6. See Monthly Report of Income and Expense and export in pdf according to month.
7. Edit or delete transactions as needed


## Database Structure

The application uses the following main tables:

-   **users**: Stores user account information
-   **transactions**: Stores all financial transactions with the following fields:
    -   `id`: Unique identifier
    -   `description`: Transaction description
    -   `amount`: Transaction amount
    -   `type`: Transaction type (Income/Expense)
    -   `transaction_date`: Date when the transaction occurred
    -   `user_id`: Reference to the user who owns the transaction
    -   'deleted_at`: Timestamp
    -   `created_at` and `updated_at`: Timestamps

## Development

### Laravel Herd

This project is optimized for Laravel Herd, which provides a streamlined development environment for Laravel on Windows, including:

-   PHP with optimized configuration
-   MySQL database
-   Automatic virtual host configuration
-   Easy SSL certificate management
-   Simple project management UI

### Testing

The project includes comprehensive test coverage:

![image](https://github.com/user-attachments/assets/0a778b43-cedb-448e-bc8c-b74455c984d7)



## Customization

-   Transaction types are managed through the `TransactionTypeEnum` enum
-   Encapsule the CRUD logic in TransactionService
-   Additional transaction categories can be added by extending the enum
-   Added the Chart to Views the data

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
