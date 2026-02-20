import type { Price } from '../types/catalog';

export function formatPrice(price: Price): string {
  return `${price.currency.symbol}${price.amount.toFixed(2)}`;
}

export function toKebabCase(input: string): string {
  return input
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)+/g, '');
}
