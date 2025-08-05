# Canlaon Evacuee System - Complete System Flow

## 🏗️ System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    CANLAON EVACUEE SYSTEM                      │
├─────────────────────────────────────────────────────────────────┤
│  Frontend (Bootstrap 5 + Custom CSS)                           │
│  ├── Responsive Web Interface                                  │
│  ├── User Authentication & Authorization                       │
│  └── Real-time Data Visualization                              │
├─────────────────────────────────────────────────────────────────┤
│  Backend (Custom PHP MVC)                                      │
│  ├── Controllers (Business Logic)                              │
│  ├── Models (Data Access Layer)                                │
│  └── Views (Presentation Layer)                                │
├─────────────────────────────────────────────────────────────────┤
│  Database (MySQL)                                              │
│  ├── User Management                                           │
│  ├── Resident & Household Data                                 │
│  ├── Evacuation Center Management                              │
│  └── ID Card System                                            │
└─────────────────────────────────────────────────────────────────┘
```

## 👥 User Roles & Access Control

### 1. Main Administrator
- **Access Level**: System-wide
- **Permissions**:
  - ✅ User management (create admin accounts)
  - ✅ Barangay management
  - ✅ All resident operations
  - ✅ ID card generation
  - ✅ Reports and analytics
  - ✅ Evacuation center management
- **Data Scope**: All barangays

### 2. Administrator
- **Access Level**: Barangay-specific
- **Permissions**:
  - ✅ Create staff accounts for their barangay
  - ✅ Manage residents in assigned barangay
  - ✅ Generate ID cards
  - ✅ View reports for their barangay
  - ✅ Manage evacuation centers for their barangay
  - ❌ User management (system-wide)
  - ❌ Barangay management
- **Data Scope**: Assigned barangay only

### 3. Staff
- **Access Level**: Barangay-specific (limited)
- **Permissions**:
  - ✅ Encode resident information
  - ✅ View residents in assigned barangay
  - ❌ ID card generation
  - ❌ User management
  - ❌ Reports access
- **Data Scope**: Assigned barangay only

## 🔄 Complete System Flow

### 1. Authentication Flow
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Login     │───▶│  Validate   │───▶│  Check      │───▶│  Dashboard  │
│   Page      │    │ Credentials │    │  Role &     │    │   Access    │
└─────────────┘    └─────────────┘    │ Permissions │    └─────────────┘
                                      └─────────────┘
```

### 2. Resident Management Flow
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Add New    │───▶│  Validate   │───▶│  Auto-Create│───▶│  Auto-Assign│
│  Resident   │    │  Input Data │    │  Household  │    │  Evac Center│
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
                                                              │
┌─────────────┐    ┌─────────────┐    ┌─────────────┐        │
│  Generate   │◀───│  Update     │◀───│  Success    │◀───────┘
│  ID Card    │    │  Database   │    │  Message    │
└─────────────┘    └─────────────┘    └─────────────┘
```

### 3. Household Management Flow
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Resident   │───▶│  Auto-Gen   │───▶│  Assign to  │
│  Creation   │    │  Household  │    │  Evac Center│
└─────────────┘    └─────────────┘    └─────────────┘
                                              │
┌─────────────┐    ┌─────────────┐           │
│  Add Family │◀───│  Update     │◀──────────┘
│  Members    │    │  Occupancy  │
└─────────────┘    └─────────────┘
```

### 4. Evacuation Center Management Flow
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Create     │───▶│  Set        │───▶│  Auto-      │───▶│  Track      │
│  Center     │    │  Capacity   │    │  Assignment │    │  Occupancy  │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
                                                              │
┌─────────────┐    ┌─────────────┐    ┌─────────────┐        │
│  Generate   │◀───│  Update     │◀───│  Monitor    │◀───────┘
│  Reports    │    │  Statistics │    │  Utilization│
└─────────────┘    └─────────────┘    └─────────────┘
```

### 5. ID Card Generation Flow
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Select     │───▶│  Validate   │───▶│  Generate   │───▶│  Create     │
│  Resident   │    │  Eligibility│    │  Barcode    │    │  Card Data  │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
                                                              │
┌─────────────┐    ┌─────────────┐    ┌─────────────┐        │
│  Print/     │◀───│  Set Expiry │◀───│  Log        │◀───────┘
│  Download   │    │  Date       │    │  Activity   │
└─────────────┘    └─────────────┘    └─────────────┘
```

## 📊 Data Flow Architecture

### 1. User Authentication & Session Management
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Login      │───▶│  User Model │───▶│  Session    │───▶│  Role-Based │
│  Form       │    │  Validation │    │  Creation   │    │  Access     │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

### 2. Resident Data Processing
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Resident   │───▶│  Validation │───▶│  Household  │───▶│  Evacuation │
│  Controller │    │  & Sanitize │    │  Creation   │    │  Assignment │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
                                                              │
┌─────────────┐    ┌─────────────┐    ┌─────────────┐        │
│  Database   │◀───│  Transaction│◀───│  Success    │◀───────┘
│  Update     │    │  Commit     │    │  Response   │
└─────────────┘    └─────────────┘    └─────────────┘
```

### 3. Evacuation Center Assignment Logic
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  New        │───▶│  Check      │───▶│  Find       │───▶│  Update     │
│  Household  │    │  Barangay   │    │  Available  │    │  Center     │
│  Created    │    │  Centers    │    │  Center     │    │  Occupancy  │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
                                                              │
┌─────────────┐    ┌─────────────┐    ┌─────────────┐        │
│  Update     │◀───│  Assign     │◀───│  Validate   │◀───────┘
│  Household  │    │  Household  │    │  Capacity   │
│  Record     │    │  to Center  │    │  Available  │
└─────────────┘    └─────────────┘    └─────────────┘
```

## 🔐 Security & Authorization Flow

### 1. Permission Checking
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  User       │───▶│  Check      │───▶│  Validate   │───▶│  Grant/Deny │
│  Request    │    │  Session    │    │  Permission │    │  Access     │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

### 2. Data Isolation
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  User       │───▶│  Get        │───▶│  Filter     │
│  Role       │    │  Barangay   │    │  Data by    │
│  & Barangay │    │  Assignment │    │  Barangay   │
└─────────────┘    └─────────────┘    └─────────────┘
```

## 📈 Reporting & Analytics Flow

### 1. Dashboard Statistics
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Dashboard  │───▶│  Query      │───▶│  Calculate  │───▶│  Display    │
│  Load       │    │  Database   │    │  Statistics │    │  Charts &   │
│             │    │             │    │             │    │  Numbers    │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

### 2. Report Generation
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Select     │───▶│  Apply      │───▶│  Generate   │───▶│  Export     │
│  Report     │    │  Filters    │    │  Data Set   │    │  (PDF/CSV)  │
│  Type       │    │             │    │             │    │             │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

## 🚨 Emergency Response Flow

### 1. Evacuation Planning
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Emergency  │───▶│  Check      │───▶│  Generate   │───▶│  Distribute │
│  Alert      │    │  Evacuation │    │  Lists by   │    │  to Centers │
│             │    │  Centers    │    │  Center     │    │             │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

### 2. Special Needs Assistance
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Identify   │───▶│  Prioritize │───▶│  Assign     │───▶│  Track      │
│  Special    │    │  by Need    │    │  Assistance │    │  Assistance │
│  Needs      │    │  Type       │    │  Resources  │    │  Delivery   │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

## 🔄 Transaction Management

### 1. Resident Creation Transaction
```sql
BEGIN TRANSACTION;
  -- Create household
  INSERT INTO households (household_head, address, barangay_id, ...);
  
  -- Create resident
  INSERT INTO residents (household_id, first_name, last_name, ...);
  
  -- Auto-assign evacuation center
  UPDATE evacuation_centers SET current_occupancy = current_occupancy + 1;
  
  -- Update household with evacuation center
  UPDATE households SET assigned_evacuation_center = 'center_name';
COMMIT;
```

### 2. Evacuation Center Reassignment
```sql
BEGIN TRANSACTION;
  -- Remove from old center
  UPDATE evacuation_centers SET current_occupancy = current_occupancy - member_count;
  
  -- Assign to new center
  UPDATE evacuation_centers SET current_occupancy = current_occupancy + member_count;
  
  -- Update household assignment
  UPDATE households SET assigned_evacuation_center = 'new_center_name';
COMMIT;
```

## 📱 User Interface Flow

### 1. Navigation Structure
```
Dashboard
├── Residents
│   ├── List Residents
│   ├── Add New Resident
│   ├── Add Family Member
│   ├── Edit Resident
│   └── View Resident Details
├── Households
│   ├── List Households
│   ├── View Household Details
│   └── Edit Household
├── ID Cards
│   ├── List ID Cards
│   ├── Generate New Card
│   ├── View Card Details
│   └── Print/Download Card
├── Evacuation Centers
│   ├── List Centers
│   ├── Add New Center
│   ├── Edit Center
│   ├── View Center Details
│   └── Auto Assignment
├── Reports
│   ├── Resident Reports
│   ├── Household Reports
│   ├── ID Card Reports
│   └── Evacuation Reports
└── User Management (Admin Only)
    ├── List Users
    ├── Add New User
    ├── Edit User
    └── Manage Permissions
```

### 2. Responsive Design Flow
```
Desktop (≥1200px)
├── Full sidebar navigation
├── Complete data tables
└── All features visible

Tablet (768px - 1199px)
├── Collapsible sidebar
├── Responsive tables
└── Touch-friendly buttons

Mobile (<768px)
├── Hamburger menu
├── Stacked layouts
└── Simplified navigation
```

## 🔧 System Integration Points

### 1. Database Integration
- **Primary Database**: MySQL
- **Connection Pooling**: Custom implementation
- **Transaction Management**: ACID compliance
- **Data Validation**: Server-side validation

### 2. File System Integration
- **Upload Directory**: `uploads/`
- **Barcode Images**: Generated on-demand
- **ID Card Templates**: Stored as HTML/CSS
- **Export Files**: Temporary storage

### 3. Session Management
- **Session Storage**: PHP native sessions
- **Session Lifetime**: Configurable (default: 1 hour)
- **Security**: CSRF protection, input sanitization
- **Logout**: Automatic cleanup

## 📊 Performance Optimization

### 1. Database Optimization
- **Indexing**: Primary keys, foreign keys, search fields
- **Query Optimization**: Prepared statements, efficient joins
- **Pagination**: Limit result sets
- **Caching**: Session-based caching

### 2. Frontend Optimization
- **CSS/JS Minification**: Bootstrap CDN
- **Image Optimization**: Barcode generation
- **Lazy Loading**: Pagination implementation
- **Responsive Images**: Bootstrap responsive classes

## 🔒 Security Measures

### 1. Authentication Security
- **Password Hashing**: bcrypt algorithm
- **Session Security**: Secure session handling
- **CSRF Protection**: Token-based validation
- **Input Sanitization**: XSS prevention

### 2. Authorization Security
- **Role-Based Access**: Permission checking
- **Data Isolation**: Barangay-specific access
- **SQL Injection Prevention**: Prepared statements
- **File Upload Security**: Type and size validation

## 📈 Scalability Considerations

### 1. Database Scalability
- **Normalization**: Proper database design
- **Indexing Strategy**: Optimized for common queries
- **Partitioning**: Potential for large datasets
- **Backup Strategy**: Regular database backups

### 2. Application Scalability
- **Modular Architecture**: MVC pattern
- **Code Reusability**: Shared components
- **Configuration Management**: Centralized settings
- **Error Handling**: Comprehensive logging

## 🚀 Deployment Flow

### 1. Development to Production
```
Development Environment
├── Local testing
├── Feature development
└── Unit testing

Staging Environment
├── Integration testing
├── User acceptance testing
└── Performance testing

Production Environment
├── Database migration
├── Application deployment
└── Monitoring setup
```

### 2. Maintenance Procedures
```
Regular Maintenance
├── Database backups
├── Log rotation
├── Security updates
└── Performance monitoring

Emergency Procedures
├── System rollback
├── Data recovery
├── Incident response
└── Communication protocols
```

---

## 📋 Summary

The Canlaon Evacuee System is a comprehensive emergency management solution that:

1. **Streamlines Resident Management**: Automatic household creation and evacuation center assignment
2. **Ensures Data Security**: Role-based access control and barangay-specific data isolation
3. **Facilitates Emergency Response**: Quick access to resident information and evacuation plans
4. **Provides Real-time Analytics**: Dashboard with live statistics and reporting capabilities
5. **Supports Multiple User Roles**: Main admin, barangay admin, and staff with appropriate permissions
6. **Offers Scalable Architecture**: MVC pattern with modular design for future enhancements

The system flow ensures efficient data processing, secure user access, and reliable emergency response capabilities while maintaining data integrity and system performance. 