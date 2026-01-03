# Route Troubleshooting Guide

## ✅ Route is Registered Correctly

The route `POST api/auth/send-otp` is registered and should be accessible at:
```
POST http://your-domain.com/api/auth/send-otp
```

## 🔍 Common Issues & Solutions

### 1. **Wrong HTTP Method**
❌ **Wrong:** Using GET request  
✅ **Correct:** Use POST request

### 2. **Wrong Content-Type Header**
❌ **Wrong:** `Content-Type: application/json`  
✅ **Correct:** `Content-Type: multipart/form-data` (for FormData)

**In Postman:**
- Go to Body tab
- Select `form-data` (NOT raw JSON)
- Add key: `mobile` with value: `9000000001`

### 3. **Wrong Base URL**
❌ **Wrong:** `http://localhost/api/auth/send-otp`  
✅ **Correct:** `http://127.0.0.1:8004/api/auth/send-otp`

**For this project:**
- Base URL: `http://127.0.0.1:8004`
- Full endpoint: `http://127.0.0.1:8004/api/auth/send-otp`

### 4. **Route Cache Issue**
If routes were recently added/modified, clear cache:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### 5. **Server Configuration**
Ensure your web server (Apache/Nginx) is properly configured to route requests to Laravel's `public/index.php`.

## 📋 Postman Setup Checklist

1. **Method:** POST ✅
2. **URL:** `{{base_url}}/api/auth/send-otp` ✅
3. **Headers:**
   - `Accept-Language: en` (optional)
   - `Content-Type: multipart/form-data` (auto-set by Postman when using form-data)
4. **Body:** 
   - Select `form-data` tab
   - Key: `mobile`
   - Value: `9000000001`
5. **Base URL Variable:** Set `base_url` to `http://127.0.0.1:8004`

## 🧪 Test the Route

### Using cURL:
```bash
curl -X POST http://127.0.0.1:8004/api/auth/send-otp \
  -H "Accept-Language: en" \
  -F "mobile=9000000001"
```

### Using Postman:
1. Method: **POST**
2. URL: `http://127.0.0.1:8004/api/auth/send-otp`
3. Body → form-data:
   - Key: `mobile`
   - Value: `9000000001`
4. Headers:
   - `Accept-Language: en`

## ✅ Expected Response

```json
{
  "success": true,
  "message": "OTP sent. Use 1234 for verification (TEST MODE)",
  "data": {
    "expires_at": "2026-01-03 12:00:00"
  }
}
```

## 🔧 Verify Routes are Loaded

Run this command to see all registered routes:
```bash
php artisan route:list --path=api
```

You should see:
```
POST  api/auth/send-otp .................... Api\AuthController@sendOtp
```

## 🚨 Still Not Working?

1. **Check Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check if server is running:**
   ```bash
   php artisan serve
   ```

3. **Verify .htaccess exists** in `public/` directory

4. **Check web server configuration** (Apache/Nginx) points to `public/` directory

