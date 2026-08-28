/**
 * Motion for marina.newyorkcity.
 *
 * Everything here is an enhancement: with this file absent the site still
 * renders, still navigates and still reads. Nothing is hidden by CSS that only
 * JS can bring back — .fade-up is revealed by a synchronous in-viewport pass
 * before any observer is involved, so a page can never come up blank.
 */

document.documentElement.classList.remove('no-js');

const EASE_OUT = 'transform .6s cubic-bezier(.22,1,.36,1)';

/* ---------------------------------------------------------------- reveals -- */

const revealed = (el) => el.classList.contains('is-visible');

const reveal = (el) => {
    if (revealed(el)) {
        return;
    }

    const delay = Number(el.dataset.delay || 0);

    if (delay) {
        window.setTimeout(() => el.classList.add('is-visible'), delay);
    } else {
        el.classList.add('is-visible');
    }
};

const revealObserver = new IntersectionObserver(
    (entries) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                reveal(entry.target);
                revealObserver.unobserve(entry.target);
            }
        }
    },
    { threshold: 0.08, rootMargin: '0px 0px -60px 0px' },
);

const fadeUps = () => Array.from(document.querySelectorAll('.fade-up'));

/**
 * Anything already on screen is revealed immediately. An IntersectionObserver
 * callback that never lands must not be able to hide a whole page.
 */
const revealInView = () => {
    for (const el of fadeUps()) {
        const rect = el.getBoundingClientRect();

        if (rect.top < window.innerHeight && rect.bottom > 0) {
            reveal(el);
        }
    }
};

fadeUps().forEach((el) => revealObserver.observe(el));
revealInView();
requestAnimationFrame(revealInView);
window.setTimeout(() => fadeUps().forEach(reveal), 2600);

/* ------------------------------------------------- header state + parallax -- */

/**
 * Everything below this line moves things. Somebody who has asked their system
 * for less motion gets none of it: the CSS can only make these instant, not
 * absent, because they are written as inline transforms.
 */
const wantsLessMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const header = document.querySelector('.site-header');
const parallaxLayers = Array.from(document.querySelectorAll('[data-parallax]'));

/**
 * Pictures inside a fixed frame drift against the scroll. The CSS gives them a
 * 1.12 base scale, so the frame stays filled while the image travels; --py is
 * composed with the cursor offset rather than overwriting it.
 */
const driftImages = Array.from(
    document.querySelectorAll('.card-media img, .event-row-media img'),
);

const onScroll = () => {
    if (header) {
        header.classList.toggle('is-stuck', window.scrollY > 40);
    }

    if (wantsLessMotion) {
        return;
    }

    for (const layer of parallaxLayers) {
        const rect = layer.getBoundingClientRect();
        const speed = parseFloat(layer.dataset.parallax) || 0.15;
        const offset = (rect.top + rect.height / 2 - window.innerHeight / 2) * -speed;

        layer.style.transform = 'translate3d(0,' + offset.toFixed(1) + 'px,0)';
    }

    for (const image of driftImages) {
        const frame = image.parentElement;
        const rect = frame.getBoundingClientRect();

        if (rect.bottom < -200 || rect.top > window.innerHeight + 200) {
            continue;
        }

        const progress = (rect.top + rect.height / 2 - window.innerHeight / 2) / window.innerHeight;
        const offset = Math.max(-26, Math.min(26, progress * -30));

        image.style.setProperty('--py', offset.toFixed(1) + 'px');
    }
};

window.addEventListener('scroll', onScroll, { passive: true });
window.addEventListener('resize', onScroll);
onScroll();

/* ------------------------------------------------------ cursor-follow cards -- */

const hasFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

if (hasFinePointer && !wantsLessMotion) {
    for (const card of document.querySelectorAll('.card, .event-row')) {
        const image = card.querySelector('img');

        if (!image) {
            continue;
        }

        card.addEventListener('mousemove', (event) => {
            const rect = card.getBoundingClientRect();
            const dx = ((event.clientX - rect.left) / rect.width - 0.5) * 14;
            const dy = ((event.clientY - rect.top) / rect.height - 0.5) * 14;

            // Custom properties, not `transform` — the scroll drift owns --py and
            // writing the whole transform here would cancel it.
            image.style.setProperty('--mx', dx.toFixed(1) + 'px');
            image.style.setProperty('--my', dy.toFixed(1) + 'px');
            image.style.setProperty('--sc', '1.2');
        });

        card.addEventListener('mouseleave', () => {
            image.style.removeProperty('--mx');
            image.style.removeProperty('--my');
            image.style.removeProperty('--sc');
        });
    }

    /* ------------------------------------------------------ magnetic buttons -- */

    for (const button of document.querySelectorAll('.btn')) {
        button.addEventListener('mousemove', (event) => {
            const rect = button.getBoundingClientRect();
            const dx = (event.clientX - rect.left - rect.width / 2) * 0.28;
            const dy = (event.clientY - rect.top - rect.height / 2) * 0.34;

            button.style.transition = 'transform .12s ease-out';
            button.style.transform =
                'translate3d(' + dx.toFixed(1) + 'px,' + dy.toFixed(1) + 'px,0)';
        });

        button.addEventListener('mouseleave', () => {
            button.style.transition = EASE_OUT;
            button.style.transform = '';
        });
    }
}

/* ------------------------------------------------------------- mobile nav -- */

const navToggle = document.querySelector('.nav-toggle');
const siteNav = document.querySelector('.site-nav');

if (navToggle && siteNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = siteNav.classList.toggle('is-open');

        navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
}

/* --------------------------------------------------------- anchor scrolling -- */

for (const anchor of document.querySelectorAll('a[href^="#"]')) {
    anchor.addEventListener('click', (event) => {
        const id = anchor.getAttribute('href');

        if (id === '#' || id.length < 2) {
            return;
        }

        const target = document.querySelector(id);

        if (!target) {
            return;
        }

        event.preventDefault();
        window.scrollTo({
            top: target.getBoundingClientRect().top + window.scrollY - 90,
            behavior: 'smooth',
        });
    });
}
