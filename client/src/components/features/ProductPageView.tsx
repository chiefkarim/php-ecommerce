import type { Product } from '../../types/catalog';
import { ProductGallery } from './ProductGallery';
import { ProductDetails } from './ProductDetails';

export function ProductPageView({ product }: { product: Product }): JSX.Element {
  return (
    <section className="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 py-6 md:grid-cols-[104px_1fr_360px] md:px-20 md:py-10">
      <ProductGallery product={product} />
      <ProductDetails product={product} />
    </section>
  );
}
