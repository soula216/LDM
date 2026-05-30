function applyTacheServiceMode(tacheItem, mode) {
    if (!tacheItem) return;
    const isCustom = mode === 'custom';
    const catalogBlock = tacheItem.querySelector('.tache-service-catalog');
    const customBlock = tacheItem.querySelector('.tache-service-custom');
    const serviceSelect = tacheItem.querySelector('select.tache-service-select');
    const customInput = tacheItem.querySelector('.tache-custom-service-input')
        || tacheItem.querySelector('input[name*="[custom_service]"]');
    const groupeSelect = tacheItem.querySelector('.tache-custom-groupe-select');
    const triggerText = tacheItem.querySelector('.service-combobox-trigger-text');

    catalogBlock?.classList.toggle('hidden', isCustom);
    customBlock?.classList.toggle('hidden', !isCustom);

    if (serviceSelect) {
        serviceSelect.disabled = isCustom;
        if (isCustom) {
            serviceSelect.value = '';
            if (triggerText) triggerText.textContent = 'Sélectionner un service';
        }
    }

    if (customInput) {
        customInput.disabled = !isCustom;
        if (!isCustom) {
            customInput.value = '';
        }
    }

    if (groupeSelect) {
        groupeSelect.disabled = !isCustom;
        if (!isCustom) {
            groupeSelect.value = '';
        }
    }
}

function initTacheServiceModes(container) {
    container.querySelectorAll('.tache-item').forEach(function(tacheItem) {
        const checked = tacheItem.querySelector('.tache-service-type-radio:checked');
        applyTacheServiceMode(tacheItem, checked?.value || 'catalog');
    });
}

function initTacheServiceModeDelegation(container) {
    if (!container || container.dataset.serviceModeDelegation === '1') return;
    container.dataset.serviceModeDelegation = '1';
    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('tache-service-type-radio')) {
            applyTacheServiceMode(e.target.closest('.tache-item'), e.target.value);
        }
    });
}
