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
      className="group relative flex flex-col gap-6 p-4 transition hover:shadow-card"
    >
      <div className="relative h-[330px] overflow-hidden">
        <img
          src={product.gallery[0] ?? ''}
          alt={product.name}
          className={[
            'h-full w-full object-cover',
            product.inStock ? '' : 'opacity-40 grayscale',
          ].join(' ')}
        />

        {!product.inStock ? (
          <p className="absolute inset-0 flex items-center justify-center text-2xl font-normal uppercase text-muted">
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
            🛒
          </button>
        ) : null}
      </div>

      <div className="flex flex-col gap-1">
        <p className={product.inStock ? 'text-lg font-light' : 'text-lg font-light text-muted'}>{product.name}</p>
        <p className={product.inStock ? 'text-lg font-semibold' : 'text-lg font-semibold text-muted'}>
          {primaryPrice ? formatPrice(primaryPrice) : '$0.00'}
        </p>
      </div>
    </Link>
  );
}
