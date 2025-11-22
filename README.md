# Phone Book - Installation Instructions

## Installation

### 1. Clone the repository

```bash
git clone git@github.com:vladsandbox/phone-book.git
cd phone-book
```

### 2. Configure environment variables

Copy `.env.example` to `.env` and modify settings.


### 3. Install PHP dependencies

```bash
cd app
docker run --rm -v $(pwd):/app composer:latest install
cd ..
```

### 4. Start Docker containers

```bash
docker-compose up -d
```

This will start three containers:
- **web** (nginx) on port 8080
- **php** (PHP 8.2+) on port 9000
- **db** (MySQL 8) on port 3306

### 5. Create database structure

Connect to the MySQL container and create tables:

```bash
docker exec -it $db_container_name mysql -u $user_name -p$user_password $db_name
```

Execute the following SQL:

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_login (login),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_name (last_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Exit MySQL with the `exit;` command.


### 6. Verify installation

Open your browser and navigate to:

```
http://localhost:8080
```
You should see the registration/login form.