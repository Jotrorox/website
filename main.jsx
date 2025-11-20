import React, { useState, useEffect } from "react";
import {
  Github,
  Mail,
  Terminal,
  Code2,
  Cpu,
  Globe,
  ExternalLink,
  Server,
  Database,
  ChevronDown,
  Gamepad2,
  MessageCircle,
} from "lucide-react";

// --- Components ---

const NavLink = ({ href, children }) => (
  <a
    href={href}
    className="text-gray-400 hover:text-emerald-400 transition-colors duration-300 text-sm font-mono uppercase tracking-wider"
  >
    {children}
  </a>
);

const SectionTitle = ({ children, icon: Icon }) => (
  <div className="flex items-center gap-3 mb-8">
    <Icon className="w-6 h-6 text-emerald-500" />
    <h2 className="text-2xl md:text-3xl font-bold text-gray-100 font-mono">
      <span className="text-emerald-500">./</span>
      {children}
    </h2>
    <div className="h-px bg-gray-800 flex-grow ml-4"></div>
  </div>
);

const SkillBadge = ({ name }) => (
  <span className="px-3 py-1 bg-gray-800/50 border border-gray-700 rounded text-xs font-mono text-emerald-300 hover:border-emerald-500/50 transition-colors cursor-default">
    {name}
  </span>
);

const ProjectCard = ({ title, desc, lang, langColor, link, tags }) => (
  <div className="group relative bg-gray-900/40 border border-gray-800 hover:border-emerald-500/30 rounded-lg p-6 transition-all duration-300 hover:-translate-y-1 overflow-hidden">
    <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

    <div className="flex justify-between items-start mb-4">
      <h3 className="text-xl font-bold text-gray-100 group-hover:text-emerald-400 transition-colors">
        {title}
      </h3>
      <div className="flex gap-3">
        {link.includes("github") ? (
          <a
            href={link}
            target="_blank"
            rel="noreferrer"
            className="text-gray-500 hover:text-white transition-colors"
          >
            <Github className="w-5 h-5" />
          </a>
        ) : (
          <a
            href={link}
            target="_blank"
            rel="noreferrer"
            className="text-gray-500 hover:text-white transition-colors"
          >
            <ExternalLink className="w-5 h-5" />
          </a>
        )}
      </div>
    </div>

    <p className="text-gray-400 mb-6 text-sm leading-relaxed">{desc}</p>

    <div className="flex flex-wrap gap-2 mt-auto">
      <span
        className={`text-xs font-bold px-2 py-1 rounded bg-gray-800 ${langColor}`}
      >
        {lang}
      </span>
      {tags.map((tag, i) => (
        <span
          key={i}
          className="text-xs px-2 py-1 rounded bg-gray-800 text-gray-400"
        >
          {tag}
        </span>
      ))}
    </div>
  </div>
);

const SocialLink = ({ href, icon: Icon, label }) => (
  <a
    href={href}
    target="_blank"
    rel="noreferrer"
    className="flex items-center gap-2 px-4 py-2 bg-gray-800/50 hover:bg-gray-800 text-gray-300 hover:text-emerald-400 rounded transition-all duration-300 border border-gray-700/50 hover:border-emerald-500/30"
  >
    <Icon className="w-4 h-4" />
    <span className="font-mono text-sm">{label}</span>
  </a>
);

// --- Main App ---

export default function App() {
  const [scrolled, setScrolled] = useState(false);
  const [terminalText, setTerminalText] = useState("");
  const fullText = "systems_engineer --target=world";

  // Handle scroll for navbar
  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 50);
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  // Typing effect
  useEffect(() => {
    let index = 0;
    const interval = setInterval(() => {
      setTerminalText(fullText.substring(0, index));
      index++;
      if (index > fullText.length) clearInterval(interval);
    }, 100);
    return () => clearInterval(interval);
  }, []);

  return (
    <div className="min-h-screen bg-[#0a0a0a] text-gray-300 selection:bg-emerald-500/30 selection:text-emerald-200 font-sans">
      {/* Background Grid Pattern */}
      <div
        className="fixed inset-0 z-0 opacity-[0.03] pointer-events-none"
        style={{
          backgroundImage:
            "linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px)",
          backgroundSize: "40px 40px",
        }}
      ></div>

      {/* Navbar */}
      <nav
        className={`fixed top-0 w-full z-50 transition-all duration-300 ${scrolled ? "bg-[#0a0a0a]/90 backdrop-blur-md border-b border-gray-800" : "bg-transparent"}`}
      >
        <div className="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
          <div className="font-mono text-xl font-bold text-emerald-500 tracking-tighter">
            &lt;Jotrorox /&gt;
          </div>
          <div className="hidden md:flex gap-8">
            <NavLink href="#about">About</NavLink>
            <NavLink href="#projects">Projects</NavLink>
            <NavLink href="#stack">Stack</NavLink>
            <NavLink href="#contact">Contact</NavLink>
          </div>
        </div>
      </nav>

      {/* Hero Section */}
      <header className="relative min-h-screen flex flex-col justify-center px-6 pt-16 z-10 max-w-6xl mx-auto">
        <div className="space-y-6">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-900/20 border border-emerald-500/20 text-emerald-400 text-xs font-mono animate-fade-in">
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Available for collaboration
          </div>

          <h1 className="text-5xl md:text-7xl lg:text-8xl font-extrabold text-white tracking-tight leading-tight">
            Johannes Müller
            <span className="block text-gray-600 mt-2">
              aka <span className="text-emerald-500">Jotrorox</span>
            </span>
          </h1>

          <div className="h-8 font-mono text-lg md:text-xl text-emerald-400/80">
            $ {terminalText}
            <span className="animate-pulse">_</span>
          </div>

          <p className="max-w-xl text-gray-400 text-lg md:text-xl leading-relaxed">
            16-year-old student and hobby developer from Germany. Passionate
            about low-level programming, Arch Linux, and building efficient
            tools in Rust, Odin, and C.
          </p>

          <div className="flex gap-4 pt-4">
            <a
              href="#projects"
              className="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded transition-colors duration-200"
            >
              View Work
            </a>
            <a
              href="https://github.com/jotrorox"
              target="_blank"
              className="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded transition-colors duration-200 flex items-center gap-2"
            >
              <Github className="w-4 h-4" /> GitHub
            </a>
          </div>
        </div>

        <div className="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce text-gray-600">
          <ChevronDown className="w-6 h-6" />
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-6xl mx-auto px-6 py-20 space-y-32 z-10 relative">
        {/* About Section */}
        <section id="about" className="scroll-mt-24">
          <SectionTitle icon={Terminal}>About Me</SectionTitle>
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div className="space-y-6 text-gray-400 leading-relaxed">
              <p>
                I've been programming for over 5 years, starting my journey with
                Java before diving deep into the world of systems programming.
                Currently based in Germany, I spend my time exploring the
                intricacies of{" "}
                <span className="text-gray-200 font-medium">Arch Linux</span>{" "}
                and kernel hacking.
              </p>
              <p>
                I embrace the "do it yourself" mentality—whether it's writing my
                own assembler, configuring a custom Neovim environment, or
                co-developing platforms like RelaxoGames. I believe in
                understanding how things work under the hood to write better,
                more efficient code.
              </p>
            </div>
            <div className="bg-gray-900 border border-gray-800 p-6 rounded-lg font-mono text-sm text-gray-300 shadow-2xl">
              <div className="flex gap-2 mb-4 border-b border-gray-800 pb-4">
                <div className="w-3 h-3 rounded-full bg-red-500/50"></div>
                <div className="w-3 h-3 rounded-full bg-yellow-500/50"></div>
                <div className="w-3 h-3 rounded-full bg-green-500/50"></div>
              </div>
              <div className="space-y-2">
                <p>
                  <span className="text-purple-400">const</span>{" "}
                  <span className="text-blue-400">profile</span> = {"{"}
                </p>
                <p className="pl-4">
                  name:{" "}
                  <span className="text-green-400">"Johannes Müller"</span>,
                </p>
                <p className="pl-4">
                  born: <span className="text-orange-400">2008</span>,
                </p>
                <p className="pl-4">
                  location: <span className="text-green-400">"Germany"</span>,
                </p>
                <p className="pl-4">
                  os: <span className="text-green-400">"Arch Linux"</span>,
                </p>
                <p className="pl-4">
                  editors: [<span className="text-green-400">"Neovim"</span>],
                </p>
                <p className="pl-4">
                  focus: [<span className="text-green-400">"Rust"</span>,{" "}
                  <span className="text-green-400">"Odin"</span>,{" "}
                  <span className="text-green-400">"Kernel"</span>]
                </p>
                <p>{"}"};</p>
              </div>
            </div>
          </div>
        </section>

        {/* Stack Section */}
        <section id="stack" className="scroll-mt-24">
          <SectionTitle icon={Cpu}>Tech Stack</SectionTitle>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="space-y-4">
              <h3 className="text-gray-200 font-semibold flex items-center gap-2">
                <Code2 className="w-4 h-4 text-emerald-500" /> Languages
              </h3>
              <div className="flex flex-wrap gap-2">
                {[
                  "Rust",
                  "Odin",
                  "C",
                  "C++",
                  "Lua",
                  "Haskell",
                  "Gleam",
                  "Assembly",
                  "Java",
                  "JavaScript",
                ].map((s) => (
                  <SkillBadge key={s} name={s} />
                ))}
              </div>
            </div>
            <div className="space-y-4">
              <h3 className="text-gray-200 font-semibold flex items-center gap-2">
                <Server className="w-4 h-4 text-emerald-500" /> Systems & Tools
              </h3>
              <div className="flex flex-wrap gap-2">
                {["Arch Linux", "Nix", "Git", "Neovim", "Bash", "Docker"].map(
                  (s) => (
                    <SkillBadge key={s} name={s} />
                  ),
                )}
              </div>
            </div>
            <div className="space-y-4">
              <h3 className="text-gray-200 font-semibold flex items-center gap-2">
                <Database className="w-4 h-4 text-emerald-500" /> Learning
              </h3>
              <div className="flex flex-wrap gap-2">
                {["Kernel Dev", "Compilers", "Networking", "Embedded"].map(
                  (s) => (
                    <SkillBadge key={s} name={s} />
                  ),
                )}
              </div>
            </div>
          </div>
        </section>

        {/* Projects Section */}
        <section id="projects" className="scroll-mt-24">
          <SectionTitle icon={Code2}>Selected Projects</SectionTitle>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <ProjectCard
              title="RelaxoGames"
              desc="One of two lead developers for RelaxoGames.de, a browser-based gaming platform."
              lang="Web"
              langColor="text-purple-400"
              tags={["Co-Developer", "Game Dev", "Web"]}
              link="https://relaxogames.de"
            />
            <ProjectCard
              title="Supervocab"
              desc="A spaced repetition add-on for Supernotes built with Rust. Helps users memorize concepts efficiently within their note-taking workflow."
              lang="Rust"
              langColor="text-orange-400"
              tags={["CLI", "Education", "Supernotes"]}
              link="https://github.com/Jotrorox/supervocab"
            />
            <ProjectCard
              title="Austri"
              desc="A simple yet robust HTTP Server Library for the Odin programming language. Built to understand the low-level mechanics of web servers."
              lang="Odin"
              langColor="text-blue-400"
              tags={["Networking", "Library", "HTTP"]}
              link="https://github.com/Jotrorox/austri"
            />
            <ProjectCard
              title="Jasm"
              desc="A simple Assembler written from scratch in C for educational purposes. Converts assembly mnemonics into machine code."
              lang="C"
              langColor="text-slate-400"
              tags={["Low-level", "Education", "Compiler"]}
              link="https://github.com/Jotrorox/jasm"
            />
            <ProjectCard
              title="Neovim Config"
              desc="A highly customized Neovim configuration using lazy.nvim. Designed for speed and efficiency in a terminal environment."
              lang="Lua"
              langColor="text-blue-300"
              tags={["Editor", "Productivity", "Tools"]}
              link="https://github.com/Jotrorox/nvim"
            />
            <ProjectCard
              title="FizzBuzz API"
              desc="Showcasing FizzBuzz in multiple languages and a dead simple API endpoint for 'fizz buzzing'."
              lang="Multi"
              langColor="text-pink-400"
              tags={["API", "Fun"]}
              link="https://github.com/Jotrorox/fizzbuzz"
            />
          </div>
        </section>

        {/* Contact Section */}
        <section id="contact" className="scroll-mt-24 pb-20">
          <SectionTitle icon={Mail}>Connect</SectionTitle>
          <div className="bg-gradient-to-br from-gray-900 to-gray-900/50 border border-gray-800 rounded-2xl p-8 md:p-12 text-center">
            <h3 className="text-2xl text-white font-bold mb-4">
              Let's build something together
            </h3>
            <p className="text-gray-400 max-w-2xl mx-auto mb-8">
              Whether you want to discuss low-level systems, need help with a
              Rust project, or just want to say hi.
            </p>

            <div className="flex flex-wrap justify-center gap-4">
              <SocialLink
                href="mailto:mail@jotrorox.com"
                icon={Mail}
                label="Email"
              />
              <SocialLink
                href="https://discord.jotrorox.com"
                icon={MessageCircle}
                label="Discord"
              />
              <SocialLink
                href="https://github.com/Jotrorox"
                icon={Github}
                label="GitHub"
              />
              <SocialLink
                href="https://fosstodon.org/@Jotrorox"
                icon={Globe}
                label="Mastodon"
              />
            </div>
          </div>
        </section>
      </main>

      {/* Footer */}
      <footer className="border-t border-gray-900 bg-[#050505] py-8">
        <div className="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
          <div>
            &copy; {new Date().getFullYear()} Johannes Müller. All rights
            reserved.
          </div>
          <div className="mt-2 md:mt-0 font-mono">
            Built with React & Tailwind
          </div>
        </div>
      </footer>
    </div>
  );
}
