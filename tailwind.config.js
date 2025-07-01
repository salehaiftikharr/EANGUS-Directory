/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./**/*.js",
    "./**/*.html",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          light: '#88CBE7',   // Light Blue
          DEFAULT: '#1870AF', // Dark Blue (default "brand")
        },
        accent: {
          yellow: '#FADC24',
          red: '#F6DC24',     
          green: '#6b7f44',
        }
      }
    }
  },
  plugins: [],
}
