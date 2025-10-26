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
      { text: 'Templates', link: '/templates/api' },
      { text: 'Troubleshooting', link: '/troubleshooting' }
    ],

    sidebar: [
      {
        text: 'Getting Started',
        items: [
          { text: 'Installation', link: '/guide/installation' },
          { text: 'Quick Start', link: '/guide/quick-start' }
        ]
      },
      {
        text: 'Fields',
        items: [
          { text: 'Field Types', link: '/guide/field-types' },
          { text: 'Relationships', link: '/guide/relationships' },
          { text: 'Generate from Schema', link: '/guide/generate-from-schema' }
        ]
      },
      {
        text: 'Templates',
        items: [
          { text: 'API Template', link: '/templates/api' },
          { text: 'Customizing Stubs', link: '/templates/customizing-stubs' },
          { text: 'Customizing Field Types', link: '/templates/customizing-field-types' },
          { text: 'Customizing Generators', link: '/templates/customizing-generators' },
          { text: 'Customizing Printers', link: '/templates/customizing-printers' },
          { text: 'Creating Your Own Template', link: '/templates/custom' }
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
  }
})
