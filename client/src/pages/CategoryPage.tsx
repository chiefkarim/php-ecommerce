import { useParams } from 'react-router-dom';

export function CategoryPage(): JSX.Element {
  const { categoryName = 'all' } = useParams();

  return (
    <section className="mx-auto max-w-7xl px-6 py-10 md:px-20">
      <h1 className="text-4xl font-normal capitalize">{categoryName}</h1>
    </section>
  );
}
