'use strict';

function cameFromOwnSite() {
    if (history.length < 2 || document.referrer === '') {
        return false;
    }

    return new URL(document.referrer).origin === window.location.origin;
}

function handleBackLinkClick(event) {
    if (cameFromOwnSite() === false) {
        return;
    }

    event.preventDefault();
    history.back();
}

document.querySelectorAll('.back-link').forEach((link) => {
    link.addEventListener('click', handleBackLinkClick);
});
