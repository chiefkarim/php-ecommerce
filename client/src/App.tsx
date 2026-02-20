import { Navigate, Route, Routes } from 'react-router-dom';
import { CartProvider } from './store/cartStore';
import { AppLayout } from './components/features/AppLayout';
import { CategoryPage } from './pages/CategoryPage';
import { HomeRedirectPage } from './pages/HomeRedirectPage';
import { ProductPage } from './pages/ProductPage';

export function App(): JSX.Element {
  return (
    <CartProvider>
      <Routes>
        <Route element={<AppLayout />}>
          <Route index element={<HomeRedirectPage />} />
          <Route path="category/:categoryName" element={<CategoryPage />} />
          <Route path="product/:productId" element={<ProductPage />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Route>
      </Routes>
    </CartProvider>
  );
}
