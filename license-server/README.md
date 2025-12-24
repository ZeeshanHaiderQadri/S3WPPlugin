# WP Cloud Media Offload - License Server

Complete license management system with Super Admin Dashboard, payment processing, and usage tracking.

## Features

- 🔑 License key generation and management
- 👥 User account management
- 💳 Stripe payment integration
- 📊 Usage tracking (media uploads per user)
- 🎯 Plan management (Free, Bronze, Silver, Gold, Platinum, Unlimited)
- 📈 Analytics and reporting
- 🔔 Email notifications
- 🌐 REST API for WordPress plugin
- 🎨 Modern admin dashboard

## Tech Stack

- **Backend**: Laravel 10 (PHP 8.1+)
- **Database**: MySQL 8.0+
- **Payment**: Stripe
- **Frontend**: Vue.js 3 + Tailwind CSS
- **API**: RESTful API

## Plans

| Plan | Media Limit | Price/Year | Features |
|------|-------------|------------|----------|
| Free | 2,500 | $0 | Basic features |
| Bronze | 2,000 | $39 | Priority support |
| Silver | 6,000 | $59 | Priority support |
| Gold | 20,000 | $149 | Priority support + CloudFront |
| Platinum | 40,000 | $199 | All features |
| Unlimited | Unlimited | $1,199 | Everything + dedicated support |

## Installation

See INSTALLATION.md for detailed setup instructions.

## Quick Start

```bash
# Clone repository
git clone https://github.com/yourcompany/license-server.git
cd license-server

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

## API Endpoints

- `POST /api/v1/activate` - Activate license
- `POST /api/v1/deactivate` - Deactivate license
- `POST /api/v1/check` - Check license status
- `POST /api/v1/track-upload` - Track media upload
- `GET /api/v1/usage` - Get usage statistics

## Admin Dashboard

Access at: `https://your-domain.com/admin`

Default credentials:
- Email: admin@yourcompany.com
- Password: (set during installation)
