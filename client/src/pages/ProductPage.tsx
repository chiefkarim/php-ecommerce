import { useParams } from 'react-router-dom';
import { ErrorState } from '../components/base/ErrorState';
import { LoadingState } from '../components/base/LoadingState';
import { ProductPageView } from '../components/features/ProductPageView';
import { useProduct } from '../hooks/useProduct';

export function ProductPage(): JSX.Element {
  const { productId = '' } = useParams();
  const { loading, error, data } = useProduct(productId);

  if (loading) {
    return <LoadingState label="Loading product" />;
  }

  if (error) {
    return <ErrorState message={error} />;
  }

  if (!data) {
    return <ErrorState message="Product not found" />;
  }

  return <ProductPageView product={data} />;
}
