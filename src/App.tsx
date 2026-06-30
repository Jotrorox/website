import { For } from 'solid-js'

const primaryLinks = [
  { label: 'mqui', url: 'https://github.com/jotrorox/mqui' },
  { label: 'arduino.c3l', url: 'https://github.com/jotrorox/arduino.c3l' },
  { label: 'sqlodin', url: 'https://github.com/jotrorox/sqlodin' },
  { label: 'austri', url: 'https://github.com/jotrorox/austri' },
  { label: 'depender', url: 'https://depender.jotrorox.com' },
  { label: 'foveara ', url: 'https://foveara.com' },
  { label: 'sickle', url: 'https://sickle-cal.com' },
  { label: 'relaxogames', url: 'https://relaxogames.de' },
]

const socialLinks = [
  { label: 'gh', url: 'https://github.com/jotrorox' },
  { label: 'x', url: 'https://x.com/jotrorox' },
  { label: 'bsky', url: 'https://bsky.app/profile/jotrorox' },
  { label: 'yt', url: 'https://youtube.com/@jotrorox' },
  { label: 'twitch', url: 'https://twitch.tv/jotrorox' },
  { label: 'discord', url: 'https://discord.gg/jotrorox' },
  { label: 'mail', url: 'mailto:mail@jotrorox.com' },
]

export default function App() {
  return (
    <main class="min-h-screen bg-[#09090b] text-[#a1a1aa] font-mono flex flex-col justify-center items-center px-4 selection:bg-neutral-800 selection:text-white">
      <div class="w-full max-w-xs flex flex-col items-start gap-12">
        <header class="flex flex-col gap-1">
          <h1 class="text-white text-lg font-medium tracking-tight">jotrorox</h1>
          <p class="text-sm text-neutral-500">builds things or smth</p>
        </header>

        <nav class="flex flex-col gap-5 w-full" aria-label="Projects">
          <For each={primaryLinks}>
            {(link) => (
              <a
                href={link.url}
                target="_blank"
                rel="noopener noreferrer"
                class="text-sm text-neutral-400 hover:text-white transition-colors duration-150 ease-in-out w-fit"
              >
                {link.label}
              </a>
            )}
          </For>
        </nav>

        <footer class="w-full flex flex-col gap-6">
          <hr class="border-neutral-900 w-full" />

          <div class="flex gap-4 items-center" aria-label="Social links">
            <For each={socialLinks}>
              {(social) => (
                <a
                  href={social.url}
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-xs text-neutral-600 hover:text-neutral-300 transition-colors duration-150 ease-in-out"
                >
                  {social.label}
                </a>
              )}
            </For>
          </div>
        </footer>
      </div>
    </main>
  )
}
