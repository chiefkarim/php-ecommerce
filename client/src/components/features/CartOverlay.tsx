import { useMemo } from 'react';
import { usePlaceOrder } from '../../hooks/usePlaceOrder';
import { useCart } from '../../hooks/useCart';
import { CartItem } from './CartItem';

function itemLabel(totalItems: number): string {
  return totalItems === 1 ? '1 Item' : `${totalItems} Items`;
}

export function CartOverlay(): JSX.Element | null {
  const { state, totalItems, totalAmount, increment, decrement, closeOverlay, clearCart, toOrderInput } = useCart();
  const { loading, error, submit } = usePlaceOrder();

  const totalSymbol = useMemo(() => state.items[0]?.unitPrice.currency.symbol ?? '$', [state.items]);

  if (!state.isOpen) {
    return null;
  }

  const handlePlaceOrder = async () => {
    if (state.items.length === 0 || loading) {
      return;
    }

    const result = await submit(toOrderInput());
    if (result.success) {
      clearCart();
    }
  };

  return (
    <>
      <button
        type="button"
        aria-label="Close cart overlay"
        className="fixed inset-x-0 bottom-0 top-20 z-20 bg-black/55"
        onClick={closeOverlay}
      />

      <aside
        data-testid="cart-overlay"
        className="fixed right-6 top-20 z-30 w-[92vw] max-w-[420px] bg-white px-4 py-8 shadow-xl md:right-20"
      >
        <p className="mb-8 font-brand text-[16px] font-bold leading-[26px] text-ink">
          My Bag, <span className="font-medium">{itemLabel(totalItems)}</span>
        </p>

        <div className="max-h-[50vh] space-y-10 overflow-auto pr-2">
          {state.items.map((item) => (
            <CartItem key={item.key} item={item} increment={increment} decrement={decrement} />
          ))}
        </div>

        <div className="mt-8 flex items-center justify-between font-brand text-[16px] font-semibold leading-[18px] text-ink">
          <span>Total</span>
          <span data-testid="cart-total">{`${totalSymbol}${totalAmount.toFixed(2)}`}</span>
        </div>

        {error ? <p className="mt-3 text-sm text-red-600">{error}</p> : null}

        <button
          type="button"
          disabled={state.items.length === 0 || loading}
          onClick={handlePlaceOrder}
          className={[
            'mt-8 w-full px-8 py-3 font-brand text-[14px] font-semibold uppercase leading-[17px] text-white',
            state.items.length > 0 && !loading ? 'bg-primary' : 'cursor-not-allowed bg-slate-300',
          ].join(' ')}
        >
          {loading ? 'Processing...' : 'Place order'}
        </button>
      </aside>
    </>
  );
}
