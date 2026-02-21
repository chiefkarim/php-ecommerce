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
    <section className="mx-auto max-w-7xl px-6 py-10 md:px-20">
      <h1 className="mb-14 font-brand text-[42px] font-normal capitalize leading-[67px] text-ink">
        {normalizedCategory}
      </h1>

      {loading ? <LoadingState label="Loading products" /> : null}
      {error ? <ErrorState message={error} /> : null}

      {data ? (
        <div className="grid grid-cols-1 gap-x-10 gap-y-16 sm:grid-cols-2 xl:grid-cols-3">
          {data.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      ) : null}
    </section>
  );
}
