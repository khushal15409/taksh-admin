# 📱 Ecommerce API Endpoints - Postman Testing Guide

**Base URL:** `http://127.0.0.1:8004/api`  
**Content-Type:** `application/json` (for GET) or `multipart/form-data` (for POST/PUT)

**Language Header:** `Accept-Language: en` (or `hi`, `gu`)

---

## 🔐 AUTHENTICATION ENDPOINTS

### 1. Send OTP

**POST** `/api/auth/send-otp`

**Headers:**

-   `Accept-Language: en` (optional)

**Body (FormData):**

```
mobile: 9000000001
```

**Response:**

```json
{
    "success": true,
    "message": "OTP sent. Use 1234 for verification (TEST MODE)",
    "data": {
        "expires_at": "2026-01-03 12:00:00"
    }
}
```

**Test Data:**

-   Mobile: `9000000001` to `9000000007`
-   OTP: `1234` (fixed for all requests)

---

### 2. Verify OTP

**POST** `/api/auth/verify-otp`

**Headers:**

-   `Accept-Language: en` (optional)

**Body (FormData):**

```
mobile: 9000000001
otp: 1234
guest_token: guest_token_test_12345678901234567890 (optional)
```

**Response:**

```json
{
    "success": true,
    "message": "OTP verified successfully",
    "data": {
        "user": {
            "id": 1,
            "mobile": "9000000001",
            "name": "John Doe",
            "is_verified": true
        },
        "token": "1|xxxxxxxxxxxxxxxxxxxx"
    }
}
```

**Note:** Save the `token` for authenticated requests. Use it in `Authorization: Bearer {token}` header.

---

### 3. Logout

**POST** `/api/auth/logout`

**Headers:**

-   `Authorization: Bearer {token}` (required)
-   `Accept-Language: en` (optional)

**Body:** None

**Response:**

```json
{
    "success": true,
    "message": "Logged out successfully",
    "data": []
}
```

---

## 📊 DASHBOARD ENDPOINTS

### 4. Get Dashboard Data

**GET** `/api/dashboard`

**Headers:**

-   `Accept-Language: en` (optional)

**Query Parameters:** None

**Response:**

```json
{
  "success": true,
  "message": "Dashboard data loaded successfully",
  "data": {
    "banners": [
      {
        "id": 1,
        "title": "Big Sale - Up to 70% Off",
        "image_url": "https://cdn.example.com/banners/big-sale-70-off.jpg",
        "position": "home_top",
        "redirect_type": "category",
        "redirect_id": 1
      }
    ],
    "trending_products": [
      {
        "id": 1,
        "name": "iPhone 15 Pro",
        "slug": "iphone-15-pro",
        "short_description": "Premium smartphone with cutting-edge technology",
        "price": 94900.00,
        "image_url": "https://via.placeholder.com/500x500?text=iPhone+15+Pro",
        "brand": "Apple"
      }
    ],
    "latest_products": [
      {
        "id": 1,
        "name": "iPhone 15 Pro",
        "slug": "iphone-15-pro",
        "short_description": "Premium smartphone with cutting-edge technology",
        "price": 94900.00,
        "image_url": "https://via.placeholder.com/500x500?text=iPhone+15+Pro",
        "brand": "Apple"
      }
    ],
    "categories": [
      {
        "id": 1,
        "name": "Electronics",
        "slug": "electronics",
        "image_url": "https://cdn.example.com/categories/electronics.jpg",
        "icon_url": "https://cdn.example.com/categories/icons/electronics.png"
      }
    ]
  }
}
```

**Note:** 
- Returns all dashboard data in a single API call
- Banners include only `home_top` and `home_middle` positions that are active and within date range
- Trending products are marked with `is_trending = true` (limit 10)
- Latest products are ordered by `created_at DESC` (limit 10)
- Categories include only parent categories (no sub-categories) with image and icon URLs

---

## 🎨 BANNER ENDPOINTS

### 5. Get Banners

**GET** `/api/banners`

**Headers:**

-   `Accept-Language: en` (optional)

**Query Parameters (optional):**

```
position: home_top (optional: home_top, home_middle, home_bottom, dashboard)
```

**Example URLs:**

-   `/api/banners` (all active banners)
-   `/api/banners?position=home_top` (home top banners only)
-   `/api/banners?position=home_middle` (home middle banners)
-   `/api/banners?position=home_bottom` (home bottom banners)
-   `/api/banners?position=dashboard` (dashboard banners)

**Response:**

```json
{
    "success": true,
    "message": "Banners fetched successfully",
    "data": [
        {
            "id": 1,
            "title": "Big Sale - Up to 70% Off",
            "description": "Shop now and save big on all products",
            "image_url": "https://cdn.example.com/banners/big-sale-70-off.jpg",
            "redirect_type": "category",
            "redirect_id": 1,
            "redirect_url": null,
            "position": "home_top",
            "start_date": "2026-01-01T14:01:11.000000Z",
            "end_date": "2026-01-18T14:01:11.000000Z",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2026-01-03T14:01:11.000000Z",
            "updated_at": "2026-01-03T14:01:11.000000Z"
        }
    ]
}
```

**Note:** Only returns active banners that are within their start_date and end_date range. Results are ordered by `sort_order` ASC, then by `created_at` DESC.

**Test Data:**

-   Positions: `home_top`, `home_middle`, `home_bottom`, `dashboard`
-   Redirect Types: `product`, `category`, `external`, `none`

---

## 📦 PRODUCT ENDPOINTS

### 6. Get Categories

**GET** `/api/categories`

**Headers:**

-   `Accept-Language: en` (optional)

**Query Parameters:** None

**Response:**

```json
{
  "success": true,
  "message": "Categories fetched successfully",
  "data": [...]
}
```

---

### 7. Get Products List

**GET** `/api/products`

**Headers:**

-   `Accept-Language: en` (optional)

**Query Parameters (all optional):**

```
category_id: 4
search: iPhone
min_price: 50000
max_price: 150000
page: 1
limit: 15
```

**Example URLs:**

-   `/api/products`
-   `/api/products?category_id=4`
-   `/api/products?search=iPhone&min_price=50000&max_price=150000`
-   `/api/products?page=1&limit=10`

**Response:**

```json
{
  "success": true,
  "message": "Products fetched successfully",
  "data": {
    "current_page": 1,
    "data": [...],
    "total": 5
  }
}
```

**Test Data:**

-   Category IDs: `1` (Electronics), `2` (Fashion), `3` (Home & Kitchen)
-   Sub-categories: `4` (Mobile Phones), `5` (Laptops), `6` (Headphones), `7` (Men's Clothing), `8` (Women's Clothing), `9` (Shoes)

---

### 8. Get Product Details

**GET** `/api/products/{id}`

**Headers:**

-   `Accept-Language: en` (optional)

**URL Parameters:**

-   `id`: Product ID (1-5)

**Example URLs:**

-   `/api/products/1` (iPhone 15 Pro)
-   `/api/products/2` (Samsung Galaxy S24)
-   `/api/products/3` (Nike Air Max 90)
-   `/api/products/4` (Adidas Ultraboost 22)
-   `/api/products/5` (Sony WH-1000XM5)

**Response:**

```json
{
  "success": true,
  "message": "Product details fetched successfully",
  "data": {
    "id": 1,
    "name": "iPhone 15 Pro",
    "description": "Latest iPhone with A17 Pro chip...",
    "brand": {...},
    "category": {...},
    "variants": [...],
    "images": [...],
    "rating_summary": {
      "average_rating": 4.6,
      "total_reviews": 5,
      "rating_breakup": {
        "5": 3,
        "4": 2,
        "3": 0,
        "2": 0,
        "1": 0
      }
    },
    "reviews": [
      {
        "rating": 5,
        "title": "Perfect!",
        "review": "Everything I expected and more. Fast delivery and great packaging.",
        "verified": true,
        "created_at": "2026-01-02"
      },
      {
        "rating": 4,
        "title": "Solid upgrade",
        "review": "Upgraded from iPhone 13. The improvements are worth it, especially the camera.",
        "verified": false,
        "created_at": "2025-12-31"
      }
    ],
    "questions_answers": [
      {
        "question": "Does this phone support wireless charging?",
        "answers": [
          {
            "answer": "Yes, it supports MagSafe wireless charging and Qi wireless charging.",
            "created_at": "2025-12-20"
          },
          {
            "answer": "Yes, iPhone 15 Pro supports both MagSafe and standard Qi wireless charging.",
            "created_at": "2025-12-21"
          }
        ]
      },
      {
        "question": "What is the battery capacity?",
        "answers": [
          {
            "answer": "The battery capacity is approximately 3274 mAh. It provides all-day battery life.",
            "created_at": "2025-12-23"
          }
        ]
      }
    ]
  }
}
```

**Note:** 
- Returns complete product details including variants, images, brand, and category
- Includes rating summary with average rating, total reviews count, and star-wise breakup (1-5)
- Shows latest 10 approved reviews (sorted by latest first)
- Shows latest 5 approved questions with their approved answers
- Only approved reviews, questions, and answers are included in the response

---

## 🛒 CART ENDPOINTS

### 9. Add to Cart

**POST** `/api/cart/add`

**Headers:**

-   `Authorization: Bearer {token}` (optional - for logged-in users)
-   `Accept-Language: en` (optional)

**Body (FormData):**

```
product_variant_id: 1
qty: 2
guest_token: guest_token_test_12345678901234567890 (required if not logged in)
```

**Response:**

```json
{
  "success": true,
  "message": "Item added to cart successfully",
  "data": {
    "cart": {
      "items": [...],
      "total": 189800.00
    },
    "guest_token": "guest_token_test_12345678901234567890"
  }
}
```

**Test Data:**

-   Product Variant IDs: `1-11`
-   Guest Tokens: `guest_token_test_12345678901234567890`, `guest_token_test_09876543210987654321`

---

### 10. Get Cart

**GET** `/api/cart/cart`

**Headers:**

-   `Authorization: Bearer {token}` (optional - for logged-in users)
-   `Accept-Language: en` (optional)

**Query Parameters:**

```
guest_token: guest_token_test_12345678901234567890 (required if not logged in)
```

**Example URLs:**

-   `/api/cart/cart?guest_token=guest_token_test_12345678901234567890`
-   `/api/cart/cart` (with Bearer token)

**Response:**

```json
{
    "success": true,
    "message": "Cart fetched successfully",
    "data": {
        "cart": {
            "items": [
                {
                    "id": 1,
                    "product_variant_id": 1,
                    "product_name": "iPhone 15 Pro",
                    "sku": "IPH15P-128-BLK",
                    "price": 94900.0,
                    "qty": 2,
                    "total": 189800.0,
                    "image": "https://via.placeholder.com/500x500?text=iPhone+15+Pro+128GB"
                }
            ],
            "total": 189800.0
        },
        "guest_token": "guest_token_test_12345678901234567890"
    }
}
```

---

### 11. Update Cart Item

**PUT** `/api/cart/update`

**Headers:**

-   `Authorization: Bearer {token}` (optional - for logged-in users)
-   `Accept-Language: en` (optional)

**Body (FormData):**

```
cart_item_id: 1
qty: 3
guest_token: guest_token_test_12345678901234567890 (required if not logged in)
```

**Response:**

```json
{
  "success": true,
  "message": "Cart updated successfully",
  "data": {
    "cart": {
      "items": [...],
      "total": 284700.00
    }
  }
}
```

---

### 12. Remove Cart Item

**DELETE** `/api/cart/item/{id}`

**Headers:**

-   `Authorization: Bearer {token}` (optional - for logged-in users)
-   `Accept-Language: en` (optional)

**URL Parameters:**

-   `id`: Cart Item ID

**Query Parameters:**

```
guest_token: guest_token_test_12345678901234567890 (required if not logged in)
```

**Example URLs:**

-   `/api/cart/item/1?guest_token=guest_token_test_12345678901234567890`
-   `/api/cart/item/1` (with Bearer token)

**Response:**

```json
{
  "success": true,
  "message": "Item removed from cart",
  "data": {
    "cart": {
      "items": [...],
      "total": 0
    }
  }
}
```

---

## 🏠 ADDRESS ENDPOINTS (Requires Authentication)

### 13. Add Address

**POST** `/api/address/add`

**Headers:**

-   `Authorization: Bearer {token}` (required)
-   `Accept-Language: en` (optional)

**Body (FormData):**

```
state_id: 1
city_id: 1
area_id: 1
name: John Doe
mobile: 9000000001
address_line_1: 123, ABC Street
address_line_2: Near XYZ Mall (optional)
pincode: 380009
landmark: Opposite Bank (optional)
type: home (optional: home, work, other)
is_default: true (optional: true/false)
```

**Test Data:**

-   State IDs: `1` (Gujarat), `2` (Maharashtra), `3` (Delhi)
-   City IDs: `1` (Ahmedabad), `2` (Surat), `3` (Mumbai), `4` (Pune), `5` (New Delhi)
-   Area IDs: `1-7`

**Response:**

```json
{
  "success": true,
  "message": "Address added successfully",
  "data": {
    "id": 1,
    "address_line_1": "123, ABC Street",
    "state": {...},
    "city": {...},
    "area": {...}
  }
}
```

---

### 14. Get Addresses

**GET** `/api/address/addresses`

**Headers:**

-   `Authorization: Bearer {token}` (required)
-   `Accept-Language: en` (optional)

**Query Parameters:** None

**Response:**

```json
{
  "success": true,
  "message": "Addresses fetched successfully",
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "address_line_1": "123, ABC Street",
      "pincode": "380009",
      "state": {...},
      "city": {...},
      "area": {...}
    }
  ]
}
```

---

## 🛍️ ORDER ENDPOINTS (Requires Authentication)

### 15. Place Order

**POST** `/api/order/place`

**Headers:**

-   `Authorization: Bearer {token}` (required)
-   `Accept-Language: en` (optional)

**Body (FormData):**

```
address_id: 1
warehouse_id: 1
delivery_type: 30_min (required: 30_min, 1_day, normal)
payment_method: cod (required: cod, online)
```

**Test Data:**

-   Address IDs: `1-5` (must belong to logged-in user)
-   Warehouse IDs: `1` (Ahmedabad), `2` (Mumbai), `3` (Delhi)
-   Delivery Types: `30_min`, `1_day`, `normal`
-   Payment Methods: `cod`, `online`

**Note:** User must have items in cart. Cart will be cleared after order placement.

**Response:**

```json
{
  "success": true,
  "message": "Order placed successfully",
  "data": {
    "id": 1,
    "order_number": "ORD00000001",
    "total_amount": 104899.00,
    "order_status": "pending",
    "items": [...]
  }
}
```

---

### 16. Get Orders List

**GET** `/api/order/orders`

**Headers:**

-   `Authorization: Bearer {token}` (required)
-   `Accept-Language: en` (optional)

**Query Parameters (optional):**

```
page: 1
limit: 15
```

**Example URLs:**

-   `/api/order/orders`
-   `/api/order/orders?page=1&limit=10`

**Response:**

```json
{
  "success": true,
  "message": "Orders fetched successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "order_number": "ORD00000001",
        "total_amount": 104899.00,
        "order_status": "delivered",
        "items": [...]
      }
    ]
  }
}
```

---

### 17. Get Order Details

**GET** `/api/order/orders/{id}`

**Headers:**

-   `Authorization: Bearer {token}` (required)
-   `Accept-Language: en` (optional)

**URL Parameters:**

-   `id`: Order ID (1-4)

**Example URLs:**

-   `/api/order/orders/1`
-   `/api/order/orders/2`

**Response:**

```json
{
  "success": true,
  "message": "Order details fetched successfully",
  "data": {
    "id": 1,
    "order_number": "ORD00000001",
    "total_amount": 104899.00,
    "order_status": "delivered",
    "payment_status": "paid",
    "items": [...],
    "address": {...},
    "warehouse": {...},
    "payments": [...]
  }
}
```

---

## 💳 PAYMENT ENDPOINTS (Requires Authentication)

### 18. Initiate Payment

**POST** `/api/payment/initiate`

**Headers:**

-   `Authorization: Bearer {token}` (required)
-   `Accept-Language: en` (optional)

**Body (FormData):**

```
order_id: 1
gateway: razorpay (required: razorpay, paytm, stripe)
```

**Test Data:**

-   Order IDs: `1-4` (must belong to logged-in user and have payment_method = 'online')
-   Gateways: `razorpay`, `paytm`, `stripe`

**Response:**

```json
{
    "success": true,
    "message": "Payment initiated successfully",
    "data": {
        "payment_id": 1,
        "transaction_id": "TXNXXXXXXXXXXXX",
        "amount": 84930.0,
        "gateway": "razorpay"
    }
}
```

---

### 19. Verify Payment

**POST** `/api/payment/verify`

**Headers:**

-   `Authorization: Bearer {token}` (required)
-   `Accept-Language: en` (optional)

**Body (FormData):**

```
payment_id: 1
transaction_id: TXNXXXXXXXXXXXX
status: success (required: success, failed)
```

**Response:**

```json
{
  "success": true,
  "message": "Payment verified successfully",
  "data": {
    "id": 1,
    "status": "success",
    "order": {...}
  }
}
```

---

## 🔄 RETURN ENDPOINTS (Requires Authentication)

### 20. Request Return

**POST** `/api/return/request`

**Headers:**

-   `Authorization: Bearer {token}` (required)
-   `Accept-Language: en` (optional)

**Body (FormData):**

```
order_id: 1
order_item_id: 1
reason: Product damaged during delivery. Screen has cracks. (minimum 10 characters)
```

**Test Data:**

-   Order IDs: `1-4` (must belong to logged-in user and have order_status = 'delivered')
-   Order Item IDs: `1-4` (must belong to the order)

**Response:**

```json
{
  "success": true,
  "message": "Return requested successfully",
  "data": {
    "id": 1,
    "reason": "Product damaged during delivery. Screen has cracks.",
    "status": "pending",
    "order": {...},
    "orderItem": {...}
  }
}
```

---

## 📋 POSTMAN COLLECTION SETUP

### Environment Variables

Create a Postman environment with:

```
base_url: http://127.0.0.1:8004/api
token: (will be set after login)
guest_token: guest_token_test_12345678901234567890
```

### Headers (Global)

```
Accept-Language: en
Content-Type: application/json (for GET)
Content-Type: multipart/form-data (for POST/PUT)
```

### Authentication Flow

1. **Send OTP:** POST `/api/auth/send-otp` with `mobile: 9000000001`
2. **Verify OTP:** POST `/api/auth/verify-otp` with `mobile: 9000000001`, `otp: 1234`
3. **Save Token:** Copy `data.token` from response
4. **Set Authorization:** Add header `Authorization: Bearer {token}` for protected routes

### Test Sequence

1. Get dashboard → `/api/dashboard` (all dashboard data in one call)
2. Get banners → `/api/banners` or `/api/banners?position=home_top`
3. Get categories → `/api/categories`
4. Get products → `/api/products`
5. Get product details → `/api/products/1`
6. Add to cart (guest) → `/api/cart/add` with `guest_token`
7. Send OTP → `/api/auth/send-otp`
8. Verify OTP → `/api/auth/verify-otp` (save token)
9. Get cart (user) → `/api/cart/cart` with Bearer token
10. Add address → `/api/address/add` with Bearer token
11. Place order → `/api/order/place` with Bearer token
12. Get orders → `/api/order/orders` with Bearer token
13. Request return → `/api/return/request` with Bearer token

---

## 🧪 TEST DATA SUMMARY

### Users

-   Verified: `9000000001` to `9000000005`
-   Unverified: `9000000006`, `9000000007`
-   OTP: `1234` (fixed for all)

### Products

-   Product IDs: `1-5`
-   Variant IDs: `1-11`

### Locations

-   States: `1-3`
-   Cities: `1-5`
-   Areas: `1-7`

### Warehouses

-   Warehouse IDs: `1-3`

### Orders

-   Order IDs: `1-4`
-   Order Numbers: `ORD00000001` to `ORD00000004`

### Guest Carts

-   Guest Tokens: `guest_token_test_12345678901234567890`, `guest_token_test_09876543210987654321`

---

## ⚠️ IMPORTANT NOTES

1. **FormData:** All POST/PUT requests use `multipart/form-data`, not JSON
2. **OTP:** Fixed to `1234` for all mobile numbers (TEST MODE)
3. **Language:** Set `Accept-Language` header for translations (en, hi, gu)
4. **Authentication:** Use `Bearer {token}` for protected routes
5. **Guest Cart:** Use `guest_token` parameter when not logged in
6. **Cart Merging:** Guest cart automatically merges when user logs in (if `guest_token` provided in verify-otp)

---

## 🔗 QUICK REFERENCE

| Endpoint                 | Method | Auth | FormData |
| ------------------------ | ------ | ---- | -------- |
| `/api/auth/send-otp`     | POST   | ❌   | ✅       |
| `/api/auth/verify-otp`   | POST   | ❌   | ✅       |
| `/api/auth/logout`       | POST   | ✅   | ❌       |
| `/api/dashboard`          | GET    | ❌   | ❌       |
| `/api/banners`            | GET    | ❌   | ❌       |
| `/api/categories`         | GET    | ❌   | ❌       |
| `/api/products`          | GET    | ❌   | ❌       |
| `/api/products/{id}`     | GET    | ❌   | ❌       |
| `/api/cart/add`          | POST   | ⚠️   | ✅       |
| `/api/cart/cart`         | GET    | ⚠️   | ❌       |
| `/api/cart/update`       | PUT    | ⚠️   | ✅       |
| `/api/cart/item/{id}`    | DELETE | ⚠️   | ❌       |
| `/api/address/add`       | POST   | ✅   | ✅       |
| `/api/address/addresses` | GET    | ✅   | ❌       |
| `/api/order/place`       | POST   | ✅   | ✅       |
| `/api/order/orders`      | GET    | ✅   | ❌       |
| `/api/order/orders/{id}` | GET    | ✅   | ❌       |
| `/api/payment/initiate`  | POST   | ✅   | ✅       |
| `/api/payment/verify`    | POST   | ✅   | ✅       |
| `/api/return/request`    | POST   | ✅   | ✅       |

**Legend:**

-   ❌ = Not required
-   ✅ = Required
-   ⚠️ = Optional (works with or without auth)
