import { useMemo } from 'react';
import { useParams } from 'react-router-dom';
import { ProductCard } from '../components/features/ProductCard';
import { useProducts } from '../hooks/useProducts';
import { useCategories } from '../hooks/useCategories';
import { ErrorState } from '../components/base/ErrorState';
import { LoadingState } from '../components/base/LoadingState';

export function CategoryPage(): JSX.Element {
  const { slug = '' } = useParams();
  const normalizedSlug = useMemo(() => decodeURIComponent(slug), [slug]);
  const categoriesState = useCategories();
  const activeCategory = useMemo(
    () => categoriesState.data?.find((category) => category.slug === normalizedSlug) ?? null,
    [categoriesState.data, normalizedSlug]
  );
  const categoryId = activeCategory?.slug === 'all' ? null : activeCategory?.id ?? null;
  const { loading, data, error } = useProducts(categoryId);

  if (categoriesState.loading) {
    return <LoadingState label="Loading categories" />;
  }

  if (categoriesState.error) {
    return <ErrorState message={categoriesState.error} />;
  }

  if (!activeCategory) {
    return <ErrorState message="Category not found" />;
  }

  return (
    <section className="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 md:px-20 md:py-10">
      <h1 className="mb-10 font-brand text-[28px] font-normal capitalize leading-[40px] text-ink sm:mb-12 sm:text-[36px] sm:leading-[56px] md:mb-14 md:text-[42px] md:leading-[67px]">
        {activeCategory.name}
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
