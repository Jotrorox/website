import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import tailwindcss from '@tailwindcss/vite'

const repoName = process.env.GITHUB_REPOSITORY?.split('/')[1]
const isUserOrOrgPages = repoName?.endsWith('.github.io')

// https://vite.dev/config/
export default defineConfig({
  base: process.env.GITHUB_ACTIONS && repoName && !isUserOrOrgPages ? `/${repoName}/` : '/',
  plugins: [
      svelte(),
      tailwindcss(),
  ],
})
