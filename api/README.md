# API

## Requirements
- PHP 8.2
- MySQL 8.0.45
- `DATABASE_URL` environment variable (format: `mysql://user:password@host:3306/database`)
- Environment values are auto-loaded from `api/.env` (with fallback to repository root `.env` for local dev)

## Install
```bash
cd api
composer install
```

## Seed database
```bash
DATABASE_URL='mysql://user:pass@localhost:3306/scandiweb' php bin/seed.php
```

## Run locally
```bash
cd api
php -S localhost:8000 -t public
```

GraphQL endpoint: `POST /graphql`

## Example Query
```graphql
query {
  categories { name }
  products(category: "tech") {
    id
    name
    inStock
    prices { amount currency { label symbol } }
  }
}
```

## Example Mutation
```graphql
mutation PlaceOrder($input: PlaceOrderInput!) {
  placeOrder(input: $input) {
    success
    orderId
    message
  }
}
```
