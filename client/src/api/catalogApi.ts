import type { Category, Product } from '../types/catalog';
import { graphqlRequest } from './graphqlClient';

type CategoriesPayload = {
  categories: Category[];
};

type ProductsPayload = {
  products: Product[];
};

type ProductPayload = {
  product: Product | null;
};

const CATEGORIES_QUERY = `
  query GetCategories {
    categories {
      name
    }
  }
`;

const PRODUCTS_QUERY = `
  query GetProducts($category: String) {
    products(category: $category) {
      id
      name
      inStock
      gallery
      description
      category
      brand
      attributes {
        id
        name
        type
        items {
          id
          displayValue
          value
        }
      }
      prices {
        amount
        currency {
          label
          symbol
        }
      }
    }
  }
`;

const PRODUCT_QUERY = `
  query GetProduct($id: String!) {
    product(id: $id) {
      id
      name
      inStock
      gallery
      description
      category
      brand
      attributes {
        id
        name
        type
        items {
          id
          displayValue
          value
        }
      }
      prices {
        amount
        currency {
          label
          symbol
        }
      }
    }
  }
`;

export async function getCategories(): Promise<Category[]> {
  const payload = await graphqlRequest<CategoriesPayload, undefined>(CATEGORIES_QUERY);
  return payload.categories;
}

export async function getProducts(category: string): Promise<Product[]> {
  const payload = await graphqlRequest<ProductsPayload, { category: string }>(PRODUCTS_QUERY, { category });
  return payload.products;
}

export async function getProduct(productId: string): Promise<Product | null> {
  const payload = await graphqlRequest<ProductPayload, { id: string }>(PRODUCT_QUERY, { id: productId });
  return payload.product;
}
