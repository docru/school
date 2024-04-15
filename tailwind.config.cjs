/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'tw-',
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['Roboto'],
      },
      colors: {
        'sidebar-menu-dark-hover': '#2a2d36',
        'sidebar-menu-active': '#EFF6FF',
        'sidebar-menu-dark-active': '#264069',
        'sidebar-blue': '#263851',
        'sidebar-dark-blue': '#2B2F35',
        'sidebar-border': '#262626',
        'main': '#F9FBFF',
        'dark-main': '#1A1C21',
        'dark-area': '#2A2D36',
        'area': '#F2F2F2',
        'gray-dark': '#6B7280',
        'gray-blue': '#9197A3',
        'gray-blue-light': '#EEF2F9',
        'gray-light': '#252830',
        'gray-light-light': '#F4F4F4',
        'gray-icon': '#7C7C7C',
        'gray': '#959595',
        'gray-white': '#E2E2EA',
        'green': '#3DD598',
        'classic-blue': '#2065FE',
        'hover-blue': '#1D4ED8',
        'blue-light': '#60A5FA',
        'blue-menu': '#142B49',
        'blue-hover': '#EEF6FE',
        'dark-gray-white': '#252830',
        'table-gray': '#F8FAFF',
        'table-header': '#EEF6FE',
        'dark-table-gray': '#393D49',
        'gradient-from': '#773BF6',
        'gradient-to': '#3BBEF6',
        'black': '#030712',
        'white': '#fff',
        'stroke': '#F3F7FF',
        'yellow': '#FFCE54',
        'pink': '#FF9AD5',
        'orange': '#FFA654',
      },
      boxShadow: {
        'sidebar': '5px 0px 10px 0px rgba(0, 0, 0, 0.05)',
        'stat-card': '6px 6px 36px 0px rgba(59, 130, 246, 0.10)',
      },
      zIndex: {
        '1': '1',
        '2': '2',
        '3': '3',
      },
      borderRadius: {
        '20': '20px'
      }
    },
  },
  plugins: [],
}

