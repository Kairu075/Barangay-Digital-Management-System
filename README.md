# 🏛️ Barangay - Digital Management System
### Web-Based Barangay Management System

---

##  DEFAULT LOGIN ACCOUNTS
All accounts use the password: **password**

| Username | Password | Role |
|---|---|---|
| admin | password | Administrator |
| captain_reyes | password | Barangay Captain |
| sec_santos | password | Secretary |
| treas_garcia | password | Treasurer |
| resident1 | password | Resident |

---

##  SYSTEM MODULES

### 1. Dashboard
- Overview statistics (residents, documents, complaints, finances)
- Recent document requests
- Recent complaints
- Announcements feed
- Quick action buttons

### 2. Residents Module
- Add, view, edit resident records
- Search by name, ID, purok, gender
- Resident profile with full details
- Special classifications (Senior Citizen, PWD, Voter, Solo Parent)
- Document and complaint history per resident

### 3. Documents Module
- New document request form with resident search
- Auto-calculated processing fees
- Status workflow: Pending → Processing → For Approval → Approved → Released
- **Generate & Print official certificates:**
  - Barangay Clearance
  - Certificate of Residency
  - Indigency Certificate
  - Business Clearance
  - Certificate of Good Moral Character
- Payment recording with Official Receipt generation

### 4. Complaints Module
- File new complaints with type classification
- Track complaint status (Pending → Investigation → Mediation → Resolved)
- Admin notes and updates
- Case history per resident

### 5. Announcements Module
- Post, edit, publish/unpublish announcements
- Categories: General, Health, Emergency, Events, Programs, Advisory
- Priority levels: Normal, Important, Urgent
- Date range scheduling

### 6. Finance Module
- View all transactions with filtering by month/year
- Print individual Official Receipts
- Monthly bar chart visualization
- **Generate printable Monthly Financial Reports** with:
  - Transaction details
  - Totals by payment method
  - Signature areas for Treasurer, Secretary, Captain

### 7. User Management (Admin only)
- Add new system users
- Assign roles with access control
- Activate/deactivate accounts
- Reset passwords


---


##  PROJECT STRUCTURE

```
barangay/
├── index.php              # Root redirect
├── login.php              # Login page
├── dashboard.php          # Main dashboard
├── logout.php
├── database.sql           # Database schema + mock data
├── includes/
│   ├── config.php         # DB config + helper functions
│   ├── header.php         # Sidebar + topbar
│   └── footer.php
├── assets/
│   ├── css/main.css       # Complete stylesheet
│   └── js/main.js         # JavaScript utilities
├── api/
│   └── search_residents.php  # AJAX resident search
└── modules/
    ├── residents/
    │   ├── index.php
    │   ├── add.php
    │   ├── edit.php
    │   └── view.php
    ├── documents/
    │   ├── index.php
    │   ├── add.php
    │   ├── generate.php   # Certificate generator
    │   └── payment.php    # OR generation
    ├── complaints/
    │   ├── index.php
    │   └── add.php
    ├── announcements/
    │   ├── index.php
    │   ├── add.php
    │   └── edit.php
    ├── finance/
    │   ├── index.php
    │   ├── report.php     # Monthly financial report
    │   └── add_transaction.php
    └── users/
        └── index.php
```

---

*By: Kyle Dominic Yap*
