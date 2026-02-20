import { createElement, type ReactNode } from 'react';

const ALLOWED_TAGS = new Set(['p', 'strong', 'em', 'ul', 'ol', 'li', 'br', 'a', 'h1', 'h2', 'h3', 'h4']);

function mapNode(node: ChildNode, key: string): ReactNode {
  if (node.nodeType === Node.TEXT_NODE) {
    return node.textContent ?? '';
  }

  if (node.nodeType !== Node.ELEMENT_NODE) {
    return null;
  }

  const element = node as HTMLElement;
  const tag = element.tagName.toLowerCase();
  const children = Array.from(element.childNodes).map((child, index) => mapNode(child, `${key}-${index}`));

  if (!ALLOWED_TAGS.has(tag)) {
    return createElement('span', { key }, children);
  }

  if (tag === 'a') {
    const href = element.getAttribute('href') ?? '#';
    return createElement(
      'a',
      {
        key,
        href,
        target: '_blank',
        rel: 'noreferrer',
        className: 'underline',
      },
      children
    );
  }

  return createElement(tag, { key }, children);
}

export function htmlToReact(html: string): ReactNode[] {
  const parser = new DOMParser();
  const document = parser.parseFromString(html, 'text/html');

  return Array.from(document.body.childNodes)
    .map((node, index) => mapNode(node, `node-${index}`))
    .filter(Boolean);
}
