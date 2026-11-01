# Bhumi Technocare System (Web Application)

**Version 1.1** | Field Service Management (FSM) & Machinery Maintenance ERP  
**Backend:** PHP 8.2+ / Laravel 12  
**Database:** MySQL 8.0+ (`bhumi_technocare`)  
**Frontend:** Blade PHP + Tailwind CSS CDN (Zero NPM / No Vite Build Tools Required)  

---

## 🎨 Color Palette Implementation
The application strictly incorporates the custom brand palette across all views, badges, charts, and buttons:
- **Primary:** `#1E3A5F` (Header, sidebar active states, executive titles)
- **Secondary:** `#3B82B8` (Links, action icons, secondary highlights)
- **Accent:** `#F59E0B` (Pending tickets, urgency markers, notice announcements)
- **Background:** `#F5F7FA` (Page backdrop)
- **Surface:** `#FFFFFF` (Card panels, forms, modals)
- **Primary Text:** `#1F2937`
- **Secondary Text:** `#64748B`
- **Success:** `#16A34A` (Completed calls, clock-in confirmations, verified handovers)
- **Warning:** `#D97706` (Overdue warnings, pending reviews)
- **Error:** `#DC2626` (Cancelled tickets, rejections, validation alerts)
- **Border:** `#E2E8F0`

---

## 🚀 Quick Launch (How to Run)

### Method 1: Double-Click Launcher
Double-click `run_app.bat` inside `d:\Client 1\php code`. It checks MySQL, starts the local server, and launches your browser at `http://127.0.0.1:8000/login`.

### Method 2: Manual Terminal Commands
1. **Ensure MySQL is running on port 3306:**
   ```bash
   "C:\Program Files\MySQL\MySQL Server 9.7\bin\mysqld.exe" --datadir="d:\Client 1\mysql_data" --port=3306 --console
   ```
2. **Start the Laravel Server:**
   ```bash
   cd "d:\Client 1\php code"
   php artisan serve --host=127.0.0.1 --port=8000
   ```
3. Open `http://127.0.0.1:8000` in any browser.

---

## 🔑 Demo Login Credentials

| Role | Mobile Number | Password | Quick Login in UI |
|---|---|---|---|
| **System Administrator / Manager** | `9999999999` | `password123` | Click "Admin Demo" button |
| **Field Engineer (Kishan Patel)** | `9876543210` | `password123` | Click "Engineer Demo" button |
| **Field Engineer (Rahul Sharma)** | `9825098250` | `password123` | Manual input |
| **Field Engineer (Amit Varma)** | `9898098980` | `password123` | Manual input |

---

## 📦 Implemented Functional Modules (PRD Coverage)

1. **Authentication & Session Security (Module 1):**
   - Mobile number + password authentication with rate limiting.
   - Role-based authorization (`admin` vs `engineer`).
   - Self password change with current password verification.
   - Admin reset password action.
   - Single-session protection and session invalidation.

2. **Engineer & Staff Management (Module 2):**
   - Profile management with blood group, department, emergency contacts, profile photo, and resume attachment.
   - Active/Inactive toggling and soft deletes.
   - Performance metrics and assigned tickets tracking.

3. **Geographic & Asset Master Data (Module 3):**
   - Industrial Zones / Areas management.
   - Cities linked to areas.
   - Machinery / Product types (CNC Milling, Jet Looms, Compressors, Laser Cutters, etc.).
   - Service types (Breakdown, Routine AMC, Installation, Overhaul).

4. **Client & Customer Directory (B2B/B2C) (Module 4):**
   - Client Organizations / Manufacturing Firms with GSTIN validation, address, and photos.
   - Individual Customers and factory representatives.
   - Complete service history by firm and contact.

5. **Field Service & Complaint Lifecycle (Module 5):**
   - Ticket booking with machine type, customer/firm, priority, due date, issue photos.
   - Status pipeline: **Pending ➔ In Progress ➔ Completed / Cancelled**.
   - Derived **Overdue** status for passed due dates.
   - Engineer mobile browser actions: Start Service timestamp, progress notes, upload resolution details & photos.

6. **Billing & On-Site Payment Collection (Module 6):**
   - Multi-type billing: **Cash Collection**, **Credit (Outstanding)**, and **GST Tax Bill**.
   - Credit collection workflow with linked payment tracking.
   - Physical cash handover verification by Admin with remarks and audit logs.
   - GST Invoice numbering record.

7. **Field Expense Claims (Module 7):**
   - Engineer expense submission with categories (Travel, Spare Components, Other), description, and receipt image.
   - Admin approval queue with acceptance and rejection with remarks.

8. **Attendance & Working-Hours Leaderboard (Module 8):**
   - Browser camera/photo clock-in and clock-out.
   - Single open session rule per day per engineer.
   - Working hours leaderboard with date filters and capped hours calculation.
   - Admin attendance approval/rejection queue.

9. **Broadcast Notice Board (Module 9):**
   - Admin can publish company-wide notices with optional expiry date.
   - Real-time notice feed displayed on engineer portal and admin dashboard.

10. **Executive Analytics Dashboard (Module 10):**
    - Executive KPIs (Total Calls, In-Progress, Overdue, Revenue Collected, Pending Handover).
    - Status distribution Doughnut Chart (Chart.js via CDN).
    - Machinery ticket volume Bar Chart.
    - Quick actions and recent ticket alerts.
