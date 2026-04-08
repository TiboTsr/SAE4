import { refreshNavbar } from "./navbar.js";
import { requestGET, requestDELETE, requestPOST, requestPUT } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFileBucketUrl, openFileDialog } from "./files.js";

// Show skeleton
showPropertieSkeleton();

// Get inputs
const download_btn = document.getElementById('download_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');
const pdf_preview = document.getElementById('pdf_preview');
const prop_date = document.getElementById('prop_date');
const save_btn = document.getElementById('save_btn');

/**
 * Reloads the navigation bar with reunions items.
 */
async function fetchData() {

    // Fetch data
    let roles = [];
    try{
        roles = await requestGET('/meeting.php');
    } catch (error) {
        toast(error.message, true);
    }

    // Transform data to navbar items
    return roles.map(role => ({label: role.date_reunion.split(' ')[0], id: role.id_reunion}));

}

/**
 * Deletes the reunion from the DB.
*/
async function deleteReunion(id_reunion){

    // Show loader
    showLoader();

    // Send request
    await requestDELETE(`/meeting.php?id=${id_reunion}`);
    
    /// Update navbar
    refreshNavbar(fetchData, selectReunion);

    // Deleted message
    toast('Reunion supprimé avec succès.');

}

/**
 * Loads and displays reunion information based on the provided grade ID.
 *
 * @param {number} id_reunion - The ID of the reunion to be selected.
 */
async function selectReunion(id_reunion, li){

    // Show skeleton
    showPropertieSkeleton();

    // Show loader
    showLoader();
    
    // Fetch grade information
    const role = await requestGET(`/meeting.php?id=${id_reunion}`);

    // Update date field
    if (prop_date) {
        prop_date.value = role.date_reunion.split(' ')[0];
    }

    // Update displayed information
    const url = getFileBucketUrl(role.fichier_reunion);
    if (!url) {
        pdf_preview.src = '';
        toast("Impossible de trouver l'URL du fichier.", true);
    } else {
        pdf_preview.src = url;
    }

    // Update download button
    download_btn.onclick = ()=>{
        if (!url) {
            toast("Impossible de trouver l'URL du fichier.", true);
            return;
        }
        window.open(url, '_blank');
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
                deleteReunion(id_reunion);
            }
          });
    };

    // Save date button
    save_btn.onclick = async ()=> {
        if (!prop_date.value) {
            toast("Veuillez sélectionner une date.", true);
            return;
        }

        showLoader();
        try {
            await requestPUT(`/meeting.php?id=${id_reunion}`, { date: prop_date.value });
            if (li) {
                li.textContent = prop_date.value;
            }
            toast('Date de réunion mise à jour avec succès.');
        } catch (error) {
            toast(error.message, true);
        }
        hideLoader();
    };

    // Hide loader
    hideLoader();

    // Hide skeleton
    hidePropertieSkeleton();
    
}

// Handle new reunion
new_btn.onclick = async ()=>{

    let file = null;
    try {
        file = await openFileDialog(".pdf");
    } catch (error) {
        return;
    }

    // Show loader
    showLoader();

    // Create form data
    const form_data = new FormData();
    form_data.append('file', file);
    form_data.append('date', new Date().toISOString().split('T')[0]);

    // Send request
    try{
        const { id_reunion } =  await requestPOST('/meeting.php', form_data);
        refreshNavbar(fetchData, selectReunion, id_reunion);
        toast("Fichier uploadé avec succès");
    } catch (error) {
        toast(error.message, true);
        hideLoader();
    }

}


// Load navbar
refreshNavbar(fetchData, selectReunion);
