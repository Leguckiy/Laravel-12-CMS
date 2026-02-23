(function () {
    'use strict';

    var token = $('meta[name="csrf-token"]').attr('content') || '';
    var showResponseMessage = window.showResponseMessage;
    var $messages = $('#checkout-js-messages');

    /**
     * Get translated message for checkout JS. Keys are kebab-case (e.g. payment-after-shipping).
     * Translations come from #checkout-js-messages data-* attributes (see checkout index blade).
     * Lang keys in front/checkout.php use snake_case (e.g. payment_after_shipping).
     */
    function getCheckoutMessage(key) {
        var value = ($messages.length && $messages.attr('data-' + key)) || '';
        return value;
    }

    function getCheckoutMessageOrFallback(key) {
        return getCheckoutMessage(key) || getCheckoutMessage('error-generic') || 'Error.';
    }

    function getErrorMessageFromXhr(xhr) {
        var data = xhr.responseJSON;
        return (data && data.message) ? data.message : getCheckoutMessageOrFallback('error-generic');
    }

    function getCartRedirectConfig() {
        if (!$messages.length) {
            return { url: '', hint: '' };
        }
        return {
            url: $messages.attr('data-cart-url') || '',
            hint: $messages.attr('data-redirect-to-cart-hint') || ''
        };
    }

    var CART_REDIRECT_DELAY_MS = 2500;

    /**
     * Handles checkout AJAX errors. If response has redirect_to_cart, shows alert with message + hint and redirects to cart after delay.
     * Otherwise shows generic error alert. Optional onBeforeShow callback (e.g. re-enable button) is always called so UI is not stuck.
     */
    function handleCheckoutCartError(xhr, onBeforeShow) {
        if (typeof onBeforeShow === 'function') {
            onBeforeShow();
        }
        var data = xhr.responseJSON;
        if (data && data.redirect_to_cart) {
            var cfg = getCartRedirectConfig();
            var msg = (data.message ? data.message + ' ' : '') + (cfg.hint ? cfg.hint : '');
            window.showFrontAlert(msg || getCheckoutMessageOrFallback('error-generic'), 'danger');
            if (cfg.url) {
                setTimeout(function () {
                    window.location.href = cfg.url;
                }, CART_REDIRECT_DELAY_MS);
            }
        } else {
            window.showFrontAlert(getErrorMessageFromXhr(xhr), 'danger');
        }
    }

    function setAccountType(value) {
        var $hidden = $('#checkout-guest-account-type');
        var $passwordRow = $('.js-checkout-password-row');
        var $passwordInput = $('#checkout-guest-password');
        if (!$hidden.length || !$passwordRow.length) {
            return;
        }
        $hidden.val(value);
        if (value === 'register') {
            $passwordRow.removeClass('d-none');
        } else {
            $passwordRow.addClass('d-none');
            if ($passwordInput.length) {
                $passwordInput.val('');
            }
        }
    }

    function clearFormErrors($form) {
        $form.find('.js-checkout-error').text('');
        $form.find('.form-control, .form-select').removeClass('is-invalid');
    }

    function showFormErrors($form, errors) {
        clearFormErrors($form);
        $.each(errors, function (field, messages) {
            var $input = $form.find('[name="' + field + '"]');
            var $errEl = $form.find('.js-checkout-error[data-field="' + field + '"]');
            if ($input.length) {
                $input.addClass('is-invalid');
            }
            if ($errEl.length && messages[0]) {
                $errEl.text(messages[0]);
            }
        });
    }

    var CHECKOUT_STEP_DELIVERY = 2;
    var CHECKOUT_STEP_PAYMENT = 3;
    var CHECKOUT_STEP_CONFIRMATION = 4;

    var CHECKOUT_METHOD_SHIPPING = 'shipping';
    var CHECKOUT_METHOD_PAYMENT = 'payment';

    function getCheckoutStep() {
        var step = $('#checkout-step-container').attr('data-checkout-step');
        return parseInt(step, 10) || 1;
    }

    function setCheckoutStep(step) {
        $('#checkout-step-container').attr('data-checkout-step', step);
        updateConfirmOrderButtonState();
    }

    function updateConfirmOrderButtonState() {
        var $btn = $('#checkout-confirm-order-btn');
        if (!$btn.length) {
            return;
        }
        var step = getCheckoutStep();
        $btn.prop('disabled', step < CHECKOUT_STEP_CONFIRMATION);
    }

    function clearCheckoutMethodsDisplay() {
        var $shippingInput = $('#checkout-shipping-method-input');
        var $paymentInput = $('#checkout-payment-method-input');
        var $instructionsCard = $('#checkout-payment-instructions-card');
        var $instructionsBody = $('#checkout-payment-instructions-body');
        var $summaryTable = $('#checkout-order-summary-table');
        var $shippingRow = $('#checkout-order-summary-shipping-row');
        if ($shippingInput.length) {
            $shippingInput.val('');
        }
        if ($paymentInput.length) {
            $paymentInput.val('');
        }
        if ($instructionsCard.length) {
            $instructionsCard.hide();
        }
        if ($instructionsBody.length) {
            $instructionsBody.empty();
        }
        if ($shippingRow.length) {
            $shippingRow.remove();
        }
        if ($summaryTable.length) {
            var subtotalFormatted = $summaryTable.attr('data-subtotal-formatted');
            if (subtotalFormatted) {
                $('#checkout-order-summary-total-value').text(subtotalFormatted);
            }
        }
    }

    function clearPaymentMethodDisplay() {
        var $paymentInput = $('#checkout-payment-method-input');
        var $instructionsCard = $('#checkout-payment-instructions-card');
        var $instructionsBody = $('#checkout-payment-instructions-body');
        if ($paymentInput.length) {
            $paymentInput.val('');
        }
        if ($instructionsCard.length) {
            $instructionsCard.hide();
        }
        if ($instructionsBody.length) {
            $instructionsBody.empty();
        }
    }

    function updateOrderSummaryShipping(shippingName, shippingFormatted, orderTotalFormatted) {
        var $totalRow = $('#checkout-order-summary-total-row');
        var $shippingRow = $('#checkout-order-summary-shipping-row');
        if (!$totalRow.length) {
            return;
        }
        if ($shippingRow.length) {
            $shippingRow.find('td:first').text(shippingName);
            $shippingRow.find('td:last').text(shippingFormatted);
        } else {
            var $newRow = $('<tr id="checkout-order-summary-shipping-row"><td></td><td class="text-end"></td></tr>');
            $newRow.find('td:first').text(shippingName);
            $newRow.find('td:last').text(shippingFormatted);
            $totalRow.before($newRow);
        }
        $('#checkout-order-summary-total-value').text(orderTotalFormatted);
    }

    function updateCsrfToken(newToken) {
        if (newToken) {
            token = newToken;
            var $meta = $('meta[name="csrf-token"]');
            if ($meta.length) {
                $meta.attr('content', newToken);
            }
        }
    }

    function handleCheckoutFormSuccess($form, submitBtnId, result) {
        $('#' + submitBtnId).prop('disabled', false);
        if (result.success) {
            if (result.csrf_token) {
                updateCsrfToken(result.csrf_token);
            }
            clearFormErrors($form);
            showResponseMessage(result);
            setCheckoutStep(CHECKOUT_STEP_DELIVERY);
            clearCheckoutMethodsDisplay();
        } else {
            var errors = result.errors || {};
            showFormErrors($form, errors);
            if (Object.keys(errors).length > 0) {
                window.showFrontAlert($form.attr('data-validation-form-message') || getCheckoutMessageOrFallback('validation-form'), 'danger');
            } else if (result.message) {
                window.showFrontAlert(result.message, 'danger');
            }
        }
    }

    function handleCheckoutFormError($form, submitBtnId, xhr) {
        $('#' + submitBtnId).prop('disabled', false);
        var data = xhr.responseJSON;
        if (data && data.redirect_to_cart) {
            handleCheckoutCartError(xhr);
            return;
        }
        var errors = data && data.errors ? data.errors : {};
        if (Object.keys(errors).length > 0) {
            showFormErrors($form, errors);
        }
        window.showFrontAlert(getErrorMessageFromXhr(xhr), 'danger');
    }

    function initPostcodeLogic(countrySelectId, asteriskSelector) {
        var $countrySelect = $('#' + countrySelectId);
        var $asterisk = $(asteriskSelector);
        if (!$countrySelect.length || !$asterisk.length) {
            return;
        }
        function update() {
            var $selected = $countrySelect.find('option:selected');
            var required = !!$selected.data('postcode-required');
            if (required) {
                $asterisk.removeClass('d-none');
            } else {
                $asterisk.addClass('d-none');
            }
        }
        $countrySelect.on('change', update);
        update();
    }

    function submitAddressForm($formEl, submitBtnId) {
        if (!$formEl || !$formEl.length) {
            return;
        }
        $('#' + submitBtnId).prop('disabled', true);
        $.ajax({
            url: $formEl.attr('action'),
            type: 'POST',
            data: $formEl.serialize() + '&_token=' + encodeURIComponent(token),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (result) {
                handleCheckoutFormSuccess($formEl, submitBtnId, result);
            },
            error: function (xhr) {
                handleCheckoutFormError($formEl, submitBtnId, xhr);
            }
        });
    }

    function initGuestCheckout() {
        var $form = $('#checkout-guest-form');
        if (!$form.length) {
            return;
        }
        $form.find('input[name="account_type_radio"]').on('change', function () {
            setAccountType($(this).val());
        });
        setAccountType($form.find('input[name="account_type_radio"]:checked').val() || 'register');

        initPostcodeLogic('checkout-guest-country_id', '.js-postcode-asterisk');

        $form.on('submit', function (e) {
            e.preventDefault();
            $('#' + 'checkout-guest-submit').prop('disabled', true);
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize() + '&_token=' + encodeURIComponent(token),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (result) {
                    handleCheckoutFormSuccess($form, 'checkout-guest-submit', result);
                },
                error: function (xhr) {
                    handleCheckoutFormError($form, 'checkout-guest-submit', xhr);
                }
            });
        });
    }

    function initLoggedinCheckout() {
        var $customerAddressForm = $('#checkout-customer-address-form');
        if (!$customerAddressForm.length) {
            return;
        }
        initPostcodeLogic('checkout-customer-country_id', '.js-postcode-asterisk-customer');
        $customerAddressForm.on('submit', function (e) {
            e.preventDefault();
            submitAddressForm($(this), 'checkout-customer-address-submit');
        });
    }

    function initAddressSwitcher() {
        var $addressTypeExisting = $('#address-existing');
        var $addressTypeNew = $('#address-new');
        var $existingWrap = $('#checkout-shipping-address-wrap');
        var $newForm = $('#checkout-customer-new-address-form');
        if (!$addressTypeExisting.length || !$addressTypeNew.length || !$existingWrap.length || !$newForm.length) {
            return;
        }

        function toggleAddressType() {
            if ($addressTypeNew.is(':checked')) {
                $existingWrap.addClass('d-none');
                $newForm.removeClass('d-none');
            } else {
                $existingWrap.removeClass('d-none');
                $newForm.addClass('d-none');
            }
        }
        $addressTypeExisting.on('change', toggleAddressType);
        $addressTypeNew.on('change', toggleAddressType);
        toggleAddressType();

        var $addressSelect = $('#checkout-shipping-address-id');
        var $addressErrorEl = $existingWrap.find('.js-checkout-error[data-field="address_id"]');
        var addressRequiredMessage = $existingWrap.attr('data-address-required-message') || getCheckoutMessageOrFallback('address-required');
        if ($addressSelect.length) {
            var setAddressUrl = $existingWrap.attr('data-set-address-url');
            $addressSelect.on('change', function () {
                var val = $(this).val();
                if (!val) {
                    $addressSelect.addClass('is-invalid');
                    $addressErrorEl.text(addressRequiredMessage);
                    return;
                }
                $addressSelect.removeClass('is-invalid');
                $addressErrorEl.text('');
                if (!setAddressUrl) {
                    return;
                }
                $.ajax({
                    url: setAddressUrl,
                    type: 'POST',
                    data: {
                        address_id: val,
                        _token: token
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (result) {
                        if (result.success) {
                            showResponseMessage(result);
                            setCheckoutStep(CHECKOUT_STEP_DELIVERY);
                            clearCheckoutMethodsDisplay();
                        } else if (result.message) {
                            window.showFrontAlert(result.message, 'danger');
                        }
                    },
                    error: function (xhr) {
                        handleCheckoutCartError(xhr);
                    }
                });
            });
        }

        initPostcodeLogic('checkout-customer-new-country_id', '.js-postcode-asterisk-customer');
        $newForm.on('submit', function (e) {
            e.preventDefault();
            submitAddressForm($(this), 'checkout-customer-new-address-submit');
        });
    }

    var checkoutMethodModalConfig = {};
    checkoutMethodModalConfig[CHECKOUT_METHOD_SHIPPING] = {
            urlAttr: 'data-shipping-methods-url',
            setUrlAttr: 'data-set-shipping-method-url',
            modalId: 'checkout-shipping-method-modal',
            listId: 'checkout-shipping-method-modal-list',
            contentId: 'checkout-shipping-method-modal-content',
            loadingId: 'checkout-shipping-method-modal-loading',
            footerId: 'checkout-shipping-method-modal-footer',
            chooseBtnId: 'checkout-shipping-method-choose-btn',
            confirmBtnId: 'checkout-shipping-method-modal-confirm',
            inputId: 'checkout-shipping-method-input',
            radioName: 'checkout_shipping_method_radio',
            radioIdPrefix: 'shipping-method-opt-',
            emptyMessageKey: 'shipping-methods-none-available',
            getLabel: function (m) { return (m.name || '') + ' - ' + (m.formatted || ''); },
            getInputValue: function ($radio) { return ($radio.attr('data-name') || '') + ' - ' + ($radio.attr('data-formatted') || ''); },
            getRadioData: function (m) { return { 'data-name': m.name || '', 'data-formatted': m.formatted || '' }; },
            onConfirmSuccess: function (result) {
                setCheckoutStep(CHECKOUT_STEP_PAYMENT);
                clearPaymentMethodDisplay();
                if (result.method && result.order_total_formatted) {
                    updateOrderSummaryShipping(result.method.name || '', result.method.formatted || '', result.order_total_formatted);
                }
            }
    };
    checkoutMethodModalConfig[CHECKOUT_METHOD_PAYMENT] = {
            urlAttr: 'data-payment-methods-url',
            setUrlAttr: 'data-set-payment-method-url',
            modalId: 'checkout-payment-method-modal',
            listId: 'checkout-payment-method-modal-list',
            contentId: 'checkout-payment-method-modal-content',
            loadingId: 'checkout-payment-method-modal-loading',
            footerId: 'checkout-payment-method-modal-footer',
            chooseBtnId: 'checkout-payment-method-choose-btn',
            confirmBtnId: 'checkout-payment-method-modal-confirm',
            inputId: 'checkout-payment-method-input',
            radioName: 'checkout_payment_method_radio',
            radioIdPrefix: 'payment-method-opt-',
            emptyMessageKey: 'payment-methods-none-available',
            getLabel: function (m) { return m.name || ''; },
            getInputValue: function ($radio) { return $radio.attr('data-name') || ''; },
            getRadioData: function (m) { return { 'data-name': m.name || '' }; },
            onConfirmSuccess: function (result) {
                setCheckoutStep(CHECKOUT_STEP_CONFIRMATION);
                var $card = $('#checkout-payment-instructions-card');
                var $body = $('#checkout-payment-instructions-body');
                if (result.hasOwnProperty('instructions')) {
                    if (result.instructions) {
                        $body.html(result.instructions);
                        $card.show();
                    } else {
                        $body.empty();
                        $card.hide();
                    }
                }
            }
    };

    function openCheckoutMethodModal(type) {
        var cfg = checkoutMethodModalConfig[type];
        if (!cfg) { return; }
        var $container = $('#checkout-step-container');
        var url = $container.attr(cfg.urlAttr);
        var $modal = $('#' + cfg.modalId);
        var $loading = $('#' + cfg.loadingId);
        var $list = $('#' + cfg.listId);
        var $footer = $('#' + cfg.footerId);
        var $chooseBtn = $('#' + cfg.chooseBtnId);
        var $content = $('#' + cfg.contentId);
        if (!url || !$modal.length) { return; }
        $list.empty();
        $content.addClass('d-none');
        $footer.addClass('d-none');
        $loading.removeClass('d-none');
        $chooseBtn.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: token },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function (result) {
                $chooseBtn.prop('disabled', false);
                $loading.addClass('d-none');
                if (result.success && result.methods && result.methods.length) {
                    var selectedId = result.selected_id != null ? String(result.selected_id) : null;
                    var selectedIndex = 0;
                    if (selectedId) {
                        var found = result.methods.findIndex(function (m) {
                            var methodId = m.id != null ? String(m.id) : '';
                            return methodId === selectedId;
                        });
                        if (found >= 0) { selectedIndex = found; }
                    }
                    result.methods.forEach(function (m, index) {
                        var methodId = m.id != null ? String(m.id) : '';
                        var id = cfg.radioIdPrefix + (methodId || '').replace(/\s/g, '_');
                        var isChecked = (index === selectedIndex);
                        var $row = $('<div class="form-check"></div>');
                        var radioData = { type: 'radio', class: 'form-check-input', name: cfg.radioName, id: id, value: m.id || '', checked: isChecked };
                        $.extend(radioData, cfg.getRadioData(m));
                        $row.append($('<input>', radioData));
                        $row.append($('<label class="form-check-label"></label>').attr('for', id).text(cfg.getLabel(m)));
                        $list.append($row);
                    });
                    $content.removeClass('d-none');
                    $footer.removeClass('d-none');
                    var modalInstance = window.bootstrap && window.bootstrap.Modal ? new window.bootstrap.Modal($modal[0]) : ($modal.data('bs.modal') || $modal.modal());
                    modalInstance.show();
                } else {
                    var emptyMsg = getCheckoutMessage(cfg.emptyMessageKey);
                    var msg = (result.methods && result.methods.length === 0 && emptyMsg) ? emptyMsg : (result.message || getCheckoutMessageOrFallback('error-generic'));
                    window.showFrontAlert(msg, 'danger');
                }
            },
            error: function (xhr) {
                handleCheckoutCartError(xhr, function () {
                    $chooseBtn.prop('disabled', false);
                    $loading.addClass('d-none');
                });
            }
        });
    }

    function confirmCheckoutMethodSelection(type) {
        var cfg = checkoutMethodModalConfig[type];
        if (!cfg) { return; }
        var $container = $('#checkout-step-container');
        var url = $container.attr(cfg.setUrlAttr);
        var $modal = $('#' + cfg.modalId);
        var $input = $('#' + cfg.inputId);
        var $confirmBtn = $('#' + cfg.confirmBtnId);
        var $radio = $('input[name="' + cfg.radioName + '"]:checked');
        if (!url || !$radio.length) { return; }
        var methodId = $radio.val();
        $confirmBtn.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            data: { method_id: methodId, _token: token },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function (result) {
                $confirmBtn.prop('disabled', false);
                if (result.success) {
                    if ($input.length) {
                        $input.val(cfg.getInputValue($radio));
                    }
                    if (window.bootstrap && window.bootstrap.Modal && $modal.length) {
                        var modalInstance = window.bootstrap.Modal.getInstance($modal[0]);
                        if (modalInstance) { modalInstance.hide(); }
                    } else if ($modal.length && $modal.data('bs.modal')) {
                        $modal.modal('hide');
                    }
                    if (result.message) { showResponseMessage(result); }
                    if (typeof cfg.onConfirmSuccess === 'function') {
                        cfg.onConfirmSuccess(result);
                    }
                } else {
                    window.showFrontAlert(result.message || getCheckoutMessageOrFallback('error-generic'), 'danger');
                }
            },
            error: function (xhr) {
                handleCheckoutCartError(xhr, function () {
                    $confirmBtn.prop('disabled', false);
                });
            }
        });
    }

    function initChooseButtons() {
        var $shippingBtn = $('#checkout-shipping-method-choose-btn');
        var $paymentBtn = $('#checkout-payment-method-choose-btn');
        if ($shippingBtn.length) {
            $shippingBtn.on('click', function () {
                if (getCheckoutStep() < CHECKOUT_STEP_DELIVERY) {
                    var msg = getCheckoutMessageOrFallback('shipping-address-required');
                    window.showFrontAlert(msg, 'danger');
                    return;
                }
                var $addressWrap = $('#checkout-shipping-address-wrap');
                var $addressSelect = $('#checkout-shipping-address-id');
                if ($addressWrap.length && $addressSelect.length && !$addressWrap.hasClass('d-none')) {
                    var addressId = $addressSelect.val();
                    if (!addressId || addressId === '') {
                        var msg = $addressWrap.attr('data-address-required-message') || getCheckoutMessageOrFallback('address-required');
                        window.showFrontAlert(msg, 'danger');
                        return;
                    }
                }
                openCheckoutMethodModal(CHECKOUT_METHOD_SHIPPING);
            });
        }
        if ($paymentBtn.length) {
            $paymentBtn.on('click', function () {
                if (getCheckoutStep() < CHECKOUT_STEP_PAYMENT) {
                    window.showFrontAlert(getCheckoutMessageOrFallback('payment-after-shipping'), 'danger');
                    return;
                }
                openCheckoutMethodModal(CHECKOUT_METHOD_PAYMENT);
            });
        }
        $('#checkout-shipping-method-modal-confirm').on('click', function () {
            confirmCheckoutMethodSelection(CHECKOUT_METHOD_SHIPPING);
        });
        $('#checkout-payment-method-modal-confirm').on('click', function () {
            confirmCheckoutMethodSelection(CHECKOUT_METHOD_PAYMENT);
        });
    }

    function initConfirmOrderButton() {
        var $btn = $('#checkout-confirm-order-btn');
        var $container = $('#checkout-step-container');
        var url = $container.attr('data-confirm-order-url');
        if (!$btn.length || !url) {
            return;
        }
        $btn.on('click', function () {
            if ($btn.prop('disabled')) {
                return;
            }
            $btn.prop('disabled', true);
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: token,
                    comment: $('#checkout-order-comment').val() || ''
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (result) {
                    if (result.success && result.redirect_url) {
                        window.location.href = result.redirect_url;
                    } else {
                        $btn.prop('disabled', false);
                        window.showFrontAlert(result.message || getCheckoutMessageOrFallback('error-generic'), 'danger');
                    }
                },
                error: function (xhr) {
                    handleCheckoutCartError(xhr, function () {
                        $btn.prop('disabled', false);
                    });
                }
            });
        });
    }

    function initCheckout() {
        initGuestCheckout();
        initLoggedinCheckout();
        initAddressSwitcher();
        initChooseButtons();
        initConfirmOrderButton();
        updateConfirmOrderButtonState();
    }

    $(function () {
        initCheckout();
    });
})();
