import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "CRUD Templates for Laravel",
  description: "The most customizable CRUD generator for Laravel. Create your own CRUD templates easily.",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Guide', link: '/guide/installation' },
      { text: 'Fields', link: '/guide/field-types' },
      { text: 'Templates', link: '/templates/api' },
    ],

    sidebar: [
      {
        text: 'Getting Started',
        items: [
          { text: 'Overview', link: '/guide/overview' },
          { text: 'Installation', link: '/guide/installation' },
          { text: 'Quick Start', link: '/guide/quick-start' }
        ]
      },
      {
        text: 'Available Templates',
        items: [
          { text: 'API Template', link: '/templates/api' },
          { text: 'Creating Your Own Template', link: '/templates/custom' }
        ]
      },
      {
        text: 'Using Templates',
        items: [
          { text: 'Field Types', link: '/guide/field-types' },
          { text: 'Relationships', link: '/guide/relationships' },
          { text: 'Generate from Schema', link: '/guide/generate-from-schema' }
        ]
      },
      {
        text: 'Customizing Templates',
        items: [
          { text: 'Customizing Stubs', link: '/templates/customizing-stubs' },
          { text: 'Customizing Generators', link: '/templates/customizing-generators' },
          { text: 'Customizing Field Types', link: '/templates/customizing-field-types' },
          { text: 'Customizing Printers', link: '/templates/customizing-printers' }
        ]
      },
      {
        text: 'Resources',
        items: [
          { text: 'Troubleshooting', link: '/troubleshooting' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/jcsoriano/laravel-crud-templates' }
    ],

    search: {
      provider: 'local'
    }
  },

  head: [
    [
      'script',
      { 
        async: '',
        src: 'https://www.googletagmanager.com/gtag/js?id=G-QJVB3KT1FF' 
      }
    ],
    [
      'script',
      {},
      `window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-QJVB3KT1FF');`
    ]
  ]
})
