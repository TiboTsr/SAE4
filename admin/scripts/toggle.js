document.addEventListener("DOMContentLoaded", ()=>{
    let toggleElements = document.querySelectorAll('.toggle');
    toggleElements.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            toggle.classList.toggle('toggle-active');
        });
    });
});


export function updateToggleStatus(toggle, status){
    if(status) toggle.classList.add('toggle-active');
    else toggle.classList.remove('toggle-active');
}
export function getToggleStatus(toggle){
    return toggle.classList.contains('toggle-active');
}