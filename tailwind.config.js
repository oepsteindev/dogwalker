/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './assets/**/*.{js,ts,vue}',
    './assets/styles/*.css'
  ],
  theme: {
    extend: {
      height: {
        'screen': '100vh',
      },
    },
  },
  plugins: [],
}
