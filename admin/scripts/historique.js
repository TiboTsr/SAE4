// Imports
import { requestGET, requestPATCH } from './ajax.js';
import { getToggleStatus } from './toggle.js';

// DOM Elements
const table = document.getElementById('table');
const tbody = table.getElementsByTagName('tbody')[0];
const toggle_boutique = document.getElementById('toggle_boutique');
const toggle_grades =  document.getElementById('toggle_grades');
const toggle_events = document.getElementById('toggle_events');
const userSearch = document.getElementById('userSearch');

// Values
const default_data = await requestGET('/purchase.php');
default_data.forEach(item => {
    item.user = item.nom_membre.toUpperCase() + ' ' + item.prenom_membre;
    item.recupere = Number(item.recupere);
});

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

// Call loadData
loadData();

// Set update events
toggle_boutique.addEventListener('click', loadData);
toggle_grades.addEventListener('click', loadData);
toggle_events.addEventListener('click', loadData);
userSearch.addEventListener('input', loadData);
