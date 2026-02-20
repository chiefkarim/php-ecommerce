import { getProduct } from '../api/catalogApi';
import { useAsyncValue } from './useAsyncValue';

export function useProduct(productId: string) {
  return useAsyncValue(() => getProduct(productId), [productId]);
}
