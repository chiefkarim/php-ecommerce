import { Link } from 'react-router-dom';
import type { MouseEvent } from 'react';
import type { Product } from '../../types/catalog';
import { useCart, getDefaultSelections } from '../../store/cartStore';
import { formatPrice, toKebabCase } from '../../utils/format';

export function ProductCard({ product }: { product: Product }): JSX.Element {
  const { addItem } = useCart();
  const primaryPrice = product.prices[0];

  const onQuickShop = (event: MouseEvent<HTMLButtonElement>) => {
    event.preventDefault();
    event.stopPropagation();
    if (!product.inStock) {
      return;
    }

    addItem({
      product,
      selectedAttributes: getDefaultSelections(product.attributes),
    });
  };

  return (
    <Link
      to={`/product/${product.id}`}
      data-testid={`product-${toKebabCase(product.name)}`}
      className="group relative flex flex-col gap-6 p-3 transition hover:shadow-card sm:p-4"
    >
      <div className="relative h-[220px] overflow-hidden sm:h-[280px] md:h-[330px]">
        <img
          src={product.gallery[0] ?? ''}
          alt={product.name}
          className={[
            'h-full w-full object-cover',
            product.inStock ? '' : 'opacity-40 grayscale',
          ].join(' ')}
        />

        {!product.inStock ? (
          <p className="absolute inset-0 flex items-center justify-center font-brand text-[18px] font-normal uppercase leading-[28px] text-muted sm:text-[24px] sm:leading-[38px]">
            Out of stock
          </p>
        ) : null}

        {product.inStock ? (
          <button
            type="button"
            onClick={onQuickShop}
            aria-label={`Quick shop ${product.name}`}
            className="absolute bottom-4 right-4 hidden h-12 w-12 items-center justify-center rounded-full bg-primary text-xl text-white shadow-md group-hover:inline-flex"
          >
            <img src="/white-cart.svg" alt="" className="h-6 w-6" aria-hidden="true" />
          </button>
        ) : null}
      </div>

      <div className="flex flex-col gap-1 font-brand">
        <p
          className={
            product.inStock
              ? 'text-[16px] font-light leading-[24px] sm:text-[18px] sm:leading-[29px]'
              : 'text-[16px] font-light leading-[24px] text-muted sm:text-[18px] sm:leading-[29px]'
          }
        >
          {product.name}
        </p>
        <p
          className={
            product.inStock
              ? 'text-[16px] font-normal leading-[24px] sm:text-[18px] sm:leading-[29px]'
              : 'text-[16px] font-normal leading-[24px] text-muted sm:text-[18px] sm:leading-[29px]'
          }
        >
          {primaryPrice ? formatPrice(primaryPrice) : '$0.00'}
        </p>
      </div>
    </Link>
  );
}
