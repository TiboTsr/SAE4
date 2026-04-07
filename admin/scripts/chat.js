import { requestGET, requestPOST } from './ajax.js';
import { toast } from './toaster.js';

const messagesContainer = document.getElementById('chat_messages');
const form = document.getElementById('chat_form');
const input = document.getElementById('chat_input');
const sendButton = document.getElementById('chat_send');
const refreshButton = document.getElementById('chat_refresh');

let lastLoadFailed = false;

function formatDate(isoValue) {
    const date = new Date(isoValue.replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return date.toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function scrollToBottom() {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function renderMessages(messages) {
    messagesContainer.innerHTML = '';

    if (!Array.isArray(messages) || messages.length === 0) {
        const empty = document.createElement('p');
        empty.className = 'chat-empty';
        empty.textContent = 'Aucun message pour le moment.';
        messagesContainer.appendChild(empty);
        return;
    }

    messages.forEach(item => {
        const card = document.createElement('article');
        card.className = 'chat-message';

        const header = document.createElement('div');
        header.className = 'chat-message-header';

        const author = document.createElement('strong');
        author.className = 'chat-author';
        author.textContent = `${item.prenom_membre} ${item.nom_membre}`;

        const date = document.createElement('span');
        date.className = 'chat-date';
        date.textContent = formatDate(item.date_message);

        header.appendChild(author);
        header.appendChild(date);

        const content = document.createElement('p');
        content.className = 'chat-content';
        content.textContent = item.contenu_message;

        card.appendChild(header);
        card.appendChild(content);
        messagesContainer.appendChild(card);
    });
}

async function loadMessages(showToastOnError = false) {
    try {
        const messages = await requestGET('/chat.php?limit=120');
        renderMessages(messages);
        scrollToBottom();
        lastLoadFailed = false;
    } catch (error) {
        if (showToastOnError || !lastLoadFailed) {
            toast(error.message || 'Impossible de charger le chat.', true);
        }
        lastLoadFailed = true;
    }
}

async function sendMessage() {
    const message = input.value.trim();
    if (message.length === 0) {
        toast('Veuillez saisir un message.', true);
        return;
    }

    sendButton.disabled = true;

    try {
        await requestPOST('/chat.php', { message });
        input.value = '';
        await loadMessages();
    } catch (error) {
        toast(error.message || 'Impossible d envoyer le message.', true);
    } finally {
        sendButton.disabled = false;
    }
}

form.addEventListener('submit', async event => {
    event.preventDefault();
    await sendMessage();
});

refreshButton.addEventListener('click', () => loadMessages(true));

loadMessages();
setInterval(() => {
    loadMessages();
}, 10000);
