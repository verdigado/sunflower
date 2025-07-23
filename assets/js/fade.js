/**
 * Entfernt die 'preload'‑Klasse **nach** dem ersten Frame
 * und blendet später erscheinende Elemente via IntersectionObserver ein.
 */
(() => {
	'use strict';

	const TAGS = [
		'header','main','section','article','aside','nav',
		'figure','img','video','picture', '.wp-block-buttons', '.right-bar',
		'.logo-background', 'p', 'a', '.wp-block-group',
		'blockquote','ul','ol','li','table','form',
		'h1','h2','h3','h4','h5','h6'
	];

	/* 1. Fade‑in für alles, was bereits im Viewport ist ------------- */
	requestAnimationFrame(() =>      // Frame 1: Layout mit opacity 0
		requestAnimationFrame(() =>  // Frame 2: jetzt sichtbar setzen
			document.documentElement.classList.remove('preload')
		)
	);

	/* 2. IntersectionObserver für nachfolgende Elemente ------------- */
	if (!('IntersectionObserver' in window)) return;

	const io = new IntersectionObserver((entries, obs) => {
		entries.forEach(e => {
			if (e.isIntersecting) {
				/* Opacity‑Regel von html.preload ist weg; jetzt Übergang anwenden */
				e.target.style.opacity = '1';
				obs.unobserve(e.target);
			}
		});
	}, { rootMargin: '0px 0px -10% 0px' });

	/* 3. Alle relevanten Elemente anmelden -------------------------- */
	TAGS.forEach(tag =>
		document.querySelectorAll(tag).forEach(el => {
			if (el.getBoundingClientRect().top > window.innerHeight) {
				el.style.opacity = '0';
				io.observe(el);
			}
		})
	);

	/* 4. Aufräumen, wenn Tab inaktiv -------------------------------- */
	document.addEventListener('visibilitychange', () => {
		if (document.hidden) io.disconnect();
	});
})();
