import { Navigate } from 'react-router-dom';
import { useCategories } from '../hooks/useCategories';
import { ErrorState } from '../components/base/ErrorState';
import { LoadingState } from '../components/base/LoadingState';

export function HomeRedirectPage(): JSX.Element {
  const { loading, error, data } = useCategories();

  if (loading) {
    return <LoadingState label="Resolving first category" />;
  }

  if (error) {
    return <ErrorState message={error} />;
  }

  const firstCategory = data?.[0]?.name;

  if (!firstCategory) {
    return <ErrorState message="No category found" />;
  }

  return <Navigate to={`/category/${encodeURIComponent(firstCategory)}`} replace />;
}
