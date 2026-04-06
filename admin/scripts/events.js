import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPATCH, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFullFilepath, openFileDialog } from "./files.js";
import { getToggleStatus, updateToggleStatus } from "./toggle.js";

// Show skeleton
showPropertieSkeleton();

// Get inputs
const prop_image = document.getElementById('prop_image');
const prop_name = document.getElementById('prop_name');
const prop_desc = document.getElementById('prop_desc');
const prop_lieu = document.getElementById('prop_lieu');
const prop_xp = document.getElementById('prop_xp');
const prop_date = document.getElementById('prop_date');
const prop_places = document.getElementById('prop_places');
const prop_price = document.getElementById('prop_price');
const prop_reductions = document.getElementById('prop_reductions');
const save_btn = document.getElementById('save_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');

/**
 * Reloads the navigation bar with event items.
 */
async function fetchData() {

    // Fetch data
    let events = [];
    try{
        events = await requestGET('/event.php');
    } catch (error) {
        toast('Erreur lors du chargement des evenements.', true);
    }

    // Transform data to navbar items
    return events.map(event => ({label: event.nom_evenement, id: event.id_evenement}));

}

/**
 * Saves the event information.
 *
 * @param {number} id_event - The ID of the event to be saved.
 * @returns {Promise<void>} A promise that resolves when the event is successfully saved.
 */
async function saveEvent(id_event){

    // Show loader
    showLoader();

    // Create data
    const data = {
        nom: prop_name.value,
        description: prop_desc.value,
        xp: prop_xp.value,
        places: prop_places.value,
        prix: prop_price.value,
        lieu: prop_lieu.value,
        date: prop_date.value,
        reductions: getToggleStatus(prop_reductions)
    };

    // Send data
    try {
        await requestPUT('/event.php?id=' + id_event.toString(), data);
        toast('Evenement mis à jour avec succès.');
        selectEvent(id_event);
    } catch (error) {
        toast(error.message, true);
    }

    // Stop loader
    hideLoader();

}

/**
 * Deletes the event from the DB.
*/
async function deleteEvent(id_event){

    // Show loader
    showLoader();

    // Send request
    await requestDELETE(`/event.php?id=${id_event}`);
    
    /// Update navbar
    refreshNavbar(fetchData, selectEvent);

    // Deleted message
    toast('Evenement supprimé avec succès.');

}

/**
 * Loads and displays event information based on the provided event ID.
 *
 * @param {number} id_event - The ID of the event to be selected.
 * @returns {Promise<void>} A promise that resolves when the event information has been fetched and displayed.
 */
async function selectEvent(id_event, li){

    // Show skeleton
    showPropertieSkeleton();

    // Show loader
    showLoader();

    // Fetch event information
    const event = await requestGET(`/event.php?id=${id_event}`);

    // Update displayed information
    prop_image.src = await getFullFilepath(event.image_evenement, '../ressources/default_images/event.jpg');
    prop_name.value = event.nom_evenement;
    prop_desc.value = event.description_evenement ?? '';
    prop_xp.value = event.xp_evenement;
    prop_places.value = event.places_evenement;
    prop_price.value = event.prix_evenement;
    updateToggleStatus(prop_reductions, event.reductions_evenement);
    prop_lieu.value = event.lieu_evenement;
    prop_date.value = event.date_evenement.split(' ')[0]; // Because barnabé want to return a date and an hour on the api

    // Update save button
    save_btn.onclick = ()=>{
        saveEvent(id_event);
    };

    // Delete button
    delete_btn.onclick = ()=>{
        swal({
            title: "Êtes vous sûr ?",
            text: "Cette action est définitive",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
                deleteEvent(id_event);
            }
          });
    };

    // Update name
    prop_name.onkeyup = ()=>{
        li.textContent = prop_name.value;
    };

    // Update image
    document.getElementById('prop_image_edit').onclick = async ()=>{
        toast("Les images d'evenements ne sont pas prises en charge par le schema actuel.", true);
        return;

        // Get file form
        const image = await openFileDialog();

        // Update image src
        const url = URL.createObjectURL(image);
        prop_image.src = url;

        // Show loader
        showLoader();

        // Send data
        try {
            await requestPATCH('/event.php?id=' + id_event.toString(), image);
            toast('Image mis à jour avec succès.');
        } catch (error) {
            toast(error.message, true);
        }

        // Stop loader
        hideLoader();

    };

    // Hide loader
    hideLoader();

    // Hide skeleton
    hidePropertieSkeleton();
    
}

// Handle new event
new_btn.onclick = async ()=>{

    // Show loader
    showLoader();

    // Create new event
    try {
        const { id_evenement } = await requestPOST('/event.php');
        refreshNavbar(fetchData, selectEvent, id_evenement);
    } catch (error) {
        toast("Erreur lors de la création de l'évenement", true);
        hideLoader();
    }

};

// Load navbar
refreshNavbar(fetchData, selectEvent);
