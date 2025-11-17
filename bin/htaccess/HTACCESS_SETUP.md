# URL Rewriting with .htaccess - Setup Guide

## ✅ What This Does

This `.htaccess` file allows users to access PHP files **without the `.php` extension**.

### Examples:
```
Instead of:  http://localhost:8000/App/View/course.php
Access as:   http://localhost:8000/App/View/course

Instead of:  http://localhost:8000/App/View/student.php
Access as:   http://localhost:8000/App/View/student

Instead of:  http://localhost:8000/App/View/department.php
Access as:   http://localhost:8000/App/View/department
```

---

## 🔧 Setup Instructions

### Step 1: Verify .htaccess File Location
The `.htaccess` file should be in:
```
App/View/.htaccess
```

### Step 2: Enable mod_rewrite in Apache

**For XAMPP on Windows:**

1. Open Apache configuration file:
   ```
   C:\xampp\apache\conf\httpd.conf
   ```

2. Find this line (around line 140):
   ```apache
   #LoadModule rewrite_module modules/mod_rewrite.so
   ```

3. **Remove the `#` to uncomment it:**
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

4. Also find and verify this is uncommented (around line 250):
   ```apache
   <Directory "${SRVROOT}/htdocs">
       AllowOverride All
   </Directory>
   ```
   
   Make sure `AllowOverride All` is set (not `None`)

5. Save the file and restart Apache:
   ```powershell
   # Stop Apache
   net stop Apache2.4
   
   # Start Apache
   net start Apache2.4
   ```

### Step 3: Test the Configuration

Try accessing a page without `.php`:
```
http://localhost:8000/App/View/course
```

You should see the `course.php` page load.

---

## 📝 How It Works

The `.htaccess` rules:

```apache
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-zA-Z0-9_-]+)/?$ $1.php [L]
```

**Explanation:**
- `RewriteCond %{REQUEST_FILENAME} !-f` - Don't rewrite if it's an actual file
- `RewriteCond %{REQUEST_FILENAME} !-d` - Don't rewrite if it's an actual directory
- `RewriteRule ^([a-zA-Z0-9_-]+)/?$ $1.php [L]` - Rewrite `coursename` → `coursename.php`

---

## 🎯 Usage Examples

### Access Pages Without Extension:

| File | Old URL | New URL |
|------|---------|---------|
| `course.php` | `/course.php` | `/course` |
| `student.php` | `/student.php` | `/student` |
| `enrollment.php` | `/enrollment.php` | `/enrollment` |
| `department.php` | `/department.php` | `/department` |
| `user.php` | `/user.php` | `/user` |
| `index.php` | `/index.php` | `/index` or `/` |

---

## ✨ Features

✅ **Clean URLs** - No `.php` extension visible  
✅ **User-friendly** - Easier to share and remember  
✅ **SEO-friendly** - Better for search engines  
✅ **Backward compatible** - Old URLs with `.php` still work*  
✅ **Automatic** - No code changes needed  

*Note: If you want to redirect `.php` URLs to non-.php URLs, uncomment the alternative rule in the .htaccess file.

---

## 🔗 With Query Parameters

The rewrite also works with query strings:

```
/course?id=1          → course.php?id=1
/student?id=5         → student.php?id=5
/department?code=CS   → department.php?code=CS
```

---

## 🚨 Troubleshooting

### Issue: Pages return 404 error

**Solution:**
1. Verify Apache's `mod_rewrite` is enabled
2. Check that `AllowOverride All` is set in httpd.conf
3. Make sure `.htaccess` file is in the correct location (`App/View/`)
4. Restart Apache after making changes

### Issue: `.htaccess` file not working

**Check 1:** Is the file actually `.htaccess` (not `.htaccess.txt`)?
```powershell
# In PowerShell, show file extensions
Get-ChildItem -Force | Select Name
```

**Check 2:** Is `mod_rewrite` enabled?
```powershell
# In Apache directory, check with
C:\xampp\apache\bin\httpd -M | findstr rewrite
```

You should see:
```
rewrite_module (shared)
```

**Check 3:** Verify file permissions (should be readable)

### Issue: Only some pages work

**Cause:** Maybe special characters in filenames  
**Solution:** The rule allows: `a-z`, `A-Z`, `0-9`, `_`, `-`

For other characters, modify the rule:
```apache
RewriteRule ^(.+?)/?$ $1.php [L]
```

---

## 🔐 Security Note

This `.htaccess` only hides the `.php` extension. It does NOT:
- Hide your file structure
- Protect your code
- Restrict access to files

For access control, use your existing authentication system (already implemented).

---

## 📚 Additional Configurations

### Option 1: Redirect .php URLs to non-.php URLs

Uncomment in `.htaccess`:
```apache
# This forces old URLs to redirect to new format
RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.*\.php\ HTTP/
RewriteRule ^(.+)\.php$ /$1 [R=301,L]
```

**Effect:** `course.php` → `course` (301 redirect)

### Option 2: Handle Directory Index

Add this to `.htaccess`:
```apache
DirectoryIndex index.php index.html
```

This serves `index.php` when accessing a directory.

---

## 🔄 In Your Application

Your PHP files don't need any changes! Everything works as-is:

```php
// In course.php - this all still works normally
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    // Process normally
}

// Links still work the same way
?>
<a href="course.php?id=1">Course 1</a>  <!-- Still works -->
<a href="course?id=1">Course 1</a>      <!-- Also works now -->
```

---

## 📊 Summary

| Item | Status |
|------|--------|
| `.htaccess` created | ✅ Yes |
| Location | `App/View/.htaccess` |
| Requires Apache | ✅ Yes |
| Requires mod_rewrite | ✅ Yes |
| Works with XAMPP | ✅ Yes |
| Breaks existing code | ❌ No |
| SEO impact | ✅ Positive |

---

## 🚀 Next Steps

1. **Enable mod_rewrite** in Apache (httpd.conf)
2. **Restart Apache**
3. **Test access** without `.php` extension
4. **Verify query parameters** work correctly
5. **Deploy** to production

---

## 📞 Quick Reference

**Enable mod_rewrite:**
1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Find line with `#LoadModule rewrite_module`
3. Remove the `#` to uncomment
4. Save and restart Apache

**Test:**
```
http://localhost:8000/App/View/course
```

**Verify working:**
- Page should load
- URL should show without `.php`
- Query parameters should work

---

**Created:** November 15, 2025  
**Status:** ✅ Ready to Use
