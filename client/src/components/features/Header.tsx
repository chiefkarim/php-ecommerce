import { Link, useLocation } from 'react-router-dom';
import { useState } from 'react';
import { useCategories } from '../../hooks/useCategories';
import { useCart } from '../../store/cartStore';
import { ErrorState } from '../base/ErrorState';
import { LoadingState } from '../base/LoadingState';

function getCategoryPath(name: string): string {
  return `/${encodeURIComponent(name)}`;
}

export function Header(): JSX.Element {
  const location = useLocation();
  const { loading, error, data } = useCategories();
  const { totalItems, openOverlay, closeOverlay, state } = useCart();
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  return (
    <header className="sticky top-0 z-30 h-16 bg-white md:h-20">
      <div className="mx-auto grid h-full max-w-[1440px] grid-cols-[auto_1fr_auto] items-center px-4 md:grid-cols-[1fr_auto_1fr] md:px-[101px]">
        <button
          type="button"
          className="inline-flex h-10 w-10 items-center justify-center md:hidden"
          aria-label="Toggle categories"
          aria-controls="mobile-nav"
          aria-expanded={isMenuOpen}
          onClick={() => setIsMenuOpen((open) => !open)}
        >
          <span className="sr-only">Toggle categories</span>
          <svg viewBox="0 0 24 24" className="h-6 w-6 stroke-ink" fill="none" strokeWidth="2">
            <path strokeLinecap="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <nav className="hidden min-h-14 flex-wrap items-end gap-2 font-brand text-base uppercase md:flex">
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
            onClick={() => (state.isOpen ? closeOverlay() : openOverlay())}
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

      {isMenuOpen ? (
        <>
          <button
            type="button"
            className="fixed inset-0 z-10 bg-black/20 md:hidden"
            aria-label="Close categories"
            onClick={() => setIsMenuOpen(false)}
          />
          <div
            id="mobile-nav"
            className="absolute left-0 right-0 top-full z-20 border-t border-slate-100 bg-white shadow-md md:hidden"
          >
            <nav className="flex flex-col gap-2 px-4 py-4 font-brand text-base uppercase">
              {loading ? <LoadingState label="Loading categories" /> : null}
              {error ? <ErrorState message={error} /> : null}
              {data?.map((category) => {
                const href = getCategoryPath(category.name);
                const isActive = location.pathname === href;

                return (
                  <Link
                    key={category.name}
                    to={href}
                    onClick={() => setIsMenuOpen(false)}
                    data-testid={isActive ? 'active-category-link' : 'category-link'}
                    className={[
                      'border-b-2 pb-2 pt-1 text-[16px] uppercase leading-[19px] transition-colors',
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
          </div>
        </>
      ) : null}
    </header>
  );
}
