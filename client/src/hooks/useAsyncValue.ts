import { useEffect, useState, type DependencyList } from 'react';

type AsyncState<TData> = {
  loading: boolean;
  data: TData | null;
  error: string | null;
};

export function useAsyncValue<TData>(
  factory: () => Promise<TData>,
  deps: DependencyList
): AsyncState<TData> {
  const [state, setState] = useState<AsyncState<TData>>({
    loading: true,
    data: null,
    error: null,
  });

  useEffect(() => {
    let mounted = true;

    setState({ loading: true, data: null, error: null });

    factory()
      .then((data) => {
        if (!mounted) {
          return;
        }

        setState({ loading: false, data, error: null });
      })
      .catch((error: unknown) => {
        if (!mounted) {
          return;
        }

        setState({
          loading: false,
          data: null,
          error: error instanceof Error ? error.message : 'Unexpected error',
        });
      });

    return () => {
      mounted = false;
    };
  }, deps);

  return state;
}
