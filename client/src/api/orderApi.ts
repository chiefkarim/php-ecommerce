import type { PlaceOrderInput, PlaceOrderResult } from '../types/order';
import { graphqlRequest } from './graphqlClient';

type PlaceOrderPayload = {
  placeOrder: PlaceOrderResult;
};

const PLACE_ORDER_MUTATION = `
  mutation PlaceOrder($input: PlaceOrderInput!) {
    placeOrder(input: $input) {
      success
      orderId
      message
    }
  }
`;

export async function placeOrder(input: PlaceOrderInput): Promise<PlaceOrderResult> {
  const payload = await graphqlRequest<PlaceOrderPayload, { input: PlaceOrderInput }>(PLACE_ORDER_MUTATION, {
    input,
  });

  return payload.placeOrder;
}
