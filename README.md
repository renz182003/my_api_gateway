# PHP API Gateway (my_api_gateway)

## 🔧 Project Overview

This project demonstrates a simple PHP-based API Gateway that provides:

- Routing to internal microservices
- API key-based authentication
- Rate limiting per API key (10 requests/min)
- Request logging
- Basic API documentation

Built using ** PHP** under a standard **XAMPP** environment.

---


---

## ⚙️ Setup Instructions

1. **Install XAMPP** and start **Apache**.
2. Place the `my_api_gateway` folder into your `htdocs` directory.
3. Ensure `mod_rewrite` is enabled in Apache.
4. Ensure the `logs/` and `ratelimit_data/` folders are writable (`chmod 777` if using Linux).
5. Visit `http://localhost/my_api_gateway/docs.html` to view the documentation.

---

## 🔑 API Key Authentication

### Valid API Keys

```php
$valid_api_keys = [
  'key123' => 'UserA',
  'key456' => 'UserB'
];



