<script lang="ts">
  import { onMount } from 'svelte';

  let mouseX: number = 0;
  let mouseY: number = 0;

  onMount(() => {
    const handleMouseMove = (e: MouseEvent) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    };

    window.addEventListener('mousemove', handleMouseMove);

    return () => {
      window.removeEventListener('mousemove', handleMouseMove);
    };
  });
</script>

<svelte:head>
	<title>Johannes Müller</title>
	<meta name="description" content="Johannes Müller (@jotrorox) - Software Engineer" />
	<meta property="og:title" content="Johannes Müller" />
	<meta property="og:description" content="Software Engineer @jotrorox" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://jotrorox.com" />
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:title" content="Johannes Müller" />
	<meta name="twitter:description" content="Software Engineer @jotrorox" />
</svelte:head>

<header class="min-h-screen flex items-center justify-center bg-[#282828] text-[#ebdbb2] font-serif">
  <div class="text-center px-4 max-w-md w-full">
    <h1 class="text-4xl md:text-5xl font-bold mb-2 leading-tight">Johannes Müller</h1>
    <p class="text-xl md:text-2xl mb-6 opacity-80">@jotrorox</p>
    <div class="flex flex-wrap justify-center space-x-6 space-y-2">
      <a
        href="https://github.com/jotrorox"
        class="text-[#d5c4a1] hover:text-[#fe8019] transition-colors duration-200 underline decoration-1 underline-offset-4"
        aria-label="GitHub profile"
      >GitHub</a>
      <a
        href="https://jotrorox.com"
        class="text-[#d5c4a1] hover:text-[#fe8019] transition-colors duration-200 underline decoration-1 underline-offset-4"
        aria-label="Personal website"
      >jotrorox.com</a>
      <a
        href="mailto:mail@jotrorox.com"
        class="text-[#d5c4a1] hover:text-[#fe8019] transition-colors duration-200 underline decoration-1 underline-offset-4"
        aria-label="Email"
      >mail@jotrorox.com</a>
      <a
        href="https://discord.gg/RVr4cceFUt"
        class="text-[#d5c4a1] hover:text-[#fe8019] transition-colors duration-200 underline decoration-1 underline-offset-4"
        aria-label="Discord Server"
      >Discord</a>
      <a
        href="https://twitter.com/jotrorox"
        class="text-[#d5c4a1] hover:text-[#fe8019] transition-colors duration-200 underline decoration-1 underline-offset-4"
        aria-label="Twitter"
      >Twitter</a>
      <a
        href="https://www.twitch.tv/jotrorox"
        class="text-[#d5c4a1] hover:text-[#fe8019] transition-colors duration-200 underline decoration-1 underline-offset-4"
        aria-label="Twitch"
      >Twitch</a>
    </div>
  </div>
</header>

<!-- svelte-ignore element_invalid_self_closing_tag -->
<div
  class="fixed pointer-events-none z-50 w-1 h-1 bg-[#fe8019]/80 rounded-full transition-all duration-200 ease-out blur-sm"
  style="left: {mouseX}px; top: {mouseY}px; transform: translate(-50%, -50%);"
/>