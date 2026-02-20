export function LoadingState({ label = 'Loading...' }: { label?: string }): JSX.Element {
  return <p className="p-8 text-sm text-muted">{label}</p>;
}
