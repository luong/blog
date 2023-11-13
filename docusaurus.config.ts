import {themes as prismThemes} from 'prism-react-renderer';
import type {Config} from '@docusaurus/types';
import type * as Preset from '@docusaurus/preset-classic';

const config: Config = {
  title: '12G Dev Blog',
  tagline: 'Discover dev news and issues',
  favicon: 'img/favicon.ico',

  url: 'https://12g.co',
  baseUrl: '/',

  organizationName: 'luong', // Usually your GitHub org/user name.
  projectName: 'luong', // Usually your repo name.

  onBrokenLinks: 'throw',
  onBrokenMarkdownLinks: 'warn',

  i18n: {
    defaultLocale: 'en',
    locales: ['en'],
  },

  presets: [
    [
      'classic',
      {
        docs: false,
        blog: {
          routeBasePath: '/',
          showReadingTime: true,
        },
        theme: {
          customCss: './src/css/custom.css',
        },
        googleTagManager: {
          containerId: 'GTM-NHQ95FQJ',
        },
      } satisfies Preset.Options,
    ],
  ],

  themeConfig: {
    colorMode: {
      defaultMode: 'light',
      disableSwitch: true,
      respectPrefersColorScheme: false,
    },
    image: 'img/logo.png',
    navbar: {
      title: '12G Dev Blog',
      logo: {
        alt: '12G Logo',
        src: 'img/logo.png',
        className: 'nav-logo',
      },
      items: [
        {
          type: 'search',
          position: 'left',
        },
        {
          type: 'html',
          position: 'right',
          value: '<a href="https://x.com/luongfoxx"><img width="24" src="/img/x.png"/></a>',
        },
        {
          type: 'html',
          position: 'right',
          value: '<a href="https://github.com/luong"><img width="24" src="/img/github.png"/></a>',
        },
      ],
    },
    footer: {
      style: 'dark',
      links: [
      ],
      copyright: `Copyright © ${new Date().getFullYear()} * 12G`,
    },
    prism: {
      theme: prismThemes.github,
      darkTheme: prismThemes.oneDark,
    },
  } satisfies Preset.ThemeConfig,
};

export default config;
