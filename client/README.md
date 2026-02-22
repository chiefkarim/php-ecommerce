# Client

React + TypeScript + Tailwind SPA for the Scandi e-commerce frontend.

## Stack
- React 18
- TypeScript
- Vite
- TailwindCSS
- React Router
- Vitest + Testing Library

## Architecture
- `src/api`: GraphQL request layer and operation modules.
- `src/hooks`: UI-facing hooks that consume the API layer.
- `src/store`: cart state, reducer, and persistence.
- `src/components/base`: shared base states/elements.
- `src/components/features`: feature components (header, cards, overlay, PDP view).
- `src/pages`: route-level page composition.
- `src/utils`: formatting and safe HTML parsing utilities.

## Runtime Flow
- `/` resolves categories and redirects to the first category slug.
- `/:slug` renders the category product listing page.
- `/product/:productId` renders the product details page.
- Cart is managed in context/reducer state and persisted in localStorage.
- Place order calls GraphQL mutation, then clears cart on success.

## Scripts
- `pnpm dev`: start development server.
- `pnpm build`: type-check and build production bundle.
- `pnpm test`: run Vitest suite.
- `pnpm preview`: preview production build.

## Environment
Copy template:
```bash
cp .env.example .env
```

Variables:
- `VITE_GRAPHQL_ENDPOINT`: full GraphQL endpoint URL (highest priority).
- `VITE_BACKEND_URL`: backend base URL, used only if `VITE_GRAPHQL_ENDPOINT` is not set. `/graphql` is appended automatically when needed.

Examples:
- Local backend:
  - `VITE_GRAPHQL_ENDPOINT=http://localhost:8000/graphql`

## Troubleshooting
- After changing `.env`, restart Vite dev server.
- Ensure backend is actually running before testing frontend requests.
- For browser CORS issues, verify backend returns `Access-Control-Allow-Origin` for your frontend origin.

## Validation
```bash
pnpm test
pnpm build
```

Latest known result in this workspace:
- both commands passing

## QA checklist
- Header cart button has `data-testid="cart-btn"`.
- Category links expose `category-link` and active link exposes `active-category-link`.
- PLP cards expose `product-${kebab-product-name}`.
- Cart overlay contains required ids:
  - `cart-item-attribute-${attribute-name-kebab}`
  - `cart-item-attribute-${attribute-name-kebab}-${attribute-item-kebab}`
  - `cart-item-attribute-${attribute-name-kebab}-${attribute-item-kebab}-selected`
  - `cart-item-amount-decrease`
  - `cart-item-amount-increase`
  - `cart-item-amount`
  - `cart-total`
- PDP contains:
  - `product-gallery`
  - `product-description`
  - `add-to-cart`
  - `product-attribute-${attribute-kebab}`
