import { useMemo, useState, useEffect } from 'react';
import type { Product } from '../../types/catalog';
import { formatPrice, toKebabCase } from '../../utils/format';
import { htmlToReact } from '../../utils/htmlToReact';
import { useCart } from '../../hooks/useCart';

type ProductDetailsProps = {
  product: Product;
};

export function ProductDetails({ product }: ProductDetailsProps): JSX.Element {
  const { addItem } = useCart();
  const [selected, setSelected] = useState<Record<string, string>>({});

  useEffect(() => {
    setSelected({});
  }, [product.id]);
  
  const hasAllSelections = useMemo(
    () => product.attributes.every((attribute) => Boolean(selected[attribute.id])),
    [product.attributes, selected]
  );

  const descriptionNodes = htmlToReact(product.description);

  const onAddToCart = () => {
    if (!hasAllSelections || !product.inStock) {
      return;
    }
    addItem({ product, selectedAttributes: selected });
  };

  return (
    <div className="order-3">
      <h1 className="font-brand text-[30px] font-semibold leading-[27px] text-ink">{product.brand}</h1>
      <h2 className="mb-6 mt-3 font-brand text-[30px] font-semibold leading-[27px] text-ink md:mb-8 md:mt-4">
        {product.name}
      </h2>

      {product.attributes.map((attribute) => (
        <div key={attribute.id} data-testid={`product-attribute-${toKebabCase(attribute.name)}`} className="mb-4 md:mb-6">
          <p className="mb-2 font-roboto-condensed text-[18px] font-bold uppercase leading-[18px] text-ink">
            {attribute.name}:
          </p>
          <div className="flex flex-wrap gap-3">
            {attribute.items.map((item) => {
              const isSelected = selected[attribute.id] === item.id;
              const optionTestId = `product-attribute-${toKebabCase(attribute.name)}-${item.displayValue}`;

              return (
                <button
                  key={item.id}
                  type="button"
                  onClick={() => setSelected((previous) => ({ ...previous, [attribute.id]: item.id }))}
                  data-testid={optionTestId}
                  className={[
                    attribute.type === 'swatch'
                      ? [
                          'flex h-9 w-9 min-w-9 items-center justify-center border-0 bg-white p-0',
                          isSelected
                            ? 'ring-1 ring-primary ring-offset-2 ring-offset-white'
                            : 'ring-1 ring-[#A6A6A6]/60 ring-offset-0',
                        ].join(' ')
                      : [
                          'min-h-11 min-w-[63px] border px-3 font-source text-[16px] leading-[18px] tracking-[0.05em]',
                          isSelected ? 'border-ink bg-ink text-white' : 'border-ink bg-white text-ink',
                        ].join(' '),
                  ].join(' ')}
                  style={attribute.type === 'swatch' ? { backgroundColor: item.value } : undefined}
                >
                  {attribute.type === 'swatch' ? (
                    <span className="h-8 w-8" style={{ backgroundColor: item.value }} />
                  ) : (
                    item.value
                  )}
                </button>
              );
            })}
          </div>
        </div>
      ))}

      <p className="mt-6 font-roboto-condensed text-[18px] font-bold uppercase leading-[18px] text-ink md:mt-8">
        Price:
      </p>
      <p className="mb-6 mt-2 font-brand text-[24px] font-bold leading-[18px] text-ink md:mb-8">
        {product.prices[0] ? formatPrice(product.prices[0]) : '$0.00'}
      </p>

      <button
        type="button"
        data-testid="add-to-cart"
        disabled={!hasAllSelections || !product.inStock}
        onClick={onAddToCart}
        className={[
          'mb-6 w-full px-8 py-4 font-brand text-[16px] font-semibold uppercase leading-[19px] text-white md:mb-8',
          hasAllSelections && product.inStock ? 'bg-primary' : 'cursor-not-allowed bg-slate-300',
        ].join(' ')}
      >
        Add to cart
      </button>

      <div
        data-testid="product-description"
        className="product-description space-y-4 font-roboto text-[16px] leading-[26px] text-ink [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1"
      >
        {descriptionNodes}
      </div>
    </div>
  );
}
