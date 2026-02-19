(function () {
    'use strict';

    var token = $('meta[name="csrf-token"]').attr('content') || '';
    var showResponseMessage = window.showResponseMessage;
    var $messages = $('#checkout-js-messages');

    function getCheckoutMessage(key) {
        return ($messages.length && $messages.attr('data-' + key)) || '';
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

    function showFormErrors($form, errors) {
        var errorEls = $form.find('.js-checkout-error');
        var inputs = $form.find('.form-control, .form-select');
        errorEls.text('');
        inputs.removeClass('is-invalid');
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

    function handleCheckoutFormSuccess($form, submitBtnId, result) {
        $('#' + submitBtnId).prop('disabled', false);
        if (result.success) {
            showResponseMessage(result);
        } else {
            var errors = result.errors || {};
            showFormErrors($form, errors);
            if (Object.keys(errors).length > 0) {
                window.showFrontAlert($form.attr('data-validation-form-message') || getCheckoutMessage('validation-form') || 'Please correct the errors in the form.', 'danger');
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
        var msg = (data && data.message) ? data.message : (getCheckoutMessage('error-generic') || 'Error.');
        window.showFrontAlert(msg, 'danger');
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
        var addressRequiredMessage = $existingWrap.attr('data-address-required-message') || getCheckoutMessage('address-required') || 'Please select an address.';
        if ($addressSelect.length) {
            $addressSelect.on('change', function () {
                var val = $(this).val();
                if (!val) {
                    $addressSelect.addClass('is-invalid');
                    $addressErrorEl.text(addressRequiredMessage);
                    return;
                }
                $addressSelect.removeClass('is-invalid');
                $addressErrorEl.text('');
                $.ajax({
                    url: $newForm.attr('action'),
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
                        } else if (result.message) {
                            window.showFrontAlert(result.message, 'danger');
                        }
                    },
                    error: function (xhr) {
                        var data = xhr.responseJSON;
                        var msg = (data && data.message) ? data.message : (getCheckoutMessage('error-generic') || 'Error.');
                        window.showFrontAlert(msg, 'danger');
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

    function initCheckout() {
        initGuestCheckout();
        initLoggedinCheckout();
        initAddressSwitcher();
    }

    $(function () {
        initCheckout();
    });
})();
