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
    <header className="sticky top-0 z-30 h-20 bg-white">
      <div className="mx-auto grid h-full max-w-[1440px] grid-cols-[1fr_auto_1fr] items-center px-6 md:px-[101px]">
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
                  'flex h-14 items-end border-b-2 px-4 pb-3 pt-2 text-[16px] uppercase leading-[19px] transition-colors',
                  isActive
                    ? 'border-primary font-semibold text-primary'
                    : 'border-transparent font-normal text-ink',
                ].join(' ')}
              >
                {category.name}
              </Link>
            );
          })}
        </nav>

        <div className="flex items-center justify-center">
          <img src="/icon.svg" alt="Store" className="h-[41px] w-[41px]" />
        </div>

        <div className="flex items-center justify-end gap-[22px]">
          <button
            type="button"
            data-testid="cart-btn"
            onClick={openOverlay}
            className="relative inline-flex h-10 w-10 items-center justify-center rounded-full"
            aria-label="Open cart"
          >
            <img src="/black-cart.svg" alt="" className="h-5 w-5" aria-hidden="true" />
            {totalItems > 0 ? (
              <span className="absolute -right-2 -top-2 flex h-5 min-w-5 items-center justify-center rounded-full bg-ink px-1 text-xs font-bold text-white">
                {totalItems}
              </span>
            ) : null}
          </button>
        </div>
      </div>
    </header>
  );
}
