import { useParams } from 'react-router-dom';

export function ProductPage(): JSX.Element {
  const { productId = '' } = useParams();

  return (
    <section className="mx-auto max-w-7xl px-6 py-10 md:px-20">
      <h1 className="text-3xl font-semibold">Product {productId}</h1>
    </section>
  );
}
