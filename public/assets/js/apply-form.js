'use strict';

function validateName(input) {
    const value = input.value.trim();

    if (value === '') {
        return 'Vul je naam in.';
    }

    if (value.length > input.maxLength) {
        return `Je naam mag maximaal ${input.maxLength} tekens bevatten.`;
    }

    return '';
}

function validateEmail(input) {
    if (input.value.trim() === '') {
        return 'Vul je e-mailadres in.';
    }

    if (input.validity.typeMismatch) {
        return 'Vul een geldig e-mailadres in.';
    }

    if (input.value.length > input.maxLength) {
        return `Je e-mailadres mag maximaal ${input.maxLength} tekens bevatten.`;
    }

    return '';
}

function createCvValidator(form) {
    const maxBytes = Number(form.dataset.maxCvBytes);
    const maxMegabytes = form.dataset.maxCvMegabytes;
    const extension = form.dataset.cvExtension;

    return function validateCv(input) {
        const file = input.files[0];

        if (file === undefined) {
            return 'Kies een CV om te uploaden.';
        }

        if (file.name.toLowerCase().endsWith(`.${extension}`) === false) {
            return `Je CV moet een ${extension.toUpperCase()}-bestand zijn.`;
        }

        if (file.size === 0) {
            return 'Het gekozen bestand is leeg.';
        }

        if (file.size > maxBytes) {
            return `Je CV mag maximaal ${maxMegabytes} MB groot zijn.`;
        }

        return '';
    };
}

function validateMotivation(textarea) {
    if (textarea.value.length > textarea.maxLength) {
        return `Je motivatie mag maximaal ${textarea.maxLength} tekens bevatten.`;
    }

    return '';
}

function showFieldError(field, message) {
    document.getElementById(`${field.id}-error`).textContent = message;

    if (message === '') {
        field.removeAttribute('aria-invalid');
    } else {
        field.setAttribute('aria-invalid', 'true');
    }
}

function validateFields(validators) {
    let firstInvalidField = null;

    validators.forEach(([field, validate]) => {
        const message = validate(field);
        showFieldError(field, message);

        if (message !== '' && firstInvalidField === null) {
            firstInvalidField = field;
        }
    });

    if (firstInvalidField !== null) {
        firstInvalidField.focus();
    }

    return firstInvalidField === null;
}

function initApplyForm() {
    const form = document.getElementById('apply-form');
    const openButton = document.getElementById('apply-button');
    const cancelButton = document.getElementById('apply-cancel');

    if (form === null || openButton === null || cancelButton === null) {
        return;
    }

    const nameField = document.getElementById('apply-name');
    const validators = [
        [nameField, validateName],
        [document.getElementById('apply-email'), validateEmail],
        [document.getElementById('apply-cv'), createCvValidator(form)],
        [document.getElementById('apply-motivation'), validateMotivation],
    ];

    function setFormVisible(visible) {
        form.hidden = visible === false;
        openButton.setAttribute('aria-expanded', String(visible));
    }

    openButton.addEventListener('click', () => {
        setFormVisible(form.hidden);

        if (form.hidden === false) {
            nameField.focus();
        }
    });

    cancelButton.addEventListener('click', () => {
        setFormVisible(false);
        validators.forEach(([field]) => showFieldError(field, ''));
        openButton.focus();
    });

    validators.forEach(([field]) => {
        field.addEventListener('input', () => showFieldError(field, ''));
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        validateFields(validators);
    });
}

initApplyForm();
