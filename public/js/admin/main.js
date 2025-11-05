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
        document.querySelectorAll('.multilang-selector').forEach(function(selector) {
            const fieldName = selector.dataset.fieldName;
            const groups = document.querySelectorAll('.multilang-group[data-field-name="' + fieldName + '"], .multilang-text-group[data-field-name="' + fieldName + '"]');
            
            if (groups.length > 0) {
                selector.addEventListener('change', function() {
                    const selectedLangId = this.value;
                    
                    groups.forEach(function(group) {
                        const groupLangId = group.dataset.langId;
                        if (groupLangId === selectedLangId) {
                            group.classList.remove('d-none');
                        } else {
                            group.classList.add('d-none');
                        }
                    });
                });
            }
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
