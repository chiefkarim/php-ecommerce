# php-ecommerce

Monorepo for a PHP GraphQL API and React SPA storefront.

## Projects
- `api/`: PHP 8.2 GraphQL backend (MySQL).
- `client/`: React + TypeScript + Tailwind SPA.

## Prerequisites
- PHP `8.2`
- Composer
- MySQL `8.0.45`
- Node.js `18+`
- `pnpm`
- Docker (optional, for API container run/deploy)

## Environment Files
- Backend template: `api/.env.example`
- Frontend template: `client/.env.example`

## Run Locally
1. Backend
```bash
cd api
cp .env.example .env
composer install
composer migrate -- up
composer seed
php -S localhost:8000 -t public
```

2. Frontend
```bash
cd client
cp .env.example .env
pnpm install
pnpm dev
```

## GraphQL Endpoint
- `POST /graphql`
- Local URL: `http://localhost:8000/graphql`

## API Docker (Cloud Run-friendly)
Build from repo root:
```bash
docker build -f api/Dockerfile -t <dockerhub-username>/php-ecommerce-api:latest .
```

Run locally:
```bash
docker run --rm -p 8000:8080 -e PORT=8080 -e DATABASE_URL="mysql://user:password@host:3306/database" <dockerhub-username>/php-ecommerce-api:latest
```

## Deployment Notes
- Frontend can be deployed to Vercel.
- Backend container can be deployed to Cloud Run.
- Backend persistence is provided by MySQL via `DATABASE_URL`.
