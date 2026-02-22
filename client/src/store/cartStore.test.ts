import { describe, expect, it } from 'vitest';
import { cartReducer, getDefaultSelections, initialState, makeCartKey } from './cartStore';
import type { Product } from '../types/catalog';

const baseProduct: Product = {
  id: 'p1',
  name: 'Tee',
  inStock: true,
  gallery: ['image-1'],
  description: '<p>Desc</p>',
  categoryId: '1',
  brand: 'Brand',
  attributes: [
    {
      id: 'size',
      name: 'Size',
      type: 'text',
      items: [
        { id: 's', displayValue: 'S', value: 'S' },
        { id: 'm', displayValue: 'M', value: 'M' },
      ],
    },
  ],
  prices: [{ amount: 10, currency: { label: 'USD', symbol: '$' } }],
};

describe('cartStore', () => {
  it('builds deterministic cart key independent of selection object ordering', () => {
    const first = makeCartKey('p1', { size: 'm', color: 'red' });
    const second = makeCartKey('p1', { color: 'red', size: 'm' });

    expect(first).toBe(second);
  });

  it('merges same product with same selected attributes', () => {
    const payload = {
      product: baseProduct,
      selectedAttributes: { size: 'm' },
    };

    const once = cartReducer(initialState, { type: 'ADD_ITEM', payload });
    const twice = cartReducer(once, { type: 'ADD_ITEM', payload });

    expect(twice.items).toHaveLength(1);
    expect(twice.items[0]?.quantity).toBe(2);
  });

  it('keeps separate lines for same product with different selections', () => {
    const first = cartReducer(initialState, {
      type: 'ADD_ITEM',
      payload: { product: baseProduct, selectedAttributes: { size: 's' } },
    });

    const second = cartReducer(first, {
      type: 'ADD_ITEM',
      payload: { product: baseProduct, selectedAttributes: { size: 'm' } },
    });

    expect(second.items).toHaveLength(2);
  });

  it('removes item when decrement reaches zero', () => {
    const first = cartReducer(initialState, {
      type: 'ADD_ITEM',
      payload: { product: baseProduct, selectedAttributes: { size: 's' } },
    });
    const itemKey = first.items[0]?.key ?? '';

    const second = cartReducer(first, { type: 'DECREMENT', key: itemKey });

    expect(second.items).toHaveLength(0);
  });

  it('picks first option for every attribute default selection', () => {
    const selections = getDefaultSelections(baseProduct.attributes);
    expect(selections).toEqual({ size: 's' });
  });
});
