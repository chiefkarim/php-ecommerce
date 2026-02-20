import { Outlet } from 'react-router-dom';
import { useCart } from '../../store/cartStore';
import { Header } from './Header';
import { CartOverlay } from './CartOverlay';

export function AppLayout(): JSX.Element {
  const { state } = useCart();

  return (
    <div className="min-h-screen bg-white text-ink">
      <Header />
      <main className="relative z-0">
        <Outlet />
      </main>
      {state.isOpen ? <CartOverlay /> : null}
    </div>
  );
}
