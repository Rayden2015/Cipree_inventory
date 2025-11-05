# How to Debug User Creation Failures
**Error/Audit Logging is FULLY Implemented**

---

## ✅ **What's Being Logged:**

UserController's `store()` method logs **EVERYTHING**:

1. **User creation attempt started** - With creator's name and request data
2. **Validation failures** - Which fields failed validation
3. **Invalid roles** - If non-existent roles selected
4. **Database errors** - Constraint violations, duplicate entries, etc.
5. **Role assignment** - Success or failure
6. **Image upload** - Success or failure  
7. **Email sending** - Success or failure
8. **SMS sending** - Success or failure
9. **Final catch block** - ANY unexpected error with full context + ERROR ID

---

## 🔍 **How to Find Errors:**

### **Option 1: Check Today's Log File**

```bash
# View all UserController store logs
tail -200 storage/logs/laravel-2025-11-05.log | grep "UserController | store"

# View just errors
tail -200 storage/logs/laravel-2025-11-05.log | grep -i "error\|exception" | grep -i "user"

# Follow log in real-time (open this in a separate terminal)
tail -f storage/logs/laravel-2025-11-05.log | grep "UserController"
```

### **Option 2: Try Creating a User and Watch Logs**

**Terminal 1** (watch logs):
```bash
tail -f storage/logs/laravel-2025-11-05.log
```

**Terminal 2** (or browser):
1. Navigate to: Create User page
2. Fill in the form
3. Click "Add User"
4. Watch Terminal 1 for logs

### **Option 3: If You Get an Error ID**

User sees: "Error ID: 762345678"

```bash
# Quick search
./search-error.sh 762345678

# Or web UI
http://127.0.0.1:8000/error-logs
# Search for: 762345678
```

---

## 📋 **What Different Errors Look Like:**

### **1. Validation Error**
```
[timestamp] local.WARNING: UserController | store | Validation failed 
{
  "created_by": "Admin",
  "validation_errors": {
    "email": ["The email has already been taken."],
    "phone": ["The phone has already been taken."]
  }
}
```

### **2. Database Error**
```
[timestamp] local.ERROR: UserController | store | Database error during user creation 
{
  "created_by": "Admin",
  "error_code": "23000",
  "error_message": "Integrity constraint violation"
}
```

### **3. Role Error**
```
[timestamp] local.WARNING: UserController | store | Invalid roles provided 
{
  "created_by": "Admin",
  "invalid_roles": ["non-existent-role"]
}
```

### **4. Unexpected Error (With ERROR ID)**
```
[timestamp] local.ERROR: [ERROR_ID:762345678] UserController | store() | Column not found
{
  "error_id": 762345678,
  "controller": "UserController",
  "method": "store()",
  "user_id": 5,
  "user_name": "Admin",
  "url": "http://127.0.0.1:8000/users",
  "error_message": "SQLSTATE[42S22]: Column not found: 1054 Unknown column...",
  ...full context...
}
```

---

## 🛠️ **Common User Creation Issues:**

### **Issue 1: Missing Required Fields**
**Error**: "The site id field is required"
**Fix**: Ensure site is selected in the form

### **Issue 2: Duplicate Email/Phone**
**Error**: "The email has already been taken"
**Fix**: Use a different email or check existing users

### **Issue 3: Missing Columns**
**Error**: "Column not found: 1054 Unknown column 'department_id'"
**Fix**: Run migrations:
```bash
php artisan migrate
```

### **Issue 4: Invalid Foreign Keys**
**Error**: "Cannot add foreign key constraint"
**Fix**: Ensure site_id, department_id, section_id exist in their respective tables

### **Issue 5: Permission Denied**
**Error**: "You do not have permission to create users"
**Fix**: Ensure user has 'add-user' permission

---

## 🚀 **Quick Debugging Steps:**

### **Step 1: Try Creating a User**
1. Login as Super Admin
2. Go to: Company > Account
3. Click "Add New User"
4. Fill required fields:
   - Name: Test User
   - Email: test123@example.com
   - Phone: 233555555555
   - Site: (select any)
   - Status: Active
   - Roles: requester
5. Click "Add User"

### **Step 2: Check What Happened**

```bash
# Check for the attempt log
tail -50 storage/logs/laravel-2025-11-05.log | grep "UserController | store | Attempt started"

# Check for errors
tail -50 storage/logs/laravel-2025-11-05.log | grep "ERROR\|WARNING" | grep "UserController"

# Check for success
tail -50 storage/logs/laravel-2025-11-05.log | grep "User created successfully"
```

### **Step 3: If Error ID Shown**
```bash
./search-error.sh <error_id>
```

---

## 📊 **Log Locations:**

```bash
# Today's main log
storage/logs/laravel-2025-11-05.log

# Error-specific log  
storage/logs/errors/error.log

# Previous days
storage/logs/laravel-2025-11-04.log
storage/logs/laravel-2025-11-03.log
```

---

## 🎯 **Next Steps:**

1. **Attempt to create a user** through the web interface
2. **Note any error message** shown to you
3. **Check the logs** using commands above
4. **Share the error details** if you need help

The logging is comprehensive - every failure is captured!

---

*Created: November 5, 2025*  
*Status: Error logging fully implemented*

