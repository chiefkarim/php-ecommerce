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
- Production frontend context:
  - `https://php-ecommerce-puce.vercel.app/category/all`

## Troubleshooting
- After changing `.env`, restart Vite dev server.
- Ensure backend is actually running before testing frontend requests.
- For browser CORS issues, verify backend returns `Access-Control-Allow-Origin` for your frontend origin.

## QA checklist
- Header cart button has `data-testid="cart-btn"`.
- Category links expose `category-link` and active link exposes `active-category-link`.
- PLP cards expose `product-${kebab-product-name}`.
- Cart overlay contains all required cart attribute/amount/total test ids.
- PDP contains `product-gallery`, `product-description`, `add-to-cart`, and `product-attribute-${kebab-attribute}` test ids.
