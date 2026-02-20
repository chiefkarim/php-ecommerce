import { useMemo } from 'react';
import { toKebabCase } from '../../utils/format';
import { formatPrice } from '../../utils/format';
import { useCart } from '../../store/cartStore';

function itemLabel(totalItems: number): string {
  return totalItems === 1 ? '1 Item' : `${totalItems} Items`;
}

export function CartOverlay(): JSX.Element | null {
  const { state, totalItems, totalAmount, increment, decrement, closeOverlay } = useCart();

  const totalSymbol = useMemo(() => state.items[0]?.unitPrice.currency.symbol ?? '$', [state.items]);

  if (!state.isOpen) {
    return null;
  }

  return (
    <>
      <button
        type="button"
        aria-label="Close cart overlay"
        className="fixed inset-x-0 bottom-0 top-20 z-20 bg-black/55"
        onClick={closeOverlay}
      />

      <aside className="fixed right-6 top-20 z-30 w-[92vw] max-w-[420px] bg-white px-4 py-8 shadow-xl md:right-20">
        <p className="mb-8 text-base font-bold">
          My Bag, <span className="font-medium">{itemLabel(totalItems)}</span>
        </p>

        <div className="max-h-[55vh] space-y-10 overflow-auto pr-2">
          {state.items.map((item) => (
            <article key={item.key} className="grid grid-cols-[1fr_auto_auto] gap-2">
              <div>
                <p className="text-xl font-light">{item.productName}</p>
                <p className="mb-3 mt-2 text-base font-bold">{formatPrice(item.unitPrice)}</p>

                {item.attributes.map((attribute) => {
                  const attributeTestId = `cart-item-attribute-${toKebabCase(attribute.name)}`;

                  return (
                    <div key={attribute.id} data-testid={attributeTestId} className="mb-2">
                      <p className="mb-2 text-sm font-semibold">{attribute.name}:</p>
                      <div className="flex flex-wrap gap-2">
                        {attribute.items.map((option) => {
                          const selected = item.selectedAttributes[attribute.id] === option.id;
                          const baseId = `cart-item-attribute-${toKebabCase(attribute.name)}-${toKebabCase(option.id)}`;

                          return (
                            <span
                              key={option.id}
                              data-testid={selected ? `${baseId}-selected` : baseId}
                              className={[
                                'inline-flex min-h-6 min-w-10 items-center justify-center border px-2 text-xs',
                                selected ? 'border-ink bg-ink text-white' : 'border-slate-300 bg-white text-ink',
                                attribute.type === 'swatch' ? 'h-6 w-6 min-w-6 p-0' : '',
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

              <div className="flex flex-col items-center justify-between">
                <button
                  type="button"
                  data-testid="cart-item-amount-increase"
                  onClick={() => increment(item.key)}
                  className="h-8 w-8 border border-ink text-lg"
                  aria-label="Increase quantity"
                >
                  +
                </button>
                <span data-testid="cart-item-amount" className="text-base font-medium">
                  {item.quantity}
                </span>
                <button
                  type="button"
                  data-testid="cart-item-amount-decrease"
                  onClick={() => decrement(item.key)}
                  className="h-8 w-8 border border-ink text-lg"
                  aria-label="Decrease quantity"
                >
                  -
                </button>
              </div>

              <img src={item.image} alt={item.productName} className="h-44 w-32 object-cover" />
            </article>
          ))}
        </div>

        <div className="mt-10 flex items-center justify-between text-base font-semibold">
          <span>Total</span>
          <span data-testid="cart-total">{`${totalSymbol}${totalAmount.toFixed(2)}`}</span>
        </div>
      </aside>
    </>
  );
}
