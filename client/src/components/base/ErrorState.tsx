export function ErrorState({ message }: { message: string }): JSX.Element {
  return <p className="p-8 text-sm text-red-600">{message}</p>;
}
