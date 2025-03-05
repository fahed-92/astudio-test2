# Order Approval System

A Laravel-based order management system with multi-level approval workflow.

## Features

- Order creation and management
- Multi-level approval workflow
- Order status tracking and history
- Unique sequential order numbering
- Business rule enforcement
- RESTful API endpoints
- Authentication and authorization

## Business Rules

- Order numbers must be unique and sequential (format: ORD000001)
- Orders above $1000 require approval
- Orders must have at least one item
- Approved orders cannot be modified
- Basic order history must be maintained
- Two-level approval process for orders above $1000
- Automatic approval for orders below $1000

## Requirements

- PHP >= 8.1
- Laravel >= 10.0
- MySQL >= 5.7
- Composer
- Node.js & NPM (for frontend assets)

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd order-approval-system
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install NPM dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=order_approval
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations and seeders:
```bash
php artisan migrate:fresh --seed
```

8. Start the development server:
```bash
php artisan serve
```

## API Documentation

### Authentication

First, obtain an authentication token:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'
```

Use the returned token in subsequent requests:
```bash
curl -X GET http://127.0.0.1:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Endpoints

#### Orders

1. Create Order
```bash
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "notes": "Test order",
    "items": [
      {
        "product_name": "Product 1",
        "description": "Description 1",
        "unit_price": 500,
        "quantity": 2
      }
    ]
  }'
```

2. Get All Orders
```bash
curl -X GET http://127.0.0.1:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN"
```

3. Get Single Order
```bash
curl -X GET http://127.0.0.1:8000/api/orders/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

4. Update Order
```bash
curl -X PUT http://127.0.0.1:8000/api/orders/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "notes": "Updated notes",
    "items": [
      {
        "product_name": "Updated Product",
        "description": "Updated Description",
        "unit_price": 600,
        "quantity": 3
      }
    ]
  }'
```

5. Submit Order for Approval
```bash
curl -X POST http://127.0.0.1:8000/api/orders/1/submit-approval \
  -H "Authorization: Bearer YOUR_TOKEN"
```

6. Get Order History
```bash
curl -X GET http://127.0.0.1:8000/api/orders/1/history \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Approvals

1. Process Approval
```bash
curl -X POST http://127.0.0.1:8000/api/orders/1/approve \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "approval_level": "first",
    "status": "approved",
    "notes": "Approved by first level approver"
  }'
```

2. Get Pending Approvals
```bash
curl -X GET http://127.0.0.1:8000/api/pending-approvals \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response Examples

#### Successful Order Creation
```json
{
  "data": {
    "id": 1,
    "order_number": "ORD000001",
    "total_amount": "1000.00",
    "status": "draft",
    "notes": "Test order",
    "items": [
      {
        "id": 1,
        "product_name": "Product 1",
        "description": "Description 1",
        "unit_price": "500.00",
        "quantity": 2,
        "subtotal": "1000.00"
      }
    ]
  }
}
```

#### Order History
```json
{
  "data": [
    {
      "id": 1,
      "order_id": 1,
      "status": "approved",
      "notes": "Order fully approved",
      "changed_by": "Test User",
      "created_at": "2024-03-05T10:00:00.000000Z"
    },
    {
      "id": 2,
      "order_id": 1,
      "status": "pending_approval",
      "notes": "Order submitted for approval",
      "changed_by": "Test User",
      "created_at": "2024-03-05T09:00:00.000000Z"
    }
  ]
}
```

## Testing

Run the test suite:
```bash
php artisan test
```

## Database Structure

### Tables

1. `orders`
   - `id` (primary key)
   - `order_number` (unique)
   - `total_amount`
   - `status`
   - `notes`
   - `created_at`
   - `updated_at`
   - `deleted_at`

2. `order_items`
   - `id` (primary key)
   - `order_id` (foreign key)
   - `product_name`
   - `description`
   - `unit_price`
   - `quantity`
   - `created_at`
   - `updated_at`

3. `order_status_history`
   - `id` (primary key)
   - `order_id` (foreign key)
   - `status`
   - `notes`
   - `changed_by`
   - `created_at`

4. `order_approvals`
   - `id` (primary key)
   - `order_id` (foreign key)
   - `approval_level`
   - `status`
   - `approved_by`
   - `notes`
   - `approved_at`
   - `created_at`
   - `updated_at`

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Author

Fahed
