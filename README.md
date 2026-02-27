# 💬 Real-Time Chat Application

A simple real-time one-to-one chat application built using **PHP, MySQL, Vue.js, and Bootstrap**.  
Users can log in, view friends, and exchange messages instantly using AJAX polling.

---

## 🚀 Features

- 🔐 User Authentication (Login Session)
- 👥 Friend/User List
- 💬 One-to-One Messaging
- 🔄 Auto Message Refresh (Polling)
- 🕒 Message Timestamp Support
- 📱 Responsive UI (Bootstrap)
- ⚡ Vue.js Dynamic Frontend

---

## 🛠️ Tech Stack

### Frontend
- Vue.js 3
- Bootstrap 5
- HTML5
- CSS3
- Font Awesome

### Backend
- PHP (Core PHP)
- MySQL Database

### Tools
- XAMPP / WAMP
- Git & GitHub

---

## 📂 Project Structure

```
chat-app/
│
├── chat.php        # Main chat page
├── login.php       # Login page
├── database.sql    # Database structure and sample data
└── README.md
```

---

## ⚙️ Setup Instructions

1. Clone the Repository
- git clone https://github.com/your-username/your-repository-nam

2. Move Project to Local Server

- Place the folder inside:
   xampp/htdocs/ (XAMPP)

3. Create Database

  a.Open phpMyAdmin
  b.Create a new database named: chat_db
  c.Import the file: database.sql

4. Configure Database Connection

   Inside chat.php and login.php, verify: $conn = new mysqli("localhost", "root", "", "chat_db");

5. Run the Application

    Start: Apache and MySQL
    Open in browser :  http://localhost/chat-app/login.php
   
---

## 👤 Demo Users

  You can log in using the sample users from database.sql:
  |       Email           |      Password           |
  |-----------------------|-------------------------|
  |  subhash@gmail.com    |       subhash           |	
  |  vamsi@gmail.com      |       vamsi             |
  |  car@gmail.com        |       car               |

---

## 🔄 How It Works

1. User logs in via login.php.
2. Session stores user ID and email.
3. chat.php loads friend list.
4. Vue.js sends AJAX requests to:
   - Fetch messages
   - Send messages
5. Messages update automatically every 3 seconds

---


## ⭐ Support

If you like this project:

- ⭐ Star this repository  
- 🍴 Fork it  
- 📢 Share it  
