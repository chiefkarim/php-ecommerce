import { Link, useLocation } from 'react-router-dom';
import { useCategories } from '../../hooks/useCategories';
import { useCart } from '../../store/cartStore';
import { toKebabCase } from '../../utils/format';
import { ErrorState } from '../base/ErrorState';
import { LoadingState } from '../base/LoadingState';

export function Header(): JSX.Element {
  const location = useLocation();
  const { loading, error, data } = useCategories();
  const { totalItems, openOverlay } = useCart();

  return (
    <header className="sticky top-0 z-30 bg-white px-6 py-4 shadow-sm md:px-20">
      <div className="mx-auto flex max-w-7xl items-center justify-between">
        <nav className="flex min-h-14 items-end gap-4 text-base uppercase">
          {loading ? <LoadingState label="Loading categories" /> : null}
          {error ? <ErrorState message={error} /> : null}
          {data?.map((category) => {
            const href = `/category/${category.name}`;
            const isActive =
              location.pathname === href ||
              (location.pathname === '/' && data[0]?.name === category.name);

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
                {toKebabCase(category.name).replace(/-/g, ' ')}
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
          <span aria-hidden className="text-lg">🛒</span>
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
