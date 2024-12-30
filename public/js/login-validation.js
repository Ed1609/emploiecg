document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginform');
    const msisdnInput = document.getElementById('msisdn');
    const msisdnError = document.createElement('div');
    msisdnError.classList.add('invalid-feedback');
    msisdnInput.parentNode.insertBefore(msisdnError, msisdnInput.nextSibling);

    msisdnInput.addEventListener('input', function() {
        const msisdn = msisdnInput.value.trim();
        let errorMessage = "";

        if (msisdn === "") {
            errorMessage = "Le numéro est obligatoire pour la connexion.";
        } else if (isNaN(msisdn)) {
            errorMessage = "Le numéro doit contenir uniquement des chiffres.";
        } else if (!msisdn.startsWith("24205") && !msisdn.startsWith("24204")) {
            errorMessage = "Le numéro doit commencer par 24205 ou 24204.";
        } else if (msisdn.length > 12) {
            errorMessage = "Le numéro ne doit pas dépasser 12 chiffres.";
        } else if (msisdn.length < 12 && msisdn.length > 0 && (msisdn.startsWith("24205") || msisdn.startsWith("24204"))) {
            errorMessage = "Le numéro doit contenir 12 chiffres.";
        }

        msisdnError.textContent = errorMessage;
        msisdnInput.classList.toggle('is-invalid', errorMessage !== "");
    });

    loginForm.addEventListener('submit', function(event) {
        const msisdn = msisdnInput.value.trim();
        let isValid = true;

        if (msisdn === "") {
            isValid = false;
        } else if (isNaN(msisdn)) {
            isValid = false;
        } else if (!msisdn.startsWith("24205") && !msisdn.startsWith("24204")) {
            isValid = false;
        } else if (msisdn.length !== 12) {
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
        }
    });
});