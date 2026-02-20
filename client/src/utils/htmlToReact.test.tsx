import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import { htmlToReact } from './htmlToReact';

describe('htmlToReact', () => {
  it('renders allowed tags into React elements', () => {
    const nodes = htmlToReact('<p>Hello <strong>World</strong></p>');
    render(<div>{nodes}</div>);

    expect(screen.getByText('Hello')).toBeInTheDocument();
    expect(screen.getByText('World')).toBeInTheDocument();
  });

  it('does not preserve unsupported tag names', () => {
    const nodes = htmlToReact('<script>alert(1)</script><p>safe</p>');
    render(<div>{nodes}</div>);

    expect(screen.getByText('safe')).toBeInTheDocument();
    expect(document.querySelector('script')).toBeNull();
  });
});
