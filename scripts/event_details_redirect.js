const eventDivs = document.querySelectorAll('.event');

eventDivs.forEach(div => {
    div.addEventListener('click', (event) => {
        const eventId = div.getAttribute('event-id');
        const type = div.getAttribute('data-type') || 'event';
        let url;
        if (eventId) {
            if (type === 'news') {
                url = `news_details.php?id=${eventId}`;
            } else {
                url = `event_details.php?id=${eventId}`;
            }

            // Détecte le clic molette ou Ctrl/Cmd + clic
            if (event.ctrlKey || event.metaKey || event.button === 1) {
                window.open(url, '_blank');
            } else {
                globalThis.location.href = url;
            }
        }
    });

    div.addEventListener('mousedown', (event) => {
        if (event.button === 1) {
            event.preventDefault();
        }
    });
});
