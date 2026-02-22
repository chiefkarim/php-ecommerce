# API

GraphQL backend implemented in PHP (no framework), compatible with PHP `8.2` and MySQL `8.0.45`.

## Requirements
- PHP `8.2`
- Composer
- MySQL `8.0.45`
- `DATABASE_URL` env var in format: `mysql://user:password@host:3306/database`

Environment values are auto-loaded from:
- `api/.env`
- fallback: repository root `.env`

## Setup
```bash
cd api
cp .env.example .env
composer install
```

## Seed Database
Uses root `schema.json` as source data.
```bash
cd api
DATABASE_URL='mysql://user:password@localhost:3306/scandiweb' composer seed
```

Seed a single seeder class by name (no namespace):
```bash
cd api
DATABASE_URL='mysql://user:password@localhost:3306/scandiweb' composer seed -- InitialCatalogSeeder
```

## Run Migrations
Migrations are discovered from `src/Infrastructure/Database/Migration` and run in filename order.
```bash
cd api
DATABASE_URL='mysql://user:password@localhost:3306/scandiweb' composer migrate -- up
```

Rollback all migrations (reverse order):
```bash
cd api
DATABASE_URL='mysql://user:password@localhost:3306/scandiweb' composer migrate -- down
```

Run a single migration by class name (no namespace):
```bash
cd api
DATABASE_URL='mysql://user:password@localhost:3306/scandiweb' composer migrate -- up InitialSchemaMigration
```

When running without a class name, the script warns and waits 3 seconds before executing all items.

## Run Locally (PHP built-in server)
```bash
cd api
php -S localhost:8000 -t public
```

GraphQL endpoint:
- `POST /graphql`

## Docker Run
Build from repository root:
```bash
docker build -f api/Dockerfile -t php-ecommerce-api .
```

Run locally:
```bash
docker run --rm -p 8000:8080 -e PORT=8080 -e DATABASE_URL="mysql://user:password@host:3306/scandiweb" php-ecommerce-api
```

## CORS Behavior
`api/public/index.php` currently allows CORS for:
- `http://localhost:<any-port>`
- `https://*.vercel.app`

It also handles `OPTIONS` preflight requests.

## Example Query
```graphql
query {
  categories { id name slug }
  products(categoryId: "1") {
    id
    name
    inStock
    categoryId
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
