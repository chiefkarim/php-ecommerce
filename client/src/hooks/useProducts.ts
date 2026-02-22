import { getProducts } from '../api/catalogApi';
import { useAsyncValue } from './useAsyncValue';

export function useProducts(categoryId: string | null) {
  return useAsyncValue(() => getProducts(categoryId), [categoryId]);
}
