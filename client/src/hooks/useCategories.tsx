import { createContext, useContext } from 'react';
import { getCategories } from '../api/catalogApi';
import { useAsyncValue } from './useAsyncValue';
import type { Category } from '../types/catalog';

export type CategoriesState = {
  loading: boolean;
  data: Category[] | null;
  error: string | null;
};

const CategoriesContext = createContext<CategoriesState | null>(null);

export function CategoriesProvider({ children }: { children: React.ReactNode }): JSX.Element {
  const state = useAsyncValue(getCategories, []);

  return <CategoriesContext.Provider value={state}>{children}</CategoriesContext.Provider>;
}

export function useCategories(): CategoriesState {
  const context = useContext(CategoriesContext);
  
  if (!context) {
    throw new Error('useCategories must be used within CategoriesProvider');
  }
  
  return context;
}
