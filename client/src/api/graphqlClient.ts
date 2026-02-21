export class GraphQLErrorResponse extends Error {
  constructor(public readonly messages: string[]) {
    super(messages.join('; '));
    this.name = 'GraphQLErrorResponse';
  }
}

export type GraphQLResponse<TData> = {
  data?: TData;
  errors?: Array<{ message: string }>;
};

const envEndpoint = import.meta.env.VITE_GRAPHQL_ENDPOINT as string | undefined;
const envBackendUrl = import.meta.env.VITE_BACKEND_URL as string | undefined;

const normalizeEndpoint = (endpoint: string): string => {
  if (endpoint.startsWith('/')) {
    return endpoint;
  }

  try {
    const url = new URL(endpoint);
    if (url.pathname === '' || url.pathname === '/') {
      url.pathname = '/graphql';
    }
    return url.toString();
  } catch {
    return endpoint;
  }
};

const API_URL = envEndpoint
  ? normalizeEndpoint(envEndpoint)
  : envBackendUrl
    ? normalizeEndpoint(envBackendUrl)
    : '/graphql';

export async function graphqlRequest<TData, TVariables extends Record<string, unknown> | undefined>(
  query: string,
  variables?: TVariables
): Promise<TData> {
  const response = await fetch(API_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ query, variables }),
  });

  if (!response.ok) {
    throw new Error(`GraphQL request failed with status ${response.status}`);
  }

  const json = (await response.json()) as GraphQLResponse<TData>;

  if (json.errors && json.errors.length > 0) {
    throw new GraphQLErrorResponse(json.errors.map((entry) => entry.message));
  }

  if (!json.data) {
    throw new Error('Missing GraphQL data payload');
  }

  return json.data;
}
