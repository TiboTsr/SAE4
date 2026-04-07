const listItems = document.querySelectorAll("#main nav ul li");
const content = document.getElementById('content');

const debug = isDebug();

// Check for debug
function isDebug() {

    // Récupère l'URL actuelle
    const url = window.location.href;
    
    // Vérifie si l'URL contient '?debug'
    if (url.includes('?debug')) {
        return true;
    } else {
        return false;
    }

}


listItems.forEach(item => {
    item.addEventListener('click', () => {
        // Supprime la classe 'selected' de tous les éléments
        listItems.forEach(li => li.classList.remove('selected'));

        // Ajoute la classe 'selected' à l'élément cliqué
        item.classList.add('selected');
        const baseQuery = debug ? '?debug' : '';
        const separator = baseQuery === '' ? '?' : '&';
        content.src = './panels/' + item.getAttribute('perm') + '.html' + baseQuery + separator + 'v=' + Date.now();
        
        
    });
});


/* Select first permissions */
listItems[0].click();