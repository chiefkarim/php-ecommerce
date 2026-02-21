import type { Config } from 'tailwindcss';

export default {
  content: ['./index.html', './src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      colors: {
        primary: '#5ECE7B',
        ink: '#1D1F22',
        muted: '#8D8F9A',
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
        brand: ['Raleway', 'ui-sans-serif', 'system-ui'],
      },
      boxShadow: {
        card: '0 4px 35px rgba(168, 172, 176, 0.19)',
      },
    },
  },
  plugins: [],
} satisfies Config;
