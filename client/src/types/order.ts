export type SelectedAttributeInput = {
  attributeId: string;
  itemId: string;
};

export type OrderItemInput = {
  productId: string;
  quantity: number;
  selectedAttributes: SelectedAttributeInput[];
};

export type PlaceOrderInput = {
  items: OrderItemInput[];
  totalAmount: number;
};

export type PlaceOrderResult = {
  success: boolean;
  orderId: string | null;
  message: string;
};
