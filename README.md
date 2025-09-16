# 🚀 Laravel Project Setup Guide

## 📋 Requirements
- [Git](https://git-scm.com/downloads)
- [XAMPP](https://www.apachefriends.org/index.html) 
- [Composer](https://getcomposer.org/download/) 

---

## 🔧 Installation Steps

### 1️⃣ Clone the repository
```bash

git clone https://github.com/your-username/your-repo.git
cd your-repo
```

### 2️⃣ Install PHP dependencies
```bash

composer install
```

### 3️⃣ Configure environment
```bash

copy .env.example .env
php artisan key:generate
```

### 4️⃣ Setup PATH variables
```
D:\project\xampp\php
D:\project\xampp\mysql\bin
```

### 5️⃣ Setup database
1. Start Apache & MySQL from XAMPP Control Panel.
2. Create a database:
```bash

mysql -u root -e "CREATE DATABASE ${database-name};"
```
3. Run migration and seeder:
```bash

php artisan migrate
php artisan db:seed
```



## 🔧 Run The Server
1. execute `chas-hier.exe`
