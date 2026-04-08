(() => {
    const toaster = document.createElement('div');
    const toastText = document.createElement('p');
    let toastTimeout = null;

    toaster.className = 'toast-container';
    toaster.setAttribute('aria-live', 'polite');
    toaster.setAttribute('aria-atomic', 'true');
    toastText.className = 'toast';
    toaster.appendChild(toastText);

    const mountToaster = () => {
        if (!document.body.contains(toaster)) {
            document.body.appendChild(toaster);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountToaster);
    } else {
        mountToaster();
    }

    function showToast(message, isError = false) {
        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }

        toaster.classList.toggle('error', Boolean(isError));
        toastText.textContent = message || 'Action effectuee.';
        toaster.style.display = 'flex';

        requestAnimationFrame(() => {
            toaster.classList.add('showed');
        });

        toastTimeout = setTimeout(hideToast, 3000);
    }

    function hideToast() {
        toaster.classList.remove('showed');
        setTimeout(() => {
            if (!toaster.classList.contains('showed')) {
                toaster.style.display = 'none';
            }
        }, 220);
    }

    async function addToCart(link) {
        try {
            const response = await fetch(link, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            let data = null;
            try {
                data = await response.json();
            } catch {
                showToast("Erreur de reponse du serveur.", true);
                return;
            }

            if (!response.ok || !data || data.error) {
                showToast((data && data.message) || "Impossible d'ajouter l'article au panier.", true);
                return;
            }

            const count = document.getElementById('count');
            const total = document.getElementById('total');

            if (count && typeof data.count !== 'undefined') {
                count.textContent = data.count;
            }
            if (total && typeof data.total !== 'undefined') {
                total.textContent = data.total;
            }

            showToast(data.message || 'Article ajoute au panier.');
        } catch {
            showToast("Impossible d'ajouter l'article au panier.", true);
        }
    }

    document.addEventListener('click', (event) => {
        const addButton = event.target.closest('a.addCart');
        if (!addButton) {
            return;
        }

        event.preventDefault();
        addToCart(addButton.getAttribute('href'));
    });
})();
