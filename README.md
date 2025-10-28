# E-AKSIDESA - Legal Letter Request Management System

A comprehensive full-stack application for managing legal letter requests with modern React frontend, Laravel backend, and Docker deployment.

## 🐳 Quick Start with Docker

### Prerequisites
- Docker Engine 20.10+
- Docker Compose 2.0+

### One-Command Setup
```bash
# Clone the repository
git clone <repository-url>
cd AKSIDESA-PERMINTAAN-SURAT

# Start the application
./start.sh
```

### Access Points
- **Frontend (React)**: http://localhost
- **Backend API**: http://localhost:8000  
- **Database Management**: http://localhost:8080
- **Email Testing**: http://localhost:8025

### Management Commands
```bash
# Start services
./start.sh
# or
make dev

# Stop services  
./stop.sh
# or
make down

# View logs
docker compose logs -f

# View status
docker compose ps
```

---

## 🚀 System Overview

This system provides a complete workflow for legal letter request management with three distinct user roles and multiple access methods:

### **User Roles**
- **Administrator**: Full system access, user management, company management, API key management
- **Operator**: Process requests, manage company relationships, view statistics
- **RW (Read/Write)**: Create and view own legal letter requests

### **Dual System Architecture**
1. **LegalLetter System**: Administrative management of actual legal letters
2. **RequestLegalLetter Workflow**: RW users request → Operators process → Creates LegalLetter

## 📋 Features

### **Core Features**
- ✅ Role-based access control (Administrator, Operator, RW)
- ✅ Company management with user assignments
- ✅ Legal letter CRUD with company relationships
- ✅ Request workflow (Pending → Processing → Completed)
- ✅ API key management for companies
- ✅ RESTful APIs for external integrations
- ✅ Comprehensive test coverage (97 tests, 387 assertions)

### **API Features**
- ✅ Company API for external access to request data
- ✅ RW user authentication API
- ✅ Request management via API
- ✅ Status filtering and statistics

## 🛠 Installation

### Prerequisites
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for frontend assets)

### Setup
```bash
# Clone the repository
git clone <repository-url>
cd permintaan-surat

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start the server
php artisan serve
```

## 🔐 Authentication & Authorization

### Web Authentication
- Session-based authentication for web interface
- Role-based middleware protection
- Automatic company assignment validation

### API Authentication
- **Company API**: API key authentication
- **RW User API**: Laravel Sanctum token authentication

## 📊 Database Schema

### Core Tables
- `users` - User management with roles and company assignments
- `companies` - Company information with API keys
- `legal_letters` - Administrative legal letter management
- `request_legal_letters` - User request workflow
- `legal_letter_company` - Many-to-many relationship with status tracking

## 🔌 API Documentation

## 1. Company API (External Access)

**Authentication**: API Key via `X-API-Key` header or `api_key` query parameter

### Get Company Requests
```http
GET /api/company/requests?status=Pending
X-API-Key: ck_your_api_key_here
```

**Response:**
```json
{
  "success": true,
  "data": {
    "company": {
      "id": 1,
      "name": "PT. Example Company",
      "code": "EXC"
    },
    "requests": [
      {
        "id": 1,
        "title": "Contract Legal Review",
        "description": "Need legal review for vendor contract",
        "status": "Pending",
        "created_at": "2025-10-24T10:00:00Z",
        "requester": {
          "id": 5,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "assignee": null,
        "legal_letter": null
      }
    ],
    "total_count": 1,
    "status_filter": "Pending"
  }
}
```

### Get Company Statistics
```http
GET /api/company/requests/statistics
X-API-Key: ck_your_api_key_here
```

**Response:**
```json
{
  "success": true,
  "data": {
    "company": {
      "id": 1,
      "name": "PT. Example Company",
      "code": "EXC"
    },
    "statistics": {
      "total": 10,
      "pending": 3,
      "processing": 2,
      "completed": 5
    }
  }
}
```

### Get Specific Request
```http
GET /api/company/requests/{id}
X-API-Key: ck_your_api_key_here
```

## 2. RW User API (Mobile/External App)

**Authentication**: Bearer token after login

### Login
```http
POST /api/rw/login
Content-Type: application/json

{
  "email": "rw.user@company.com",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 5,
      "name": "John Doe",
      "email": "john@company.com",
      "role": "RW",
      "company": {
        "id": 1,
        "name": "PT. Example Company",
        "code": "EXC"
      }
    },
    "token": "1|abc123def456...",
    "token_type": "Bearer"
  }
}
```

### Create Request
```http
POST /api/rw/requests
Authorization: Bearer 1|abc123def456...
Content-Type: multipart/form-data

{
  "title": "Legal Letter for Contract Dispute",
  "description": "Need legal assistance for contract dispute with vendor XYZ",
  "ktp_image": [KTP Image File - JPEG/PNG, max 2MB],
  "kk_image": [KK Image File - JPEG/PNG, max 2MB]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Legal letter request created successfully",
  "data": {
    "id": 15,
    "title": "Legal Letter for Contract Dispute",
    "description": "Need legal assistance for contract dispute with vendor XYZ",
    "ktp_image_path": "documents/ktp/abc123.jpg",
    "kk_image_path": "documents/kk/def456.jpg",
    "status": "Pending",
    "requested_by": 5,
    "assigned_to": null,
    "legal_letter_id": null,
    "created_at": "2025-10-24T10:30:00Z",
    "requester": {
      "id": 5,
      "name": "John Doe",
      "email": "john@company.com"
    }
  }
}
```

### Get User Requests
```http
GET /api/rw/requests?status=Processing
Authorization: Bearer 1|abc123def456...
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 5,
      "name": "John Doe",
      "email": "john@company.com"
    },
    "requests": [
      {
        "id": 12,
        "title": "Contract Review Request",
        "status": "Processing",
        "assignee": {
          "id": 3,
          "name": "Jane Smith",
          "email": "jane.operator@company.com"
        },
        "legal_letter": null,
        "created_at": "2025-10-24T09:00:00Z"
      }
    ],
    "total_count": 1,
    "status_filter": "Processing"
  }
}
```

### Get User Profile
```http
GET /api/rw/profile
Authorization: Bearer 1|abc123def456...
```

### Logout
```http
POST /api/rw/logout
Authorization: Bearer 1|abc123def456...
```

## 3. API Key Management (Internal Repository Only)

**Note**: API key management is handled internally through the web interface by system administrators. Companies receive their API keys from administrators and use them for external integrations.

### For System Administrators (Web Interface):
- Generate API keys for companies through the admin panel
- View API key status and usage statistics
- Regenerate or revoke API keys as needed
- Monitor API key usage and last access times

### For Companies (External Integration):
- Receive API key from system administrator
- Use API key for all external API calls via `X-API-Key` header
- Contact administrator for key regeneration or issues

**API Key Format**: `ck_` followed by 60 random characters
**Security**: Keys are securely generated and tracked with usage timestamps

## 🔄 Workflow Examples

### 1. RW User Creates Request (Mobile App)
```javascript
// 1. Login
const loginResponse = await fetch('/api/rw/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'user@company.com',
    password: 'password'
  })
});

const { data: { token } } = await loginResponse.json();

// 2. Create request with file uploads
const formData = new FormData();
formData.append('title', 'Contract Legal Review');
formData.append('description', 'Need legal review for new vendor contract');
formData.append('ktp_image', ktpImageFile); // File input from user
formData.append('kk_image', kkImageFile);   // File input from user

const requestResponse = await fetch('/api/rw/requests', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`
    // Don't set Content-Type for FormData, let browser set it
  },
  body: formData
});

// 3. Check request status
const statusResponse = await fetch('/api/rw/requests?status=Pending', {
  headers: { 'Authorization': `Bearer ${token}` }
});
```

### 2. Company External System Integration
```javascript
// Company dashboard integration
const apiKey = 'ck_your_company_api_key';

// Get all pending requests
const pendingRequests = await fetch('/api/company/requests?status=Pending', {
  headers: { 'X-API-Key': apiKey }
});

// Get company statistics
const stats = await fetch('/api/company/requests/statistics', {
  headers: { 'X-API-Key': apiKey }
});

// Monitor specific request
const requestDetail = await fetch(`/api/company/requests/${requestId}`, {
  headers: { 'X-API-Key': apiKey }
});
```

### 3. Complete Integration Example
```javascript
// Complete company integration workflow
const apiKey = 'ck_your_company_api_key'; // Obtained from system administrator

// Dashboard initialization - get overview
const [requests, stats] = await Promise.all([
  fetch('/api/company/requests', {
    headers: { 'X-API-Key': apiKey }
  }).then(r => r.json()),
  fetch('/api/company/requests/statistics', {
    headers: { 'X-API-Key': apiKey }
  }).then(r => r.json())
]);

// Real-time monitoring
setInterval(async () => {
  const pendingCount = await fetch('/api/company/requests?status=Pending', {
    headers: { 'X-API-Key': apiKey }
  }).then(r => r.json()).then(data => data.data.total_count);
  
  updateDashboard({ pendingCount });
}, 30000); // Check every 30 seconds
```

## 🧪 Testing

Run the comprehensive test suite:

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --filter=ApiKeyManagementTest
php artisan test --filter=CompanyApiTest
php artisan test --filter=RwApiTest
php artisan test --filter=RequestLegalLetterWorkflowTest

# Run with coverage
php artisan test --coverage
```

**Test Coverage:**
- 97 tests with 387 assertions
- Unit tests for models and relationships
- Feature tests for all controllers
- API integration tests
- Role-based access control tests

## 🚦 Status Codes & Error Handling

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (missing/invalid credentials)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `422` - Unprocessable Entity (validation errors)
- `500` - Internal Server Error

### Error Response Format
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

## 🔒 Security Features

- **Role-based Access Control**: Strict permission hierarchy
- **API Key Security**: Secure key generation with usage tracking
- **Token Authentication**: Laravel Sanctum for API security
- **Input Validation**: Comprehensive request validation
- **SQL Injection Protection**: Eloquent ORM with parameter binding
- **CSRF Protection**: Built-in Laravel CSRF protection
- **Rate Limiting**: API rate limiting for external requests

## 📝 License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).
