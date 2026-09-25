document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const message = form.dataset.confirm || 'Deseja continuar?';

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });
});
