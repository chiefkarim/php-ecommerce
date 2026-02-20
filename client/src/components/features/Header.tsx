import { Link, useLocation } from 'react-router-dom';
import { useCategories } from '../../hooks/useCategories';
import { useCart } from '../../store/cartStore';
import { ErrorState } from '../base/ErrorState';
import { LoadingState } from '../base/LoadingState';

function getCategoryPath(name: string): string {
  return `/category/${encodeURIComponent(name)}`;
}

export function Header(): JSX.Element {
  const location = useLocation();
  const { loading, error, data } = useCategories();
  const { totalItems, openOverlay } = useCart();

  return (
    <header className="sticky top-0 z-30 bg-white px-6 py-4 shadow-sm md:px-20">
      <div className="mx-auto flex max-w-7xl items-center justify-between gap-6">
        <nav className="flex min-h-14 flex-wrap items-end gap-2 text-base uppercase">
          {loading ? <LoadingState label="Loading categories" /> : null}
          {error ? <ErrorState message={error} /> : null}
          {data?.map((category) => {
            const href = getCategoryPath(category.name);
            const isActive = location.pathname === href;

            return (
              <Link
                key={category.name}
                to={href}
                data-testid={isActive ? 'active-category-link' : 'category-link'}
                className={[
                  'border-b-2 px-4 pb-4 pt-2 font-medium transition-colors',
                  isActive ? 'border-primary text-primary' : 'border-transparent text-ink',
                ].join(' ')}
              >
                {category.name}
              </Link>
            );
          })}
        </nav>

        <button
          type="button"
          data-testid="cart-btn"
          onClick={openOverlay}
          className="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200"
          aria-label="Open cart"
        >
          <svg viewBox="0 0 20 20" className="h-5 w-5 fill-none stroke-[#43464E] stroke-[1.6]">
            <path d="M1 2h2l2.6 9.2a1 1 0 0 0 1 .8h7.6a1 1 0 0 0 1-.7L18 5H6.3" />
            <circle cx="8" cy="16" r="1.4" />
            <circle cx="15" cy="16" r="1.4" />
          </svg>
          {totalItems > 0 ? (
            <span className="absolute -right-2 -top-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-ink px-1 text-xs font-bold text-white">
              {totalItems}
            </span>
          ) : null}
        </button>
      </div>
    </header>
  );
}
