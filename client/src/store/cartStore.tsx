import { createContext, useContext, useEffect, useMemo, useReducer } from 'react';
import type { AddToCartPayload, CartItem, CartState, SelectedAttributes } from '../types/cart';
import type { AttributeSet } from '../types/catalog';
import type { PlaceOrderInput } from '../types/order';

const STORAGE_KEY = 'cart:v1';

export type CartAction =
  | { type: 'ADD_ITEM'; payload: AddToCartPayload }
  | { type: 'INCREMENT'; key: string }
  | { type: 'DECREMENT'; key: string }
  | { type: 'REMOVE'; key: string }
  | { type: 'TOGGLE_OPEN'; value?: boolean }
  | { type: 'CLEAR' }
  | { type: 'HYDRATE'; payload: CartState };

export const initialState: CartState = {
  items: [],
  isOpen: false,
};

export function makeCartKey(productId: string, selectedAttributes: SelectedAttributes): string {
  const sorted = Object.entries(selectedAttributes).sort(([left], [right]) => left.localeCompare(right));
  return `${productId}::${sorted.map(([key, value]) => `${key}=${value}`).join('|')}`;
}

function toCartItem(payload: AddToCartPayload): CartItem {
  const primaryPrice = payload.product.prices[0];
  return {
    key: makeCartKey(payload.product.id, payload.selectedAttributes),
    productId: payload.product.id,
    productName: payload.product.name,
    image: payload.product.gallery[0] ?? '',
    attributes: payload.product.attributes,
    selectedAttributes: payload.selectedAttributes,
    quantity: 1,
    unitPrice: primaryPrice,
  };
}

export function cartReducer(state: CartState, action: CartAction): CartState {
  switch (action.type) {
    case 'HYDRATE':
      return action.payload;
    case 'TOGGLE_OPEN':
      return { ...state, isOpen: action.value ?? !state.isOpen };
    case 'ADD_ITEM': {
      const candidate = toCartItem(action.payload);
      const existingIndex = state.items.findIndex((entry) => entry.key === candidate.key);

      if (existingIndex === -1) {
        return { ...state, items: [...state.items, candidate], isOpen: true };
      }

      return {
        ...state,
        isOpen: true,
        items: state.items.map((entry, index) =>
          index === existingIndex ? { ...entry, quantity: entry.quantity + 1 } : entry
        ),
      };
    }
    case 'INCREMENT':
      return {
        ...state,
        items: state.items.map((entry) =>
          entry.key === action.key ? { ...entry, quantity: entry.quantity + 1 } : entry
        ),
      };
    case 'DECREMENT':
      return {
        ...state,
        items: state.items
          .map((entry) =>
            entry.key === action.key ? { ...entry, quantity: Math.max(0, entry.quantity - 1) } : entry
          )
          .filter((entry) => entry.quantity > 0),
      };
    case 'REMOVE':
      return {
        ...state,
        items: state.items.filter((entry) => entry.key !== action.key),
      };
    case 'CLEAR':
      return { ...state, items: [], isOpen: false };
    default:
      return state;
  }
}

type CartContextValue = {
  state: CartState;
  addItem: (payload: AddToCartPayload) => void;
  increment: (key: string) => void;
  decrement: (key: string) => void;
  openOverlay: () => void;
  closeOverlay: () => void;
  clearCart: () => void;
  totalItems: number;
  totalAmount: number;
  toOrderInput: () => PlaceOrderInput;
};

const CartContext = createContext<CartContextValue | null>(null);

export function CartProvider({ children }: { children: React.ReactNode }): JSX.Element {
  const [state, dispatch] = useReducer(cartReducer, initialState);

  useEffect(() => {
    const rawValue = window.localStorage.getItem(STORAGE_KEY);
    if (!rawValue) {
      return;
    }

    try {
      const parsed = JSON.parse(rawValue) as CartState;
      if (Array.isArray(parsed.items)) {
        dispatch({ type: 'HYDRATE', payload: parsed });
      }
    } catch {
      window.localStorage.removeItem(STORAGE_KEY);
    }
  }, []);

  useEffect(() => {
    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
  }, [state]);

  const totalItems = useMemo(
    () => state.items.reduce((sum, item) => sum + item.quantity, 0),
    [state.items]
  );

  const totalAmount = useMemo(
    () => state.items.reduce((sum, item) => sum + item.quantity * item.unitPrice.amount, 0),
    [state.items]
  );

  const value: CartContextValue = {
    state,
    addItem: (payload) => dispatch({ type: 'ADD_ITEM', payload }),
    increment: (key) => dispatch({ type: 'INCREMENT', key }),
    decrement: (key) => dispatch({ type: 'DECREMENT', key }),
    openOverlay: () => dispatch({ type: 'TOGGLE_OPEN', value: true }),
    closeOverlay: () => dispatch({ type: 'TOGGLE_OPEN', value: false }),
    clearCart: () => dispatch({ type: 'CLEAR' }),
    totalItems,
    totalAmount,
    toOrderInput: () => ({
      items: state.items.map((item) => ({
        productId: item.productId,
        quantity: item.quantity,
        selectedAttributes: Object.entries(item.selectedAttributes).map(([attributeId, itemId]) => ({
          attributeId,
          itemId,
        })),
      })),
      totalAmount: Number(totalAmount.toFixed(2)),
    }),
  };

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>;
}

export function useCart(): CartContextValue {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within CartProvider');
  }

  return context;
}

export function getDefaultSelections(attributes: AttributeSet[]): SelectedAttributes {
  return attributes.reduce<SelectedAttributes>((result, attribute) => {
    const firstOption = attribute.items[0];
    if (firstOption) {
      result[attribute.id] = firstOption.id;
    }

    return result;
  }, {});
}
