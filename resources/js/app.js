const revealObserver = new IntersectionObserver(
    (entries) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        }
    },
    { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
);

document.querySelectorAll('.fade-up').forEach((element) => revealObserver.observe(element));

const navToggle = document.querySelector('.nav-toggle');
const siteNav = document.querySelector('.site-nav');

if (navToggle && siteNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = siteNav.classList.toggle('is-open');
        navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
}

document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', (event) => {
        const target = document.querySelector(anchor.getAttribute('href'));

        if (target) {
            event.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
