import { getCategories } from '../api/catalogApi';
import { useAsyncValue } from './useAsyncValue';

export function useCategories() {
  return useAsyncValue(getCategories, []);
}
