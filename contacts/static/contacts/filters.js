(function () {
    const clearButton = document.getElementById('clear-filters');
    if (!clearButton) {
        return;
    }

    clearButton.addEventListener('click', (event) => {
        event.preventDefault();
        const form = clearButton.closest('form');
        if (form) {
            const url = clearButton.getAttribute('href');
            window.location.href = url || '#';
        }
    });
})();
