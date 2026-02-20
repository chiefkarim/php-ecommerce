export type Category = {
  name: string;
};

export type Currency = {
  label: string;
  symbol: string;
};

export type Price = {
  amount: number;
  currency: Currency;
};

export type AttributeItem = {
  id: string;
  displayValue: string;
  value: string;
};

export type AttributeSet = {
  id: string;
  name: string;
  type: string;
  items: AttributeItem[];
};

export type Product = {
  id: string;
  name: string;
  inStock: boolean;
  gallery: string[];
  description: string;
  category: string;
  brand: string;
  attributes: AttributeSet[];
  prices: Price[];
};
