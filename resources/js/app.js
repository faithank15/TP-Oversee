// Lightweight dropdown menus.
// A trigger carries [data-dropdown-trigger] + aria-controls="<menu id>".
// Its menu is position:fixed so it escapes any overflow-clipped ancestor
// (e.g. the scrollable table), and is placed next to the trigger on open.

function closeAllDropdowns() {
    document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => {
        menu.classList.add('hidden');
        const trigger = document.querySelector(`[aria-controls="${menu.id}"]`);
        trigger?.setAttribute('aria-expanded', 'false');
    });
}

function positionMenu(trigger, menu) {
    const rect = trigger.getBoundingClientRect();
    // Right-align the menu with the trigger, placed just below it.
    const left = Math.max(8, rect.right - menu.offsetWidth);
    menu.style.top = `${rect.bottom + 6}px`;
    menu.style.left = `${left}px`;
}

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-dropdown-trigger]');

    if (trigger) {
        event.preventDefault();
        const menu = document.getElementById(trigger.getAttribute('aria-controls'));
        if (!menu) return;

        const willOpen = menu.classList.contains('hidden');
        closeAllDropdowns();

        if (willOpen) {
            menu.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
            positionMenu(trigger, menu);
        }
        return;
    }

    // A click anywhere outside an open menu closes them all.
    if (!event.target.closest('[data-dropdown-menu]')) {
        closeAllDropdowns();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeAllDropdowns();
});

// Clickable rows: a click anywhere on an element with [data-href] navigates there,
// unless it landed on an interactive child (link, button, input, open menu) or the
// user was selecting text.
document.addEventListener('click', (event) => {
    if (event.target.closest('a, button, input, select, label, [data-dropdown-menu]')) return;
    if (window.getSelection()?.toString()) return;

    const row = event.target.closest('[data-href]');
    if (row) window.location = row.getAttribute('data-href');
});

// Any layout shift invalidates the fixed position, so just close.
window.addEventListener('resize', closeAllDropdowns);
window.addEventListener('scroll', closeAllDropdowns, true);
