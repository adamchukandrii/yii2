# Yii2 RESTful API

A RESTful API built with Yii2 and MongoDB for managing users and tasks.

## 🚀 Features
- User and task management
- JSON as the data exchange format
- Task status transitions (`New -> In Progress -> Finished` / `New -> In Progress -> Failed`)
- Pagination for `/users` and `/users/{id}/tasks`
- Sorting for users (`first_name`, `last_name`, `email`) and tasks (`title`, `status`)
- Task statistics per user and globally
- Deleting only unprocessed (`New`) tasks

---

## 📦 Installation

### 1️⃣ Clone the Repository
```sh
git clone git@github.com:adamchukandrii/yii2.git
cd yii2
```

### 2️⃣ Start the Application with Docker
```sh
docker-compose up -d
```

### 3️⃣ Import API Collection into Postman
- Import `Yii2.postman_collection.json` into Postman for easy testing.

---
