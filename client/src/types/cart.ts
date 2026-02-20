import type { AttributeSet, Price, Product } from './catalog';

export type SelectedAttributes = Record<string, string>;

export type CartItem = {
  key: string;
  productId: string;
  productName: string;
  image: string;
  attributes: AttributeSet[];
  selectedAttributes: SelectedAttributes;
  quantity: number;
  unitPrice: Price;
};

export type CartState = {
  items: CartItem[];
  isOpen: boolean;
};

export type AddToCartPayload = {
  product: Product;
  selectedAttributes: SelectedAttributes;
};
