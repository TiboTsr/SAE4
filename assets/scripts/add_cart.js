(() => {
    const toaster = document.createElement('div');
    const toastText = document.createElement('p');
    const countElement = document.getElementById('count');

    let toastTimeout = null;
    let burstAddedCount = 0;
    let burstResetTimeout = null;
    let highestCartCount = Number.parseInt(countElement ? countElement.textContent : '0', 10);

    if (Number.isNaN(highestCartCount)) {
        highestCartCount = 0;
    }

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

    function resetBurstCounter() {
        burstAddedCount = 0;
        if (burstResetTimeout) {
            clearTimeout(burstResetTimeout);
            burstResetTimeout = null;
        }
    }

    function showAddedCountToast() {
        burstAddedCount += 1;

        if (burstAddedCount === 1) {
            showToast('1 article ajoute au panier.');
        } else {
            showToast(`${burstAddedCount} articles ajoutes au panier.`);
        }

        if (burstResetTimeout) {
            clearTimeout(burstResetTimeout);
        }

        burstResetTimeout = setTimeout(() => {
            burstAddedCount = 0;
            burstResetTimeout = null;
        }, 1400);
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
                resetBurstCounter();
                showToast((data && data.message) || "Impossible d'ajouter l'article au panier.", true);
                return;
            }

            const count = document.getElementById('count');
            const total = document.getElementById('total');

            if (count && typeof data.count !== 'undefined') {
                const returnedCount = Number.parseInt(data.count, 10);
                if (Number.isNaN(returnedCount)) {
                    count.textContent = data.count;
                } else {
                    highestCartCount = Math.max(highestCartCount, returnedCount);
                    count.textContent = String(highestCartCount);
                }
            }
            if (total && typeof data.total !== 'undefined') {
                total.textContent = data.total;
            }

            showAddedCountToast();
        } catch {
            resetBurstCounter();
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
