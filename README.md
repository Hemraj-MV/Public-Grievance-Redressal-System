# 🏛️ AI-Powered Smart Governance Grievance Portal

> **A Privacy-First, AI-Integrated Grievance Redressal System for Municipal Corporations.**
> *Built with PHP, MySQL, and Local LLM (Llama 3.2 via Ollama).*

![Project Status](https://img.shields.io/badge/Status-Prototype%20Ready-success)
![AI Model](https://img.shields.io/badge/AI%20Model-Llama%203.2-blue)
![Privacy](https://img.shields.io/badge/Data%20Privacy-100%25%20Local-green)

---

## 📖 Problem Statement
Traditional government grievance portals suffer from:
1.  **Manual Sorting:** Officials waste hours reading and routing complaints.
2.  **Lack of Prioritization:** Critical issues (e.g., fires, gas leaks) get buried under routine requests.
3.  **Redundancy:** Multiple reports for the same issue (e.g., 50 reports for one pothole) clog the system.
4.  **Language Barriers:** Difficulty in processing complaints in local dialects or Tanglish.

## 🚀 The Solution
We built an **Intelligent Triaging System** that uses a **Local Large Language Model (LLM)** to:
* **Read & Understand** complaints in real-time.
* **Categorize** them automatically (Water, Electricity, Health, etc.).
* **Assign Priority** (Urgent issues are flagged in RED).
* **Detect Duplicates** using RAG (Retrieval-Augmented Generation) against the database.

**Key Differentiator:** All AI processing happens **locally** on the server using **Ollama**. No citizen data is sent to external clouds (like OpenAI/Google), ensuring **100% Data Privacy** and **Zero Recurring Costs**.

---

## 🛠️ Tech Stack

| Component | Technology | Why we used it? |
| :--- | :--- | :--- |
| **Frontend** | HTML5, CSS3, Bootstrap 5.3 | Responsive, enterprise-grade UI for admins and citizens. |
| **Backend** | PHP 8.2 | Fast, reliable, and native integration with government legacy systems. |
| **Database** | MySQL (XAMPP) | Structured storage for petitions and user data. |
| **AI Engine** | **Ollama** (Llama 3.2) | Runs the AI model locally for privacy and cost-efficiency. |
| **API** | REST API (CURL) | Connects the PHP backend to the local Ollama server. |
| **Features** | `html2pdf.js`, Chart.js | For generating PDF receipts and real-time analytics. |

---

## ✨ Key Features

### 1. 🤖 Automated AI Triaging
The system analyzes the description text and automatically assigns:
* **Department** (e.g., "Street Light" → Electricity Dept)
* **Priority** (e.g., "Sparking/Fire" → **URGENT**)

### 2. 🔍 Smart Duplicate Detection (RAG)
Before saving a new complaint, the system "looks back" at the last 5 days of records in MySQL to check if a similar issue was already reported in that location.

### 3. 🛡️ Privacy-First Architecture
Uses **Ollama** to run the **Meta Llama 3.2 (3B)** model on the local hardware. 
* No Internet required for categorization.
* No API billing costs.
* Secure Data Handling.

### 4. 📊 Admin Dashboard
* Visual **Pie Charts** for department load.
* **Red Flags** for urgent cases.
* **Status Tracking** (Pending → In Progress → Resolved).

---

## ⚙️ Installation & Setup

### Prerequisites
1.  **XAMPP** (for Apache & MySQL).
2.  **Ollama** (installed on your machine).
3.  **Model:** Run `ollama run llama3.2` in your terminal once.

### Steps
1.  **Clone the Repo:**
    ```bash
    git clone [https://github.com/your-username/governance-ai-portal.git](https://github.com/your-username/governance-ai-portal.git)
    ```
2.  **Move Files:** Copy the folder to `C:\xampp\htdocs\governance-ai`.
3.  **Database Setup:**
    * Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
    * Create a database named `governance_db`.
    * Import the `database.sql` file provided in this repo.
4.  **Start AI Server:**
    * Open your Terminal/Command Prompt.
    * Type: `ollama serve`.
5.  **Run the Project:**
    * Open Browser: `http://localhost/governance-ai`.

## 📄 License
This project is open-source and available under the **MIT License**.
