import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
const colors = require('tailwindcss/colors');

export default {
  darkMode: 'class',
  mode: 'jit',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        ...colors,
        iceberg: '#d9ecf2',
        froly: '#f96A79',
        coralred: '#FF414D',
        easternblue: '#1aa6B7',
        prussianblue: '#002D40',
      },
    },
  },
  plugins: [require('preline/plugin'), forms, typography],
};
