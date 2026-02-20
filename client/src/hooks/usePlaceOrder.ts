import { useState } from 'react';
import { placeOrder } from '../api/orderApi';
import type { PlaceOrderInput, PlaceOrderResult } from '../types/order';

type PlaceOrderState = {
  loading: boolean;
  error: string | null;
  result: PlaceOrderResult | null;
};

export function usePlaceOrder() {
  const [state, setState] = useState<PlaceOrderState>({
    loading: false,
    error: null,
    result: null,
  });

  const submit = async (input: PlaceOrderInput): Promise<PlaceOrderResult> => {
    setState({ loading: true, error: null, result: null });

    try {
      const result = await placeOrder(input);
      if (!result.success) {
        setState({ loading: false, error: result.message, result });
        return result;
      }

      setState({ loading: false, error: null, result });
      return result;
    } catch (error) {
      const message = error instanceof Error ? error.message : 'Failed to place order';
      const fallback: PlaceOrderResult = {
        success: false,
        orderId: null,
        message,
      };
      setState({ loading: false, error: message, result: fallback });
      return fallback;
    }
  };

  return {
    ...state,
    submit,
  };
}
