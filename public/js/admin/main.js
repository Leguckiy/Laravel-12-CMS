(function () {
    'use strict';

    function findDeleteForm() {
        return document.getElementById('delete-form');
    }

    function submitDelete(url) {
        const form = findDeleteForm();
        if (!form) {
            console.error('Missing #delete-form element in the page');
            return;
        }
        form.action = url;
        form.submit();
    }

    function onClick(event) {
        const trigger = event.target.closest('[data-delete-url]');
        if (!trigger) return;

        event.preventDefault();

        const url = trigger.getAttribute('data-delete-url');
        if (!url) return;

        const message = trigger.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
        if (!window.confirm(message)) return;

        submitDelete(url);
    }

    function initializeAdminActions() {
        document.addEventListener('click', onClick, false);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeAdminActions);
    } else {
        initializeAdminActions();
    }

    function initializeMultilangFields() {
        // Find all language selectors
        const allSelectors = document.querySelectorAll('.multilang-selector');
        
        // Find all multilingual field groups
        const allGroups = document.querySelectorAll('.multilang-group, .multilang-text-group');
        
        // Update group visibility based on selected language
        function updateAllGroups(selectedLangId) {
            allGroups.forEach(function(group) {
                const groupLangId = group.dataset.langId;
                if (groupLangId === selectedLangId) {
                    group.classList.remove('d-none');
                } else {
                    group.classList.add('d-none');
                }
            });
        }
        
        // Attach change handler to every selector
        allSelectors.forEach(function(selector) {
            selector.addEventListener('change', function() {
                const selectedLangId = this.value;
                
                // Sync all selectors to the newly chosen language
                allSelectors.forEach(function(otherSelector) {
                    if (otherSelector !== selector) {
                        otherSelector.value = selectedLangId;
                    }
                });
                
                // Toggle multilingual groups visibility
                updateAllGroups(selectedLangId);
            });
        });
    }

    function initializeAll() {
        initializeAdminActions();
        initializeMultilangFields();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeAll);
    } else {
        initializeAll();
    }

    window.AdminUI = Object.freeze({
        deleteViaForm: function (url) {
            submitDelete(url);
        }
    });
})();
