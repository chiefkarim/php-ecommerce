import { useMemo } from 'react';
import { useParams } from 'react-router-dom';
import { ProductCard } from '../components/features/ProductCard';
import { useProducts } from '../hooks/useProducts';
import { ErrorState } from '../components/base/ErrorState';
import { LoadingState } from '../components/base/LoadingState';

export function CategoryPage(): JSX.Element {
  const { categoryName = 'all' } = useParams();
  const normalizedCategory = useMemo(() => decodeURIComponent(categoryName), [categoryName]);
  const { loading, data, error } = useProducts(normalizedCategory);

  return (
    <section className="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 md:px-20 md:py-10">
      <h1 className="mb-10 font-brand text-[28px] font-normal capitalize leading-[40px] text-ink sm:mb-12 sm:text-[36px] sm:leading-[56px] md:mb-14 md:text-[42px] md:leading-[67px]">
        {normalizedCategory}
      </h1>

      {loading ? <LoadingState label="Loading products" /> : null}
      {error ? <ErrorState message={error} /> : null}

      {data ? (
        <div className="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 sm:gap-x-8 sm:gap-y-12 xl:grid-cols-3 xl:gap-x-10 xl:gap-y-16">
          {data.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      ) : null}
    </section>
  );
}
