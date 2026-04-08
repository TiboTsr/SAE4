// Imports
import { requestGET, requestPATCH, requestPOST } from './ajax.js';
import { getToggleStatus } from './toggle.js';

// DOM Elements
const table = document.getElementById('table');
const tbody = table.getElementsByTagName('tbody')[0];
const toggle_boutique = document.getElementById('toggle_boutique');
const toggle_grades =  document.getElementById('toggle_grades');
const toggle_events = document.getElementById('toggle_events');
const userSearch = document.getElementById('userSearch');
const createOrderSection = document.getElementById('createOrderSection');
const toggleCreateOrder = document.getElementById('toggleCreateOrder');
const createOrderForm = document.getElementById('createOrderForm');
const orderArticle = document.getElementById('orderArticle');
const orderQuantity = document.getElementById('orderQuantity');
const orderPayment = document.getElementById('orderPayment');
const orderUser = document.getElementById('orderUser');
const orderRecovered = document.getElementById('orderRecovered');
const createOrderButton = document.getElementById('createOrderButton');
const createOrderMessage = document.getElementById('createOrderMessage');

// Values
let default_data = [];
let historyData = [];
let purchaseMeta = { articles: [], users: [] };

try {
    [historyData, purchaseMeta] = await Promise.all([
        requestGET('/purchase.php'),
        requestGET('/purchase.php?meta=true')
    ]);
} catch (error) {
    historyData = await requestGET('/purchase.php');
    showCreateOrderMessage("Impossible de charger la liste d'articles/utilisateurs.", true);
}

setHistoryData(historyData);
populateCreateOrderForm(purchaseMeta);

// Load data
function loadData(){

    // Remove all lines of the table
    while (tbody.firstChild)
        tbody.removeChild(tbody.firstChild);

    // Filter data
    let data = default_data;
    if (!getToggleStatus(toggle_boutique)) {
        data = data.filter(item => item.type_transaction !== 'Commande');
    }
    if (!getToggleStatus(toggle_grades)) {
        data = data.filter(item => item.type_transaction !== 'Adhesion');
    }
    if (!getToggleStatus(toggle_events)) {
        data = data.filter(item => item.type_transaction !== 'Inscription');
    }
    if (userSearch.value !== '') {
        data = data.filter(item => item.user.toUpperCase().includes(userSearch.value.toUpperCase()));
    }

    // Add data to the table
    data.forEach(item => {
        const row = document.createElement('tr');

        const typeCell = document.createElement('td');
        typeCell.textContent = item.type_transaction;
        row.appendChild(typeCell);

        const elementCell = document.createElement('td');
        elementCell.textContent = item.element;
        row.appendChild(elementCell);

        const utilisateurCell = document.createElement('td');
        utilisateurCell.textContent = item.user;
        row.appendChild(utilisateurCell);

        const dateCell = document.createElement('td');
        dateCell.textContent = item.date_transaction.split(' ')[0];
        row.appendChild(dateCell);

        const quantiteCell = document.createElement('td');
        quantiteCell.textContent = item.quantite;
        row.appendChild(quantiteCell);

        const prixCell = document.createElement('td');
        prixCell.textContent = parseFloat(item.montant).toFixed(2) + ' €';
        row.appendChild(prixCell);

        const paiementCell = document.createElement('td');
        paiementCell.textContent = item.mode_paiement;
        row.appendChild(paiementCell);

        const statutCell = document.createElement('td');
        if (item.type_transaction === 'Commande' && item.id_commande !== null) {
            const statusBtn = document.createElement('button');
            statusBtn.classList.add('status-toggle-btn');
            statusBtn.classList.add(item.recupere === 1 ? 'is-done' : 'is-pending');
            statusBtn.textContent = item.recupere === 1 ? 'Récupérée' : 'En attente';
            statusBtn.addEventListener('click', async () => {
                const newStatus = item.recupere === 1 ? 0 : 1;
                statusBtn.disabled = true;
                try {
                    await requestPATCH(`/purchase.php?id=${item.id_commande}`, { recupere: newStatus === 1 });
                    item.recupere = newStatus;
                    loadData();
                } catch (error) {
                    statusBtn.disabled = false;
                    alert("Impossible de modifier le statut de la commande.");
                }
            });
            statutCell.appendChild(statusBtn);
        } else {
            statutCell.textContent = '-';
        }
        row.appendChild(statutCell);

        tbody.appendChild(row);
    });

}

function setHistoryData(data) {
    default_data = data;
    default_data.forEach(item => {
        const nom = (item.nom_membre ?? '').trim();
        const prenom = (item.prenom_membre ?? '').trim();
        item.user = `${nom} ${prenom}`.trim();
        if (item.user === '') {
            item.user = 'Client non inscrit';
        }
        item.recupere = Number(item.recupere);
    });
}

function populateCreateOrderForm(meta) {
    const articles = Array.isArray(meta.articles) ? meta.articles : [];
    const users = Array.isArray(meta.users) ? meta.users : [];

    orderArticle.innerHTML = '<option value="">Choisir un article</option>';
    orderUser.innerHTML = '<option value="">Client non inscrit</option>';

    articles.forEach(article => {
        const option = document.createElement('option');
        option.value = String(article.id_article);
        option.textContent = `${article.nom_article} (${parseFloat(article.prix_article).toFixed(2)} €)`;
        orderArticle.appendChild(option);
    });

    users.forEach(user => {
        const option = document.createElement('option');
        option.value = String(user.id_membre);
        option.textContent = `${user.nom_membre} ${user.prenom_membre}`.trim();
        orderUser.appendChild(option);
    });

    if (articles.length === 0) {
        showCreateOrderMessage("Aucun article disponible pour la création de commande.", true);
    }
}

function showCreateOrderMessage(message, isError = false) {
    createOrderMessage.textContent = message;
    createOrderMessage.classList.toggle('is-error', isError);
    createOrderMessage.classList.toggle('is-success', !isError);
}

function setCreateOrderFormVisibility(isOpen) {
    if (!createOrderForm || !toggleCreateOrder || !createOrderSection) {
        return;
    }

    createOrderForm.hidden = !isOpen;
    createOrderSection.classList.toggle('is-open', isOpen);
    toggleCreateOrder.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    toggleCreateOrder.textContent = isOpen ? 'Cacher' : 'Dérouler';
}

async function refreshHistory() {
    const data = await requestGET('/purchase.php');
    setHistoryData(data);
    loadData();
}

// Call loadData
loadData();

// Set update events
toggle_boutique.addEventListener('click', loadData);
toggle_grades.addEventListener('click', loadData);
toggle_events.addEventListener('click', loadData);
userSearch.addEventListener('input', loadData);

if (createOrderForm) {
    setCreateOrderFormVisibility(false);

    if (toggleCreateOrder) {
        toggleCreateOrder.addEventListener('click', () => {
            setCreateOrderFormVisibility(createOrderForm.hidden);
        });
    }

    createOrderForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        createOrderButton.disabled = true;
        showCreateOrderMessage('Création en cours...');

        try {
            const payload = {
                id_article: Number(orderArticle.value),
                quantite: Number(orderQuantity.value),
                mode_paiement: orderPayment.value,
                id_membre: orderUser.value === '' ? null : Number(orderUser.value),
                recupere: orderRecovered.checked
            };

            await requestPOST('/purchase.php', payload);
            showCreateOrderMessage('Commande créée avec succès.');
            createOrderForm.reset();
            orderQuantity.value = '1';
            await refreshHistory();
        } catch (error) {
            showCreateOrderMessage(error.message || 'Impossible de créer la commande.', true);
        } finally {
            createOrderButton.disabled = false;
        }
    });
}
