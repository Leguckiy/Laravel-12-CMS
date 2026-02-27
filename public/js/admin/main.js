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

    function initializeMultilangFields() {
        // Find all language selectors
        const allSelectors = document.querySelectorAll('.multilang-selector');
        
        if (!allSelectors.length) {
            return;
        }

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
        
        const initialLang = allSelectors[0].value;
        if (initialLang) {
            updateAllGroups(initialLang);
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

    function initializeImageUploadFields() {
        const fields = document.querySelectorAll('[data-image-field]');
        if (!fields.length) {
            return;
        }

        fields.forEach(function(field) {
            const removeButton = field.querySelector('[data-image-remove-trigger]');
            if (!removeButton) {
                return;
            }

            removeButton.addEventListener('click', function(event) {
                event.preventDefault();

                const hiddenInput = field.querySelector('[data-image-remove-input]');
                if (hiddenInput) {
                    hiddenInput.value = '1';
                }

                const preview = field.querySelector('[data-image-preview]');
                if (preview) {
                    const emptyText = preview.getAttribute('data-image-empty-text') || '';
                    preview.innerHTML = '<span class="text-muted small">' + emptyText + '</span>';
                }

                const fileInput = field.querySelector('input[type="file"]');
                if (fileInput) {
                    fileInput.value = '';
                }

                removeButton.remove();
            });
        });
    }

    function initializeAddressFormPostcode() {
        const countrySelect = document.getElementById('input-country_id');
        const postcodeRow = document.querySelector('.js-admin-address-postcode-row');
        if (!countrySelect || !postcodeRow) {
            return;
        }
        function update() {
            const option = countrySelect.options[countrySelect.selectedIndex];
            const required = option && option.getAttribute('data-postcode-required') === '1';
            if (required) {
                postcodeRow.classList.add('required');
            } else {
                postcodeRow.classList.remove('required');
            }
        }
        countrySelect.addEventListener('change', update);
        update();
    }

    function initializeRichTextEditors() {
        if (!window.tinymce) {
            return;
        }

        const editors = Array.prototype.slice.call(document.querySelectorAll('textarea[data-autoload-rte="true"]'))
            .filter(function(textarea) {
                return textarea.dataset.rteInitialized !== 'true';
            });

        if (!editors.length) {
            return;
        }

        editors.forEach(function(textarea) {
            textarea.dataset.rteInitialized = 'true';

            var heightAttr = textarea.dataset.rteHeight;
            var requestedHeight = heightAttr ? parseInt(heightAttr, 10) : 260;
            var editorHeight = Number.isFinite(requestedHeight) ? requestedHeight : 260;

            var baseUrl = window.TinyMceBaseUrl || '/js/library/tinymce';

            window.tinymce.init({
                target: textarea,
                base_url: baseUrl,
                suffix: '.min',
                license_key: 'gpl',
                menubar: false,
                branding: false,
                convert_urls: false,
                height: editorHeight,
                plugins: 'link lists code table',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | removeformat | code',
                language: document.documentElement.getAttribute('lang') || 'en',
                setup: function(editor) {
                    editor.on('change keyup', function() {
                        editor.save();
                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                }
            });
        });
    }

    function initializeAll() {
        initializeAdminActions();
        initializeMultilangFields();
        initializeImageUploadFields();
        initializeAddressFormPostcode();
        initializeRichTextEditors();
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
