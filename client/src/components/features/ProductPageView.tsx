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
    <section className="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 py-6 md:grid-cols-[88px_1fr_360px] md:px-20 md:py-10">
      <div className="order-2 flex gap-3 overflow-x-auto md:order-1 md:max-h-[560px] md:flex-col md:overflow-y-auto md:overflow-x-hidden">
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
        <div className="relative max-h-[360px] overflow-hidden md:max-h-[560px]">
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
          className="product-description space-y-4 font-roboto text-[16px] leading-[26px] text-ink"
        >
          {descriptionNodes}
        </div>
      </div>
    </section>
  );
}
