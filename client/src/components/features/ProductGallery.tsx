import { useState, useEffect } from 'react';
import type { Product } from '../../types/catalog';

type ProductGalleryProps = {
  product: Product;
};

export function ProductGallery({ product }: ProductGalleryProps): JSX.Element {
  const [activeImageIndex, setActiveImageIndex] = useState(0);

  useEffect(() => {
    setActiveImageIndex(0);
  }, [product.id]);

  const currentImage = product.gallery[activeImageIndex] ?? product.gallery[0] ?? '';

  const goToPreviousImage = () => {
    setActiveImageIndex((index) => {
      if (index === 0) return Math.max(0, product.gallery.length - 1);
      return index - 1;
    });
  };

  const goToNextImage = () => {
    setActiveImageIndex((index) => {
      if (index === product.gallery.length - 1) return 0;
      return index + 1;
    });
  };

  return (
    <>
      <div className="order-2 flex gap-3 overflow-x-auto md:order-1 md:max-h-[560px] md:flex-col md:overflow-y-auto md:overflow-x-hidden md:pr-4 md:[scrollbar-gutter:stable]">
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
    </>
  );
}
