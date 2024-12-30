document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-inscription');
    const msisdnInput = document.getElementById('msisdn');
    const villeSelect = document.getElementById('Ville');
    const specialiteSelect = document.getElementById('specialite');
    const msisdnError = document.createElement('div'); // Create error message element
    msisdnError.classList.add('invalid-feedback'); // Add Bootstrap class for styling
    msisdnInput.parentNode.insertBefore(msisdnError, msisdnInput.nextSibling); // Insert after input

    msisdnInput.addEventListener('input', function() {
        const msisdn = msisdnInput.value.trim();
        let errorMessage = "";

        if (msisdn === "") {
            errorMessage = "Le numéro est obligatoire.";
        } else if (isNaN(msisdn)) {
            errorMessage = "Le numéro doit contenir uniquement des chiffres.";
        } else if (!msisdn.startsWith("24205") && !msisdn.startsWith("24204")) {
            errorMessage = "Le numéro doit commencer par 24205 ou 24204.";
        } else if (msisdn.length > 12) {
            errorMessage = "Le numéro ne doit pas dépasser 12 chiffres.";
        }
        else if (msisdn.length < 12 && msisdn.length > 0 && (msisdn.startsWith("24205") || msisdn.startsWith("24204"))){
            errorMessage = "Le numéro doit contenir 12 chiffres.";
        }

        msisdnError.textContent = errorMessage; // Update error message text
        msisdnInput.classList.toggle('is-invalid', errorMessage !== ""); // Toggle Bootstrap invalid class
    });

    form.addEventListener('submit', function(event) {
        let isValid = true;
        const msisdn = msisdnInput.value.trim();

        if (msisdn === "") {
            isValid = false;
        } else if (!msisdn.startsWith("24205") && !msisdn.startsWith("24204")) {
            isValid = false;
        } else if (msisdn.length !== 12) {
            isValid = false;
        } else if (isNaN(msisdn)){
            isValid = false;
        }

        if (villeSelect.value === "") {
            villeSelect.setCustomValidity("Veuillez sélectionner une région.");
            isValid = false;
        } else {
            villeSelect.setCustomValidity("");
        }

        if (specialiteSelect.value === "") {
            specialiteSelect.setCustomValidity("Veuillez sélectionner une spécialité.");
            isValid = false;
        } else {
            specialiteSelect.setCustomValidity("");
        }
        if (!isValid) {
            event.preventDefault();
        }
    });

    villeSelect.addEventListener('change', function() {
        villeSelect.setCustomValidity("");
    });
    specialiteSelect.addEventListener('change', function() {
        specialiteSelect.setCustomValidity("");
    });
});