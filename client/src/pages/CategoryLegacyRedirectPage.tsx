import { Navigate, useParams } from 'react-router-dom';

export function CategoryLegacyRedirectPage(): JSX.Element {
  const { categoryName } = useParams();

  if (!categoryName) {
    return <Navigate to="/" replace />;
  }

  return <Navigate to={`/${encodeURIComponent(categoryName)}`} replace />;
}
