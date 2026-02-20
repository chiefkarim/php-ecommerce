import { getProducts } from '../api/catalogApi';
import { useAsyncValue } from './useAsyncValue';

export function useProducts(categoryName: string) {
  return useAsyncValue(() => getProducts(categoryName), [categoryName]);
}
