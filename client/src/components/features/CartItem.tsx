import { formatPrice, toKebabCase } from '../../utils/format';
import type { CartItem as CartItemType } from '../../types/cart';

type CartItemProps = {
  item: CartItemType;
  increment: (id: string) => void;
  decrement: (id: string) => void;
};

export function CartItem({ item, increment, decrement }: CartItemProps): JSX.Element {
  return (
    <article className="grid grid-cols-[1fr_auto_auto] gap-2">
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
  );
}
