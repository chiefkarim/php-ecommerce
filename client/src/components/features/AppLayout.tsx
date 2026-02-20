import { Outlet } from 'react-router-dom';
import { useCart } from '../../store/cartStore';
import { Header } from './Header';

export function AppLayout(): JSX.Element {
  const { state } = useCart();

  return (
    <div className="min-h-screen bg-white text-ink">
      <Header />
      <main className={state.isOpen ? 'relative z-0 bg-black/20' : ''}>
        <Outlet />
      </main>
    </div>
  );
}
