# NHIF SECURITY INCIDENT MANAGEMENT SYSTEM (SIMS)

## 📌 OVERVIEW
The **NHIF Security Incident Management System (SIMS)** is a professional-grade platform designed to monitor, track, and manage digital security breaches in real-time. It provides security teams with a centralized dashboard to visualize threats, ensuring rapid response and data integrity within the healthcare information infrastructure.

## 🛠️ TECHNOLOGIES USED
* **Backend:** PHP (Object-Oriented Logic)
* **Database:** MariaDB / MySQL
* **Frontend:** Bootstrap 5 (CSS Framework), Vanilla JavaScript
* **Security Architecture:** Prepared Statements (SQLi Protection), XSS Sanitization, and Secure Session Management.

## ✨ KEY FEATURES
* **LIVE INCIDENT DASHBOARD:** A modern, "Cybersecurity-themed" interface for high-level monitoring.
* **REAL-TIME FILTERING:** Advanced client-side filtering for Internal Logs and Public Reports without page reloads.
* **SEVERITY CLASSIFICATION:** Categorizes incidents into **Critical, High, Medium, and Low** for prioritized response.
* **SECURE CRUD OPERATIONS:** Full capability to Create, Read, and Delete records with administrative safeguards.
* **DYNAMIC ANALYTICS:** Automated counters for total incidents and critical threats displayed on the main dashboard cards.

## 🛡️ SECURITY IMPLEMENTATIONS
As a cybersecurity-focused project, this system implements the following defensive measures:
1. **SQL INJECTION PREVENTION:** Uses MySQLi Prepared Statements to neutralize database exploitation attempts.
2. **CROSS-SITE SCRIPTING (XSS) DEFENSE:** Implements a robust `escapeHTML` sanitization layer for all user-generated content.
3. **ADMINISTRATIVE ACCESS CONTROL:** All administrative routes are protected via secure session validation to prevent unauthorized access.

## 🚀 INSTALLATION
1. **Clone the repository:**
   ```bash
   git clone [https://github.com/Franklline/NHIF-SECURITY-INCIDENT-MANAGEMENT-SYSTEM.git](https://github.com/Franklline/NHIF-SECURITY-INCIDENT-MANAGEMENT-SYSTEM.git)
