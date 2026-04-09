// Import ajax
import { requestGET } from './ajax.js';

// Recuperer la textarea par son ID
const textarea = document.getElementById('content');

const response = await requestGET('/logs.php');
textarea.value = response.logs ?? '';

const scrollToBottom = () => {
    textarea.scrollTop = textarea.scrollHeight;
};

// Force le scroll apres insertion puis apres le rendu effectif du panel/iframe.
scrollToBottom();
requestAnimationFrame(scrollToBottom);
setTimeout(scrollToBottom, 60);
