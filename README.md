# 🎯 Customer Support Ticketing Platform

A full-featured web application for managing customer support tickets, built during my internship based on a detailed technical and functional specification.

## 🚀 Project Overview

This platform allows users to create, manage, and resolve support tickets through a structured and efficient interface. It supports three types of users:

- **Clients**: Submit and track support tickets  
- **Agents**: Handle and respond to tickets  
- **Administrators**: Supervise, manage users, and access analytics  

---

## ⚙️ Tech Stack

**Front-end**:  
- Vue.js  
- Vue Router, Vuex  
- Axios for API calls  
- Fully responsive UI (Desktop, Tablet, Mobile)

**Back-end**:  
- Laravel (RESTful API)  
- JWT Authentication  
- Role-based middleware (Client, Agent, Admin)

**Database**:  
- MySQL  
- Eloquent ORM for models and relationships

---

## 🔐 Authentication & Role Management

- Secure registration with email validation  
- Encrypted password storage  
- Password reset via email  
- Role-based permissions and access control

| Role         | Permissions |
|--------------|-------------|
| Client       | Submit, view, and comment on own tickets |
| Agent        | View, respond, and update tickets assigned to them |
| Administrator| Full access: manage users, assign tickets, monitor stats |

---

## 📝 Ticket Management Features

- Create tickets with:
  - Subject
  - Description
  - Category (dropdown)
  - Priority (Low / Medium / High / Critical)
  - Optional file attachments (PDF, images, docs)
  
- Ticket statuses:
  - Open
  - In Progress
  - Resolved
  - Closed

- Assignment:
  - Automatic (based on category or agent availability)
  - Manual (by an admin)

- Commenting system for clients and agents  
- File attachment in messages  
- Full message history  

---

## 📊 Dashboards

### 🧑‍💼 Administrator Dashboard

- View all tickets with filters:
  - By status, priority, category, or client  
- Real-time stats:
  - Number of tickets per status  
  - Resolution times  
  - Agent performance  

📷 _Example Screenshot_  
![Admin Dashboard](screenshots/admin_dashboard.png)

---

### 🧑 Agent Dashboard

- View and manage assigned tickets  
- Respond and update statuses  
- Add internal notes and attachments

📷 _Example Screenshot_  
![Agent Dashboard](screenshots/agent_dashboard.png)

---

### 👤 Client Dashboard

- View list of submitted tickets  
- Track status and add comments  
- Get email notifications on updates

📷 _Example Screenshot_  
![Client Dashboard](screenshots/client_dashboard.png)

---

## ✉️ Notifications

- Automatic email sent on:
  - Ticket creation
  - New response
  - Ticket closure

---

## 🧩 Ticket Categories

- Admins can manage categories (create, edit, delete)  
- Agents can be linked to specific categories

---

## 📁 Exports & Reports

- Export tickets as **Excel** or **PDF**  
- Monthly reports:
  - Ticket volume  
  - Categories breakdown  
  - Agent performance metrics

---

## 📎 File Uploads

- Ticket and message attachments supported  
- Secure file validation and storage

---

## 🔐 Security

- Input validation  
- CSRF and XSS protection  
- JWT-based secure API access

---

## 📷 Screenshots

You can find UI screenshots in the `/screenshots` folder:

- `admin_dashboard.png`
- `agent_dashboard.png`
- `client_dashboard.png`
- Others (login page, ticket form, message thread...)

---

## 🧑‍💻 Developed By

**[Your Full Name]**  
Intern Developer  
[Your Email]  
[LinkedIn or GitHub profile link]

---

## ✅ Status

✅ **All features implemented and tested as per the project requirements.**  
📅 Internship duration: 2 months  
📂 See [Cahier des Charges (FR)](link-to-pdf-if-public) for full specifications.

---

