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
- `npm run dev`: start development server.
- `npm run build`: type-check and build production bundle.
- `npm run test`: run Vitest suite.
- `npm run preview`: preview production build.

## Environment
Optional:
- `VITE_GRAPHQL_ENDPOINT`: GraphQL endpoint URL (defaults to `/graphql`).

## QA checklist
- Header cart button has `data-testid="cart-btn"`.
- Category links expose `category-link` and active link exposes `active-category-link`.
- PLP cards expose `product-${kebab-product-name}`.
- Cart overlay contains all required cart attribute/amount/total test ids.
- PDP contains `product-gallery`, `product-description`, `add-to-cart`, and `product-attribute-${kebab-attribute}` test ids.
