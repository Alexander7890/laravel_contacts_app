(function () {
    const clearButton = document.querySelector('[data-clear-filters]');
    if (!clearButton) {
        return;
    }

    clearButton.addEventListener('click', () => {
        const searchInput = document.querySelector('input[name="q"]');
        const groupSelect = document.querySelector('select[name="group"]');
        if (searchInput) searchInput.value = '';
        if (groupSelect) groupSelect.selectedIndex = 0;
        const form = clearButton.closest('form');
        if (form) form.submit();
    });
})();
