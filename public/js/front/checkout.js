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

    function getCheckoutStep() {
        var step = $('#checkout-step-container').data('checkout-step');
        return parseInt(step, 10) || 1;
    }

    function setCheckoutStep(step) {
        $('#checkout-step-container').attr('data-checkout-step', step);
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
                        } else if (result.message) {
                            window.showFrontAlert(result.message, 'danger');
                        }
                    },
                    error: function (xhr) {
                        window.showFrontAlert(getErrorMessageFromXhr(xhr), 'danger');
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

    function openShippingMethodModal() {
        var $container = $('#checkout-step-container');
        var url = $container.attr('data-shipping-methods-url');
        var $modal = $('#checkout-shipping-method-modal');
        var $loading = $('#checkout-shipping-method-modal-loading');
        var $list = $('#checkout-shipping-method-modal-list');
        var $footer = $('#checkout-shipping-method-modal-footer');
        var $chooseBtn = $('#checkout-shipping-method-choose-btn');
        if (!url || !$modal.length) {
            return;
        }
        var $content = $('#checkout-shipping-method-modal-content');
        $list.empty();
        $content.addClass('d-none');
        $footer.addClass('d-none');
        $loading.removeClass('d-none');
        $chooseBtn.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: token },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
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
                        if (found >= 0) {
                            selectedIndex = found;
                        }
                    }
                    result.methods.forEach(function (m, index) {
                        var methodId = m.id != null ? String(m.id) : '';
                        var id = 'shipping-method-opt-' + (methodId || '').replace(/\s/g, '_');
                        var isChecked = (index === selectedIndex);
                        var $row = $('<div class="form-check"></div>');
                        $row.append(
                            $('<input>', {
                                type: 'radio',
                                class: 'form-check-input',
                                name: 'checkout_shipping_method_radio',
                                id: id,
                                value: m.id || '',
                                checked: isChecked,
                                'data-name': m.name || '',
                                'data-formatted': m.formatted || ''
                            })
                        );
                        $row.append($('<label class="form-check-label"></label>').attr('for', id).text((m.name || '') + ' - ' + (m.formatted || '')));
                        $list.append($row);
                    });
                    $content.removeClass('d-none');
                    $footer.removeClass('d-none');
                    var modalInstance = window.bootstrap && window.bootstrap.Modal ? new window.bootstrap.Modal($modal[0]) : ($modal.data('bs.modal') || $modal.modal());
                    modalInstance.show();
                } else {
                    var emptyMsg = getCheckoutMessage('shipping-methods-none-available');
                    var msg = (result.methods && result.methods.length === 0 && emptyMsg) ? emptyMsg : (result.message || getCheckoutMessageOrFallback('error-generic'));
                    window.showFrontAlert(msg, 'danger');
                }
            },
            error: function (xhr) {
                $chooseBtn.prop('disabled', false);
                $loading.addClass('d-none');
                window.showFrontAlert(getErrorMessageFromXhr(xhr), 'danger');
            }
        });
    }

    function confirmShippingMethodSelection() {
        var $container = $('#checkout-step-container');
        var url = $container.attr('data-set-shipping-method-url');
        var $modal = $('#checkout-shipping-method-modal');
        var $input = $('#checkout-shipping-method-input');
        var $confirmBtn = $('#checkout-shipping-method-modal-confirm');
        var $radio = $('input[name="checkout_shipping_method_radio"]:checked');
        if (!url || !$radio.length) {
            return;
        }
        var methodId = $radio.val();
        var methodName = $radio.attr('data-name');
        var methodFormatted = $radio.attr('data-formatted');
        $confirmBtn.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            data: { method_id: methodId, _token: token },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (result) {
                $confirmBtn.prop('disabled', false);
                if (result.success) {
                    if ($input.length) {
                        $input.val((methodName || '') + ' - ' + (methodFormatted || ''));
                    }
                    setCheckoutStep(CHECKOUT_STEP_PAYMENT);
                    if (window.bootstrap && window.bootstrap.Modal && $modal.length) {
                        var modalInstance = window.bootstrap.Modal.getInstance($modal[0]);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    } else if ($modal.length && $modal.data('bs.modal')) {
                        $modal.modal('hide');
                    }
                    if (result.message) {
                        showResponseMessage(result);
                    }
                } else {
                    window.showFrontAlert(result.message || getCheckoutMessageOrFallback('error-generic'), 'danger');
                }
            },
            error: function (xhr) {
                $confirmBtn.prop('disabled', false);
                window.showFrontAlert(getErrorMessageFromXhr(xhr), 'danger');
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
                openShippingMethodModal();
            });
        }
        if ($paymentBtn.length) {
            $paymentBtn.on('click', function () {
                if (getCheckoutStep() < CHECKOUT_STEP_PAYMENT) {
                    window.showFrontAlert(getCheckoutMessageOrFallback('payment-after-shipping'), 'danger');
                    return;
                }
                // TODO: open payment methods modal/list
            });
        }
        $('#checkout-shipping-method-modal-confirm').on('click', function () {
            confirmShippingMethodSelection();
        });
    }

    function initCheckout() {
        initGuestCheckout();
        initLoggedinCheckout();
        initAddressSwitcher();
        initChooseButtons();
    }

    $(function () {
        initCheckout();
    });
})();
