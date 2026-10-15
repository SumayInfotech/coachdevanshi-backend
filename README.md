# Recipe Management Project Setup Guide 🚀

Welcome to the **Recipe Management** project! Follow these steps to set up the application correctly. 😊

---

## 1. Database Migration & Seeding 🗄️

Run the following commands to prepare your database:

```bash
composer install
php artisan migrate:fresh --seed
php artisan db:seed
```

## 2. Sync Roles and Permissions 🛡️

This command ensures all roles and permissions are correctly assigned:

```bash
php artisan sync:permissions
```

## 3. Import Locations Data 🌍

Populate the locations table with necessary data:

```bash
php artisan import:locations
```

You're all set! 🎉  
Your application is now ready to use. 

## 4. Set Up Passport Encryption Keys 🔑

Generate the necessary Passport encryption keys and create a personal access client:

```bash
php artisan passport:keys
php artisan passport:client --personal
```
