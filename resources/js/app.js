import './globals/theme.js'; /* By Sheaf.dev */ 
import './globals/modals.js'; /* By Sheaf.dev */ 
import './tiptap-editor';
import Lenis from 'lenis';
import { animate, scroll, inView, spring, stagger } from 'motion';
import { Livewire, Alpine } from "../../vendor/livewire/livewire/dist/livewire.esm"
import rover from "@sheaf/rover"
import './components/calendar/index.js';
import './components/date-picker/index.js';
import './components/select.js';

// 1. Expose Motion globally for use in Blade / Alpine components
window.motion = { animate, scroll, inView, spring, stagger };

// 2. Initialize Lenis smooth scrolling
const lenis = new Lenis({
  duration: 1.2,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
  direction: 'vertical',
  gestureDirection: 'vertical',
  smooth: true,
  smoothTouch: false,
  touchMultiplier: 2,
});

function raf(time) {
  lenis.raf(time);
  requestAnimationFrame(raf);
}
requestAnimationFrame(raf);

window.lenis = lenis;

Alpine.plugin(rover)

Alpine.data('tableOfContents', () => ({
    items: [],
    activeId: null,
    open: false,

    init() {
        if (this.$el.dataset.tocEnabled === 'false') {
            return
        }

        const prose = this.$el.querySelector('.tiptap-content')
        if (!prose) {
            return
        }

        const headings = prose.querySelectorAll('h2[id], h3[id]')
        if (headings.length < 3) {
            return
        }

        this.items = Array.from(headings).map((h) => ({
            id: h.id,
            text: h.textContent.trim().replace(/^#+\s/, ''),
            level: parseInt(h.tagName.slice(1), 10),
        }))

        const observer = new IntersectionObserver(
            (entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        this.activeId = entry.target.id
                    }
                }
            },
            { rootMargin: '-5% 0px -75% 0px' },
        )

        for (const h of headings) {
            observer.observe(h)
        }
    },

    get visible() {
        return this.items.length >= 3
    },

    toggle() {
        this.open = !this.open
    },
}))

Alpine.data('readingProgress', () => ({
    progress: 0,
    update() {
        const article = document.querySelector('article')
        if (!article) {
            return
        }
        const articleTop = article.offsetTop
        const articleHeight = article.offsetHeight
        const windowHeight = window.innerHeight
        const scrollY = window.scrollY

        const start = articleTop
        const end = articleTop + articleHeight - windowHeight
        if (end <= start) {
            this.progress = 100

            return
        }
        this.progress = Math.min(100, Math.max(0, ((scrollY - start) / (end - start)) * 100))
    },
}))

Livewire.start()