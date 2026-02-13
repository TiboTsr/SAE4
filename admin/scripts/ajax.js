/**
 * The base URL for the server API.
 * @constant {string}
 */
const SERVER_API_URL = '/BUT 2/S4/SAE4/api/';

/**
 * If true, the fetch requests are logged in the console.
 * @constant {boolean}
*/
const DEBUG_FETCHS = isDebug();

/**
 * Effectue une requête AJAX avec la méthode spécifiée.
 * @param {string} endpoint - L'endpoint de la requête.
 * @param {string} method - La méthode HTTP (GET, POST, PUT, PATCH, DELETE).
 * @param {Object} data - Les données à envoyer (pour POST, PUT).
 * @param {Object} headers - Les en-têtes HTTP supplémentaires (facultatif).
 * @returns {Promise} - Résout avec les données de la réponse ou rejette avec une erreur.
 */
async function request(endpoint, method = 'GET', data = null, headers = {}) {

    // Create url
    if (!endpoint.startsWith('/'))
        endpoint = endpoint + '/';
    const url = SERVER_API_URL + endpoint;

    // Fetch
    try {
        // Configuration de l'option de la requête
        const options = {
            method,
            headers: {
                ...headers
            }
        };

        // Handle patch (specific case for files)
        if (data instanceof File || data instanceof Blob) {
            options.headers['Content-Type'] = data.type;
            options.body = data;
        } else if (data instanceof FormData) {
            options.body = data;
        } else if (data) {
            options.headers['Content-Type'] = 'application/json; charset=utf-8';
            options.body = JSON.stringify(data);
        }

        // Fetch data
        const response = await fetch(url, options);

        // Récupérer et retourner le résultat en JSON
        const text = await response.text();
        if (DEBUG_FETCHS) {
            console.log(`%c(${response.status}) %c${method} %c${endpoint}%c${text.startsWith('\n') ? '' : '\n'}${text}`, 'color: red', 'color: peachpuff; font-weight: bold;', 'color: peachpuff;', 'color: powderblue;');
        }
        let json = null;
        try {
            json = JSON.parse(text);
        } catch (error) {
            throw new Error("The API returned a error : " + error.message);
        }

        // Vérification de la réponse
        if (!response.ok)
            if (json && json.error)
                if (json.error == 'Unauthorized' || json.error == 'Forbidden')
                    window.location.href = 'unauthorized.html';
                else
                    throw new Error(json.error);
            else if (json && json.message)
                throw new Error(json.message);
            else
                throw new Error(`Erreur: ${response.status} ${response.statusText}`);

        return json;

    } catch (error) {
        throw error;
    }
}

/**
 * Effectue une requête GET.
 * @param {string} endpoint - L'endpoint de la requête.
 * @returns {Promise}
 */
function requestGET(endpoint) {
    return request(endpoint, 'GET');
}

/**
 * Effectue une requête POST.
 * @param {string} endpoint - L'endpoint de la requête.
 * @param {Object} data - Les données à envoyer.
 * @returns {Promise}
 */
function requestPOST(endpoint, data) {
    return request(endpoint, 'POST', data);
}


/**
 * Effectue une requête PUT.
 * @param {string} endpoint - L'endpoint de la requête.
 * @param {Object} data - Les données à envoyer.
 * @returns {Promise}
 */
function requestPUT(endpoint, data) {
    return request(endpoint, 'PUT', data);
}

/**
 * Effectue une requête PATCH.
 * @param {string} endpoint - L'endpoint de la requête.
 * @param {FormData} data - Les données à envoyer.
 * @returns {Promise}
 */
function requestPATCH(endpoint, data) {
    return request(endpoint, 'PATCH', data);
}

/**
 * Effectue une requête DELETE.
 * @param {string} endpoint - L'endpoint de la requête.
 * @returns {Promise}
 */
function requestDELETE(endpoint) {
    return request(endpoint, 'DELETE');
}

// Export des fonctions
export { request, requestGET, requestPOST, requestPUT, requestPATCH, requestDELETE };

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