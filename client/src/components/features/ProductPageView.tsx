import { useEffect, useMemo, useState } from 'react';
import type { Product } from '../../types/catalog';
import { useCart } from '../../store/cartStore';
import { formatPrice, toKebabCase } from '../../utils/format';
import { htmlToReact } from '../../utils/htmlToReact';

export function ProductPageView({ product }: { product: Product }): JSX.Element {
  const { addItem } = useCart();
  const [activeImageIndex, setActiveImageIndex] = useState(0);
  const [selected, setSelected] = useState<Record<string, string>>({});

  useEffect(() => {
    setActiveImageIndex(0);
    setSelected({});
  }, [product.id]);

  const hasAllSelections = useMemo(
    () => product.attributes.every((attribute) => Boolean(selected[attribute.id])),
    [product.attributes, selected]
  );

  const currentImage = product.gallery[activeImageIndex] ?? product.gallery[0] ?? '';
  const descriptionNodes = htmlToReact(product.description);

  const onAddToCart = () => {
    if (!hasAllSelections || !product.inStock) {
      return;
    }

    addItem({ product, selectedAttributes: selected });
  };

  const goToPreviousImage = () => {
    setActiveImageIndex((index) => {
      if (index === 0) {
        return Math.max(0, product.gallery.length - 1);
      }

      return index - 1;
    });
  };

  const goToNextImage = () => {
    setActiveImageIndex((index) => {
      if (index === product.gallery.length - 1) {
        return 0;
      }

      return index + 1;
    });
  };

  return (
    <section className="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-6 py-10 md:grid-cols-[88px_1fr_360px] md:px-20">
      <div className="order-2 flex gap-3 overflow-x-auto md:order-1 md:flex-col">
        {product.gallery.map((image, index) => (
          <button
            key={image}
            type="button"
            onClick={() => setActiveImageIndex(index)}
            className={[
              'h-20 w-20 shrink-0 overflow-hidden border',
              index === activeImageIndex ? 'border-ink' : 'border-transparent',
            ].join(' ')}
            aria-label={`Show image ${index + 1}`}
          >
            <img src={image} alt={`${product.name} ${index + 1}`} className="h-full w-full object-cover" />
          </button>
        ))}
      </div>

      <div className="order-1 md:order-2" data-testid="product-gallery">
        <div className="relative max-h-[560px] overflow-hidden">
          <img src={currentImage} alt={product.name} className="h-full w-full object-contain" />

          {product.gallery.length > 1 ? (
            <>
              <button
                type="button"
                onClick={goToPreviousImage}
                className="absolute left-3 top-1/2 -translate-y-1/2 bg-ink/70 px-3 py-2 text-white"
                aria-label="Previous image"
              >
                ‹
              </button>
              <button
                type="button"
                onClick={goToNextImage}
                className="absolute right-3 top-1/2 -translate-y-1/2 bg-ink/70 px-3 py-2 text-white"
                aria-label="Next image"
              >
                ›
              </button>
            </>
          ) : null}
        </div>
      </div>

      <div className="order-3">
        <h1 className="text-3xl font-semibold">{product.brand}</h1>
        <h2 className="mb-8 mt-4 text-3xl font-normal">{product.name}</h2>

        {product.attributes.map((attribute) => (
          <div key={attribute.id} data-testid={`product-attribute-${toKebabCase(attribute.name)}`} className="mb-6">
            <p className="mb-2 text-base font-bold uppercase">{attribute.name}:</p>
            <div className="flex flex-wrap gap-3">
              {attribute.items.map((item) => {
                const isSelected = selected[attribute.id] === item.id;

                return (
                  <button
                    key={item.id}
                    type="button"
                    onClick={() => setSelected((previous) => ({ ...previous, [attribute.id]: item.id }))}
                    className={[
                      'min-h-10 min-w-14 border px-3 text-sm',
                      isSelected ? 'border-ink bg-ink text-white' : 'border-slate-400 bg-white text-ink',
                      attribute.type === 'swatch' ? 'h-9 w-9 min-w-9 p-0' : '',
                    ].join(' ')}
                    style={attribute.type === 'swatch' ? { backgroundColor: item.value } : undefined}
                  >
                    {attribute.type === 'swatch' ? '' : item.value}
                  </button>
                );
              })}
            </div>
          </div>
        ))}

        <p className="mt-8 text-base font-bold uppercase">Price:</p>
        <p className="mb-8 mt-2 text-2xl font-bold">{product.prices[0] ? formatPrice(product.prices[0]) : '$0.00'}</p>

        <button
          type="button"
          data-testid="add-to-cart"
          disabled={!hasAllSelections || !product.inStock}
          onClick={onAddToCart}
          className={[
            'mb-8 w-full px-8 py-4 text-base font-semibold uppercase text-white',
            hasAllSelections && product.inStock ? 'bg-primary' : 'cursor-not-allowed bg-slate-300',
          ].join(' ')}
        >
          Add to cart
        </button>

        <div data-testid="product-description" className="space-y-4 text-base leading-7 text-ink">
          {descriptionNodes}
        </div>
      </div>
    </section>
  );
}
