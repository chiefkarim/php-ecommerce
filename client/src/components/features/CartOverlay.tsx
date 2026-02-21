import { useMemo } from 'react';
import { usePlaceOrder } from '../../hooks/usePlaceOrder';
import { useCart } from '../../store/cartStore';
import { formatPrice, toKebabCase } from '../../utils/format';

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

      <aside className="fixed right-6 top-20 z-30 w-[92vw] max-w-[420px] bg-white px-4 py-8 shadow-xl md:right-20">
        <p className="mb-8 font-brand text-[16px] font-bold leading-[26px] text-ink">
          My Bag, <span className="font-medium">{itemLabel(totalItems)}</span>
        </p>

        <div className="max-h-[50vh] space-y-10 overflow-auto pr-2">
          {state.items.map((item) => (
            <article key={item.key} className="grid grid-cols-[1fr_auto_auto] gap-2">
              <div className="font-brand text-ink">
                <p className="text-[18px] font-light leading-[29px]">{item.productName}</p>
                <p className="mb-3 mt-2 text-[16px] font-normal leading-[26px]">
                  {formatPrice(item.unitPrice)}
                </p>

                {item.attributes.map((attribute) => {
                  const attributeTestId = `cart-item-attribute-${toKebabCase(attribute.name)}`;

                  return (
                    <div key={attribute.id} data-testid={attributeTestId} className="mb-2">
                      <p className="mb-2 text-[14px] font-normal leading-[16px] text-ink">{attribute.name}:</p>
                      <div className="flex flex-wrap gap-2">
                        {attribute.items.map((option) => {
                          const selected = item.selectedAttributes[attribute.id] === option.id;
                          const baseId = `cart-item-attribute-${toKebabCase(attribute.name)}-${toKebabCase(option.id)}`;

                          return (
                            <span
                              key={option.id}
                              data-testid={selected ? `${baseId}-selected` : baseId}
                              className={[
                                'inline-flex min-h-6 min-w-10 items-center justify-center border px-2 text-[14px] leading-[22px]',
                                selected ? 'border-ink bg-ink text-white' : 'border-ink bg-white text-ink',
                                attribute.type === 'swatch' ? 'h-5 w-5 min-w-5 p-0' : '',
                              ].join(' ')}
                              style={attribute.type === 'swatch' ? { backgroundColor: option.value } : undefined}
                            >
                              {attribute.type === 'swatch' ? '' : option.value}
                            </span>
                          );
                        })}
                      </div>
                    </div>
                  );
                })}
              </div>

              <div className="flex flex-col items-center justify-between font-brand text-ink">
                <button
                  type="button"
                  data-testid="cart-item-amount-increase"
                  onClick={() => increment(item.key)}
                  className="h-6 w-6 border border-ink text-lg leading-none"
                  aria-label="Increase quantity"
                >
                  +
                </button>
                <span data-testid="cart-item-amount" className="text-[16px] font-medium leading-[26px]">
                  {item.quantity}
                </span>
                <button
                  type="button"
                  data-testid="cart-item-amount-decrease"
                  onClick={() => decrement(item.key)}
                  className="h-6 w-6 border border-ink text-lg leading-none"
                  aria-label="Decrease quantity"
                >
                  -
                </button>
              </div>

              <img src={item.image} alt={item.productName} className="h-44 w-32 object-cover" />
            </article>
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
