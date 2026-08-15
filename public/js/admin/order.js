(function ($) {
    'use strict';

    const state = {
        customerId: null,
        addresses: [],
    };

    function getSearchUrl() {
        const el = document.getElementById('order-customer-search');
        if (!el) {
            return null;
        }
        return el.getAttribute('data-search-url') || null;
    }

    function getEmptyLabel() {
        const el = document.getElementById('order-customer-search');
        return el.getAttribute('data-empty-label');
    }

    function renderAddressOptions() {
        const select = $('#order-shipping-address');
        if (!select.length) {
            return;
        }

        const placeholderText = select.data('placeholder') || '--- Please Select ---';

        select.empty();

        select.append(
            $('<option>').val('').prop('selected', true).text(placeholderText)
        );

        if (!state.addresses.length) {
            return;
        }

        let defaultId = null;
        const defaultAddress = state.addresses.find(function (a) { return a.default; });
        if (defaultAddress) {
            defaultId = defaultAddress.id;
        } else {
            defaultId = state.addresses[0].id;
        }

        state.addresses.forEach(function (address) {
            const option = $('<option>')
                .val(address.id)
                .text(address.label);
            if (address.id === defaultId) {
                option.prop('selected', true);
            }
            select.append(option);
        });
    }

    function applyCustomer(customerId, displayText, addresses) {
        state.customerId = customerId;
        state.addresses = addresses || [];

        $('#order-customer-id').val(customerId || '');
        $('#order-customer-search').val(displayText || '');

        renderAddressOptions();
    }

    const $orderFormCard = $('#order-form-card');
    const currencyState = {
        id: $orderFormCard.data('currentCurrencyId') || $('select[name=\"currency_id\"]').val() || null,
        decimals: parseInt($orderFormCard.data('currentCurrencyDecimals'), 10) || 2,
        left: $orderFormCard.data('currentCurrencySymbolLeft') || '',
        right: $orderFormCard.data('currentCurrencySymbolRight') || '',
    };

    function formatForCurrentCurrency(amount) {
        const num = Number(amount) || 0;
        const decimals = currencyState.decimals || 2;
        const formatted = num.toFixed(decimals);
        return (currencyState.left || '') + formatted + (currencyState.right || '');
    }

    function recalculateRowTotal($row) {
        const qty = parseFloat($row.find('.order-item-qty').val()) || 0;
        const price = parseFloat($row.find('.order-item-price-input').val()) || 0;
        const total = qty * price;

        const decimals = currencyState.decimals || 2;
        const formatted = total.toFixed(decimals);
        $row.find('.order-item-total-display').text(formatForCurrentCurrency(formatted));
        $row.find('.order-item-total-input').val(formatted);

        recalculateOrderSummary();
    }

    function updateEmptyRowVisibility() {
        const $tbody = $('#order-items-body');
        const hasRows = $tbody.find('.order-item-row').length > 0;
        $tbody.find('.order-items-empty-row').toggleClass('d-none', hasRows);
        if (!hasRows && !$tbody.find('.order-items-empty-row').length) {
            $tbody.append(
                $('<tr class=\"order-items-empty-row\">')
                    .append(
                        $('<td colspan=\"6\" class=\"text-center text-muted\">')
                            .text($('#order-items-step').data('noItemsLabel') || '')
                    )
            );
        }

        const $summaryWrapper = $('#order-summary-wrapper');
        if ($summaryWrapper.length) {
            $summaryWrapper.toggleClass('d-none', !hasRows);
        }

        $(document).trigger('order-items-updated');
    }

    function recalculateOrderSummary() {
        const $tbody = $('#order-items-body');
        if (!$tbody.length) {
            return;
        }

        let subtotal = 0;
        $tbody.find('.order-item-row').each(function () {
            const val = parseFloat($(this).find('.order-item-total-input').val());
            if (!Number.isNaN(val)) {
                subtotal += val;
            }
        });

        const subtotalFormatted = subtotal.toFixed(2);
        const $subtotalSpan = $('#order-summary-subtotal');
        if ($subtotalSpan.length) {
            $subtotalSpan.text(formatForCurrentCurrency(subtotalFormatted));
        }

        const $shippingRow = $('#order-summary-shipping-row');
        let shippingAmount = 0;
        if ($shippingRow.length) {
            const rawShipping = $shippingRow.data('shippingAmount');
            shippingAmount = parseFloat(rawShipping);
            if (Number.isNaN(shippingAmount)) {
                shippingAmount = 0;
            }

            const $shippingAmountSpan = $('#order-summary-shipping-amount');
            if (shippingAmount > 0) {
                $shippingRow.removeClass('d-none');
                if ($shippingAmountSpan.length) {
                    $shippingAmountSpan.text(formatForCurrentCurrency(shippingAmount));
                }
            } else {
                $shippingRow.addClass('d-none');
                if ($shippingAmountSpan.length) {
                    $shippingAmountSpan.text(formatForCurrentCurrency(0));
                }
            }
        }

        const total = subtotal + shippingAmount;
        const totalFormatted = total.toFixed(2);

        const $totalSpan = $('#order-summary-total');
        if ($totalSpan.length) {
            $totalSpan.text(formatForCurrentCurrency(totalFormatted));
        }

        const $subtotalInput = $('#order-subtotal-input');
        if ($subtotalInput.length) {
            $subtotalInput.val(subtotalFormatted);
        }

        const $totalInput = $('#order-total-input');
        if ($totalInput.length) {
            $totalInput.val(totalFormatted);
        }
    }

    function addOrderItemRow(initialData) {
        initialData = initialData || {};
        const $tbody = $('#order-items-body');
        if (!$tbody.length) {
            return;
        }

        let nextIndex = parseInt($tbody.data('next-index'), 10);
        if (!Number.isFinite(nextIndex) || nextIndex < 0) {
            nextIndex = 0;
        }

        const productId = typeof initialData.product_id !== 'undefined' ? initialData.product_id : '';
        const name = typeof initialData.name !== 'undefined' ? initialData.name : '';
        const quantity = typeof initialData.quantity !== 'undefined' ? initialData.quantity : 1;
        const available = typeof initialData.available !== 'undefined' ? initialData.available : null;
        const priceVal = typeof initialData.price !== 'undefined' ? (parseFloat(initialData.price) || 0) : 0;
        const priceFormatted = priceVal.toFixed(2);

        const $row = $('<tr>').addClass('order-item-row');

        const $productTd = $('<td>');
        $productTd.append($('<input>', {
            type: 'hidden',
            name: 'items[' + nextIndex + '][product_id]',
            value: productId,
        }));
        $productTd.append($('<input>', {
            type: 'text',
            name: 'items[' + nextIndex + '][name]',
            class: 'form-control order-item-name',
            value: name,
        }));

        const $qtyTd = $('<td>').addClass('text-center');
        $qtyTd.append($('<input>', {
            type: 'number',
            min: 1,
            name: 'items[' + nextIndex + '][quantity]',
            class: 'form-control text-center order-item-qty',
            value: quantity,
        }));

        const $availableTd = $('<td>').addClass('text-center');
        $availableTd.append(
            $('<span>', {
                class: 'order-item-available',
                text: available !== null && available !== undefined && available !== '' ? available : '—',
            })
        );

        const $priceTd = $('<td>').addClass('text-end');
        $priceTd.append(
            $('<span>', {
                class: 'order-item-price-display',
                text: formatForCurrentCurrency(priceFormatted),
            })
        );
        $priceTd.append($('<input>', {
            type: 'hidden',
            name: 'items[' + nextIndex + '][price]',
            class: 'order-item-price-input',
            value: priceFormatted,
        }));

        const initialTotal = (quantity * priceVal).toFixed(2);
        const $totalTd = $('<td>').addClass('text-end');
        $totalTd.append(
            $('<span>', {
                class: 'order-item-total-display',
                text: formatForCurrentCurrency(initialTotal),
            })
        );
        $totalTd.append($('<input>', {
            type: 'hidden',
            name: 'items[' + nextIndex + '][total]',
            class: 'order-item-total-input',
            value: initialTotal,
        }));

        const $actionTd = $('<td>').addClass('text-center');
        const $removeBtn = $('<button>', {
            type: 'button',
            class: 'btn btn-sm btn-danger order-item-remove-btn',
        }).append($('<i>', { class: 'fa-solid fa-trash' }));
        $actionTd.append($removeBtn);

        $row.append($productTd, $qtyTd, $availableTd, $priceTd, $totalTd, $actionTd);

        $tbody.find('.order-items-empty-row').remove();
        $tbody.append($row);

        recalculateRowTotal($row);

        $tbody.data('next-index', nextIndex + 1);
        updateEmptyRowVisibility();
    }

    function getProductSearchUrl() {
        const el = document.getElementById('order-product-modal-name');
        if (!el) {
            return null;
        }

        return el.getAttribute('data-search-url') || null;
    }

    function attachProductSearchModal() {
        const $input = $('#order-product-modal-name');
        if (!$input.length) {
            return;
        }

        const searchUrl = getProductSearchUrl();
        if (!searchUrl) {
            return;
        }

        const $container = $input.closest('.position-relative');
        let debounceTimer = null;
        let $dropdown = null;

        function ensureDropdown() {
            if ($dropdown && $dropdown.length) {
                return $dropdown;
            }
            $dropdown = $('<div class="order-product-dropdown" id="order-product-dropdown" role="listbox"></div>');
            $container.append($dropdown);
            return $dropdown;
        }

        function hideDropdown() {
            const $d = $('#order-product-dropdown');
            if ($d.length) {
                $d.removeClass('is-open').empty();
            }
        }

        function showDropdown(items) {
            const $d = ensureDropdown();
            $d.empty();

            items.forEach(function (item) {
                const $btn = $('<button type="button" class="order-product-dropdown-item" role="option">')
                    .text(item.label)
                    .attr('data-product-id', item.id || '')
                    .attr('data-name', item.name || '')
                    .attr('data-price', item.price != null ? item.price : '')
                    .attr('data-available', item.available != null ? item.available : '');

                $btn.on('click', function (e) {
                    e.preventDefault();
                    const id = $(this).attr('data-product-id') || '';
                    const name = $(this).attr('data-name') || '';
                    const priceRaw = $(this).attr('data-price');
                    const availableRaw = $(this).attr('data-available');
                    const price = priceRaw !== null && priceRaw !== undefined && priceRaw !== ''
                        ? parseFloat(priceRaw) || 0
                        : 0;
                    const available = availableRaw !== null && availableRaw !== undefined && availableRaw !== ''
                        ? parseInt(availableRaw, 10) || 0
                        : 0;

                    $('#order-product-modal-id').val(id);
                    $('#order-product-modal-name').val(name);
                    $('#order-product-modal-price').val(price.toFixed(2));
                    $('#order-product-modal-available').val(available > 0 ? available : '');

                    hideDropdown();
                });

                $d.append($btn);
            });

            $d.addClass('is-open');
        }

        function performSearch(query) {
            if (!query || query.length < 2) {
                hideDropdown();
                return;
            }

            $.ajax({
                url: searchUrl,
                type: 'GET',
                dataType: 'json',
                data: { q: query },
                success: function (response) {
                    const results = response.data || [];

                    if (!results.length) {
                        hideDropdown();
                        return;
                    }

                    const items = results.map(function (p) {
                        const baseName = p.name || '';
                        const ref = p.reference ? ' [' + p.reference + ']' : '';
                        return {
                            id: p.id,
                            name: baseName,
                            label: baseName + ref,
                            price: p.price,
                            available: typeof p.quantity !== 'undefined' ? p.quantity : null,
                        };
                    });

                    showDropdown(items);
                },
                error: function () {
                    hideDropdown();
                },
            });
        }

        $input.on('input', function () {
            const query = $(this).val().trim();

            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function () {
                performSearch(query);
            }, 300);
        });

        $input.on('blur', function () {
            setTimeout(function () {
                hideDropdown();
            }, 200);
        });
    }

    function attachCustomerSearch() {
        const $input = $('#order-customer-search');
        if (!$input.length) {
            return;
        }

        const searchUrl = getSearchUrl();
        if (!searchUrl) {
            return;
        }

        const $hiddenId = $('#order-customer-id');
        const $container = $input.parent();
        let debounceTimer = null;
        let $dropdown = null;

        function ensureDropdown() {
            if ($dropdown && $dropdown.length) {
                return $dropdown;
            }
            $dropdown = $('<div class="order-customer-dropdown" id="order-customer-dropdown" role="listbox"></div>');
            $container.append($dropdown);
            return $dropdown;
        }

        function hideDropdown() {
            const $d = $('#order-customer-dropdown');
            if ($d.length) {
                $d.removeClass('is-open').empty();
            }
        }

        function showDropdown(items) {
            const $d = ensureDropdown();
            $d.empty();

            items.forEach(function (item) {
                const $btn = $('<button type="button" class="order-customer-dropdown-item" role="option">')
                    .text(item.label)
                    .attr('data-customer-id', item.id === null || item.id === undefined ? '' : item.id)
                    .attr('data-display', item.label || '');

                if (item.addresses) {
                    $btn.attr('data-addresses', JSON.stringify(item.addresses));
                }

                $btn.on('click', function (e) {
                    e.preventDefault();
                    const id = $(this).attr('data-customer-id');
                    const display = $(this).attr('data-display') || '';
                    let addresses = [];
                    try {
                        const raw = $(this).attr('data-addresses');
                        if (raw) {
                            addresses = JSON.parse(raw);
                        }
                    } catch (err) {
                        addresses = [];
                    }

                    if (id === '' || id === undefined) {
                        applyCustomer(null, '', []);
                    } else if (addresses.length === 0) {
                        const msg = $input.attr('data-no-addresses-message') ||
                            'This customer has no addresses. Please add at least one address.';

                        if (window.showAdminAlert) {
                            window.showAdminAlert(msg, 'danger');
                        } else {
                            window.alert(msg);
                        }
                    } else {
                        applyCustomer(id, display, addresses);
                    }
                    hideDropdown();
                });

                $d.append($btn);
            });

            $d.addClass('is-open');
        }

        function performSearch(query) {
            if (!query || query.length < 2) {
                hideDropdown();
                return;
            }

            $.ajax({
                url: searchUrl,
                type: 'GET',
                dataType: 'json',
                data: { q: query },
                success: function (response) {
                    const results = response.data || [];
                    const emptyLabel = getEmptyLabel();

                    const items = [
                        { id: null, label: emptyLabel },
                    ].concat(results.map(function (c) {
                        const label = (c.name || '') + (c.email ? ' (' + c.email + ')' : '');
                        return {
                            id: c.id,
                            label: label,
                            addresses: c.addresses || [],
                        };
                    }));

                    showDropdown(items);
                },
                error: function () {
                    hideDropdown();
                },
            });
        }

        $input.on('input', function () {
            const query = $(this).val().trim();

            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function () {
                performSearch(query);
            }, 300);
        });

        $input.on('blur', function () {
            setTimeout(function () {
                hideDropdown();
            }, 200);
        });

        // Init state when editing: keep existing customer and address options
        const existingId = $hiddenId.val();
        const $select = $('#order-shipping-address');
        if (existingId && $select.find('option').length) {
            state.customerId = existingId;
            state.addresses = [];
            $select.find('option').each(function () {
                const val = $(this).val();
                if (val === '' || val === 'none') {
                    return;
                }
                state.addresses.push({
                    id: val,
                    label: $(this).text(),
                    default: $(this).prop('selected'),
                });
            });
        }
    }

    $(document).ready(function () {
        attachCustomerSearch();
        attachProductSearchModal();

        $('#order-shipping-method-input').val($('#order-shipping-method-name').val());

        $('#order-payment-method-input').val($('#order-payment-method-name').val());  

        $('#order-add-item-btn').on('click', function () {
            const $name = $('#order-product-modal-name');
            const $id = $('#order-product-modal-id');
            const $qty = $('#order-product-modal-qty');
            const $price = $('#order-product-modal-price');

            $name.val('');
            $id.val('');
            $qty.val(1);
            $price.val('0');

            const modalEl = document.getElementById('order-product-modal');
            if (window.bootstrap && window.bootstrap.Modal && modalEl) {
                const instance = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                instance.show();
            } else {
                $('#order-product-modal').modal('show');
            }
        });

        $('#order-product-modal-confirm').on('click', function () {
            const name = $('#order-product-modal-name').val().trim();
            const productId = $('#order-product-modal-id').val().trim();
            const qty = parseInt($('#order-product-modal-qty').val(), 10) || 1;
            const price = parseFloat($('#order-product-modal-price').val()) || 0;
            const availableRaw = $('#order-product-modal-available').val();
            const available = availableRaw !== '' && availableRaw !== null && availableRaw !== undefined
                ? parseInt(availableRaw, 10) || 0
                : null;

            if (!name) {
                if (window.showAdminAlert) {
                    window.showAdminAlert($('#order-product-modal-name').data('validationMessage') || 'Please enter product name.', 'danger');
                } else {
                    window.alert('Please enter product name.');
                }
                return;
            }

            // If product already exists in the table, just increase quantity
            if (productId) {
                const $existingRow = $('#order-items-body .order-item-row').filter(function () {
                    const val = $(this).find('input[name$=\"[product_id]\"]').val();
                    return val && String(val) === String(productId);
                }).first();

                if ($existingRow.length) {
                    const currentQty = parseInt($existingRow.find('.order-item-qty').val(), 10) || 0;
                    const newQty = currentQty + qty;
                    $existingRow.find('.order-item-qty').val(newQty);
                    recalculateRowTotal($existingRow);

                    const modalEl = document.getElementById('order-product-modal');
                    if (window.bootstrap && window.bootstrap.Modal && modalEl) {
                        const instance = window.bootstrap.Modal.getInstance(modalEl);
                        if (instance) {
                            instance.hide();
                        }
                    } else {
                        $('#order-product-modal').modal('hide');
                    }

                    return;
                }
            }

            addOrderItemRow({
                product_id: productId,
                name: name,
                quantity: qty,
                price: price,
                available: available,
            });

            const modalEl = document.getElementById('order-product-modal');
            if (window.bootstrap && window.bootstrap.Modal && modalEl) {
                const instance = window.bootstrap.Modal.getInstance(modalEl);
                if (instance) {
                    instance.hide();
                }
            } else {
                $('#order-product-modal').modal('hide');
            }
        });

        $(document).on('click', '.order-item-remove-btn', function () {
            const $row = $(this).closest('.order-item-row');
            $row.remove();
            updateEmptyRowVisibility();
            recalculateOrderSummary();
        });

        $(document).on('focus', '.order-item-qty', function () {
            $(this).data('prevValue', $(this).val());
        });

        $('#order-items-body .order-item-row').each(function () {
            recalculateRowTotal($(this));
        });

        updateEmptyRowVisibility();
        recalculateOrderSummary();

        const $currencySelect = $('select[name=\"currency_id\"]');
        const $orderFormCard = $('#order-form-card');
        const changeCurrencyUrl = $orderFormCard.data('changeCurrencyUrl');
        const checkQuantityUrl = $orderFormCard.data('checkQuantityUrl');
        const shippingMethodsUrl = $('#order-shipping-method-choose-btn').data('shippingMethodsUrl');
        const setShippingMethodUrl = $('#order-shipping-method-choose-btn').data('setShippingMethodUrl');
        const paymentMethodsUrl = $('#order-payment-method-choose-btn').data('paymentMethodsUrl');
        const setPaymentMethodUrl = $('#order-payment-method-choose-btn').data('setPaymentMethodUrl');

        if ($currencySelect.length && changeCurrencyUrl) {
            $currencySelect.on('change', function () {
                const newId = $(this).val();
                const fromId = currencyState.id;

                if (!newId || !fromId || String(newId) === String(fromId)) {
                    return;
                }

                const csrfToken = $('#form-order input[name=\"_token\"]').val();

                $.ajax({
                    url: changeCurrencyUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        from_currency_id: fromId,
                        to_currency_id: newId,
                        _token: csrfToken,
                    },
                    success: function (resp) {
                        if (!resp || !resp.success) {
                            return;
                        }

                        const rate = parseFloat(resp.rate) || 1;
                        currencyState.id = newId;
                        currencyState.decimals = resp.decimals || 2;
                        currencyState.left = resp.symbol_left || '';
                        currencyState.right = resp.symbol_right || '';

                        $('#order-items-body .order-item-row').each(function () {
                            const $row = $(this);
                            const $priceInput = $row.find('.order-item-price-input');
                            const $totalInput = $row.find('.order-item-total-input');

                            const oldPrice = parseFloat($priceInput.val()) || 0;
                            const oldTotal = parseFloat($totalInput.val()) || 0;

                            const newPrice = oldPrice * rate;
                            const newTotal = oldTotal * rate;

                            $priceInput.val(newPrice.toFixed(currencyState.decimals));
                            $totalInput.val(newTotal.toFixed(currencyState.decimals));

                            $row.find('.order-item-price-display').text(
                                formatForCurrentCurrency(newPrice)
                            );
                            $row.find('.order-item-total-display').text(
                                formatForCurrentCurrency(newTotal)
                            );
                        });

                        const $subtotalInput = $('#order-subtotal-input');
                        const $totalInput = $('#order-total-input');
                        const $shippingRow = $('#order-summary-shipping-row');

                        const oldSubtotal = parseFloat($subtotalInput.val()) || 0;
                        const oldTotal = parseFloat($totalInput.val()) || 0;
                        const oldShipping = parseFloat($shippingRow.data('shippingAmount')) || 0;

                        const newSubtotal = oldSubtotal * rate;
                        const newTotal = oldTotal * rate;
                        const newShipping = oldShipping * rate;

                        $subtotalInput.val(newSubtotal.toFixed(currencyState.decimals));
                        $totalInput.val(newTotal.toFixed(currencyState.decimals));
                        $shippingRow.data('shippingAmount', newShipping.toFixed(currencyState.decimals));

                        $('#order-summary-subtotal').text(formatForCurrentCurrency(newSubtotal));
                        $('#order-summary-total').text(formatForCurrentCurrency(newTotal));

                        if (newShipping > 0) {
                            $('#order-summary-shipping-amount')
                                .text(formatForCurrentCurrency(newShipping));
                            $shippingRow.removeClass('d-none');
                        } else {
                            $('#order-summary-shipping-amount')
                                .text(formatForCurrentCurrency(0));
                            $shippingRow.addClass('d-none');
                        }
                    },
                });
            });
        }

        if (checkQuantityUrl) {
            $(document).on('change', '.order-item-qty', function () {
                const $input = $(this);
                const $row = $input.closest('.order-item-row');
                const productId = $row.find('input[name$=\"[product_id]\"]').val();
                const currencyId = $('select[name=\"currency_id\"]').val();
                const orderId = $('#form-order').data('orderId') || '';

                if (!productId || !currencyId) {
                    recalculateRowTotal($row);
                    return;
                }

                let quantity = parseInt($input.val(), 10);
                if (!Number.isFinite(quantity) || quantity <= 0) {
                    quantity = 1;
                    $input.val(quantity);
                }

                const csrfToken = $('#form-order input[name=\"_token\"]').val()
                    || $('meta[name=\"csrf-token\"]').attr('content')
                    || '';

                $.ajax({
                    url: checkQuantityUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        product_id: productId,
                        quantity: quantity,
                        currency_id: currencyId,
                        order_id: orderId,
                        _token: csrfToken,
                    },
                    success: function (resp) {
                        if (!resp || !resp.success) {
                            const prev = $input.data('prevValue');
                            if (resp && resp.message && window.showAdminAlert) {
                                window.showAdminAlert(resp.message, 'danger');
                            } else if (resp && resp.message) {
                                window.alert(resp.message);
                            }
                            if (prev !== undefined) {
                                $input.val(prev);
                                recalculateRowTotal($row);
                            }
                            return;
                        }

                        $input.val(resp.quantity);

                        if (typeof resp.available !== 'undefined') {
                            $row.find('.order-item-available').text(resp.available);
                        }

                        if (resp.price && typeof resp.price.raw !== 'undefined') {
                            $row.find('.order-item-price-input').val(resp.price.raw);
                        }
                        if (resp.price && resp.price.formatted) {
                            $row.find('.order-item-price-display').text(resp.price.formatted);
                        }

                        if (resp.total && typeof resp.total.raw !== 'undefined') {
                            $row.find('.order-item-total-input').val(resp.total.raw);
                        }
                        if (resp.total && resp.total.formatted) {
                            $row.find('.order-item-total-display').text(resp.total.formatted);
                        }

                        recalculateOrderSummary();
                        $input.data('prevValue', resp.quantity);
                    },
                    error: function () {
                        const prev = $input.data('prevValue');
                        if (prev !== undefined) {
                            $input.val(prev);
                            recalculateRowTotal($row);
                        }
                    },
                });
            });
        }

        const $shippingChooseBtn = $('#order-shipping-method-choose-btn');
        const $paymentChooseBtn = $('#order-payment-method-choose-btn');

        function collectOrderItemsForShipping() {
            const items = [];
            $('#order-items-body .order-item-row').each(function () {
                const $row = $(this);
                const qty = parseInt($row.find('.order-item-qty').val(), 10) || 0;
                const price = parseFloat($row.find('.order-item-price-input').val()) || 0;
                if (qty > 0 && price >= 0) {
                    items.push({
                        quantity: qty,
                        price: price,
                    });
                }
            });
            return items;
        }

        function canChooseShipping() {
            const hasCustomer = $('#order-customer-id').val();
            const hasAddress = $('#order-shipping-address').val();
            const hasItems = $('#order-items-body .order-item-row').length > 0;
            return !!(hasCustomer && hasAddress && hasItems);
        }

        function canChoosePayment() {
            const hasShippingMethod = $('#order-shipping-method-code').val();
            return !!hasShippingMethod;
        }

        function updateShippingChooseState() {
            if (!$shippingChooseBtn.length) {
                return;
            }
            $shippingChooseBtn.prop('disabled', !canChooseShipping());
        }

        function updatePaymentChooseState() {
            const $paymentChooseBtn = $('#order-payment-method-choose-btn');

            if (!$paymentChooseBtn.length) {
                return;
            }
            $paymentChooseBtn.prop('disabled', !canChoosePayment());
        }

        function updateOrderConfirmState() {
            const paymentMethodSelected = $('#order-payment-method-code').val();

            $('#order-confirm-btn').prop(
                'disabled',
                !paymentMethodSelected
            );
        }

        function setCurrentOrderStatusId(statusId) {
            $('#order-history-form').find('[name="current_order_status_id"]').val(statusId);
        }

        $('#order-customer-id, #order-shipping-address').on('change', updateShippingChooseState);

        $(document).on('order-items-updated', updateShippingChooseState);

        $('#order-history-form').on('submit', function (e) {
            e.preventDefault();

            const $form = $(this);
            const $button = $('#order-history-add-btn');

            const currentStatusId = String($form.find('[name="current_order_status_id"]').val());
            const selectedStatusId = String($form.find('[name="order_status_id"]').val());

            if (currentStatusId === selectedStatusId) {
                return;
            }

            const url = $button.data('historyUrl');

            if (!url) {
                return;
            }

            const csrfToken = $form.find('input[name="_token"]').val();

            $button.prop('disabled', true);

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: csrfToken,
                    order_status_id: $form.find('[name="order_status_id"]').val(),
                    comment: $form.find('[name="comment"]').val(),
                    notify: $form.find('[name="notify"]').is(':checked') ? 1 : 0,
                },
                success: function (response) {
                    if (!response || !response.success) {
                        if (response?.message && window.showAdminAlert) {
                            window.showAdminAlert(response.message, 'danger');
                        }

                        return;
                    }

                    if (window.showAdminAlert) {
                        window.showAdminAlert(
                            response.message || 'History added.',
                            'success'
                        );
                    }

                    setCurrentOrderStatusId(selectedStatusId);

                    $form.find('[name="comment"]').val('');
                    $form.find('[name="notify"]').prop('checked', false);
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.message;

                    if (message && window.showAdminAlert) {
                        window.showAdminAlert(message, 'danger');
                    }
                },
                complete: function () {
                    $button.prop('disabled', false);
                },
            });
        });

        updateShippingChooseState();
        updatePaymentChooseState();
        updateOrderConfirmState();

        if ($shippingChooseBtn.length && shippingMethodsUrl && setShippingMethodUrl) {
            $shippingChooseBtn.on('click', function () {
                if (!canChooseShipping()) {
                    return;
                }

                const modalEl = document.getElementById('order-shipping-method-modal');
                const $loading = $('#order-shipping-method-modal-loading');
                const $content = $('#order-shipping-method-modal-content');
                const $list = $('#order-shipping-method-modal-list');
                const $footer = $('#order-shipping-method-modal-footer');
                const $error = $('#order-shipping-method-modal-error');

                const csrfToken = $('#form-order input[name=\"_token\"]').val()
                    || $('meta[name=\"csrf-token\"]').attr('content')
                    || '';

                $list.empty();
                $error.addClass('d-none').text('');
                $content.addClass('d-none');
                $footer.addClass('d-none');
                $loading.removeClass('d-none');

                $.ajax({
                    url: shippingMethodsUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrfToken,
                        shipping_address_id: $('#order-shipping-address').val(),
                        currency_id: $('select[name=\"currency_id\"]').val(),
                        items: collectOrderItemsForShipping(),
                    },
                    success: function (resp) {
                        $loading.addClass('d-none');

                        if (!resp || !resp.success || !resp.methods || !resp.methods.length) {
                            const msg = resp && resp.message
                                ? resp.message
                                : $('#order-shipping-method-card').data('noMethodsMessage')
                                  || 'No shipping methods available for the selected address.';
                            $error.removeClass('d-none').text(msg);
                            return;
                        }

                        resp.methods.forEach(function (m, index) {
                            const id = 'order-shipping-method-opt-' + index;
                            const $row = $('<div class=\"form-check\"></div>');
                            $row.append(
                                $('<input>', {
                                    type: 'radio',
                                    class: 'form-check-input',
                                    name: 'order_shipping_method_radio',
                                    id: id,
                                    value: m.id,
                                    checked: index === 0,
                                    'data-name': m.name,
                                    'data-cost': m.cost,
                                    'data-formatted': m.formatted,
                                })
                            );
                            $row.append(
                                $('<label class=\"form-check-label\"></label>')
                                    .attr('for', id)
                                    .text(m.name + ' - ' + m.formatted)
                            );
                            $list.append($row);
                        });

                        $content.removeClass('d-none');
                        $footer.removeClass('d-none');

                        if (window.bootstrap && window.bootstrap.Modal) {
                            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
                        }
                    },
                    error: function () {
                        $loading.addClass('d-none');
                        $error.removeClass('d-none').text('Error loading shipping methods.');
                    },
                });

                $('#order-shipping-method-modal-confirm').off('click').on('click', function () {
                    const $radio = $('input[name=\"order_shipping_method_radio"]:checked');
                    if (!$radio.length) {
                        return;
                    }

                    const csrf = $('#form-order input[name=\"_token\"]').val()
                        || $('meta[name=\"csrf-token\"]').attr('content')
                        || '';

                    $.ajax({
                        url: setShippingMethodUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: csrf,
                            method_id: $radio.val(),
                            shipping_address_id: $('#order-shipping-address').val(),
                            currency_id: $('select[name=\"currency_id\"]').val(),
                            subtotal_base: $('#order-subtotal-input').val(),
                            items: collectOrderItemsForShipping(),
                        },
                        success: function (resp) {
                            if (!resp || !resp.success) {
                                if (resp && resp.message && window.showAdminAlert) {
                                    window.showAdminAlert(resp.message, 'danger');
                                }
                                return;
                            }

                            $('#order-shipping-method-code').val($radio.val());
                            $('#order-shipping-method-name').val($radio.data('name'));
                            $('#order-shipping-method-cost').val($radio.data('cost'));

                            updatePaymentChooseState();

                            var label = $radio.data('name');
                            if ($radio.data('formatted')) {
                                label += ' - ' + $radio.data('formatted');
                            }

                            $('#order-shipping-method-input').val(label);

                            const cost = parseFloat($radio.data('cost')) || 0;
                            const $shippingRow = $('#order-summary-shipping-row');
                            $shippingRow.data('shippingAmount', cost);
                            $('#order-summary-shipping-label').text($radio.data('name'));
                            $('#order-summary-shipping-amount').text(formatForCurrentCurrency(cost));

                            recalculateOrderSummary();

                            if (window.bootstrap && window.bootstrap.Modal) {
                                const instance = window.bootstrap.Modal.getInstance(modalEl);
                                if (instance) {
                                    instance.hide();
                                }
                            }
                        },
                    });
                });
            });
        }

        if ($paymentChooseBtn.length && paymentMethodsUrl && setPaymentMethodUrl) {
            $paymentChooseBtn.on('click', function () {
                const shippingMethodId = $('#order-shipping-method-code').val();

                if (!shippingMethodId) {
                    return;
                }

                const modalEl = document.getElementById('order-payment-method-modal');
                const $loading = $('#order-payment-method-modal-loading');
                const $content = $('#order-payment-method-modal-content');
                const $list = $('#order-payment-method-modal-list');
                const $footer = $('#order-payment-method-modal-footer');
                const $error = $('#order-payment-method-modal-error');
                const csrfToken = $('#form-order input[name="_token"]').val()
                    || $('meta[name="csrf-token"]').attr('content')
                    || '';

                $list.empty();
                $error.addClass('d-none').text('');
                $content.addClass('d-none');
                $footer.addClass('d-none');
                $loading.removeClass('d-none');

                $.ajax({
                    url: paymentMethodsUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrfToken,
                        shipping_method_id: shippingMethodId,
                        shipping_address_id: $('#order-shipping-address').val(),
                        currency_id: $('select[name="currency_id"]').val(),
                    },

                    success: function (resp) {
                        $loading.addClass('d-none');

                        if (!resp || !resp.success || !resp.methods || !resp.methods.length) {
                            $error.removeClass('d-none').text('No payment methods available.');

                            return;
                        }

                        resp.methods.forEach(function (method, index) {
                            const id = 'order-payment-method-opt-' + index;
                            const $row = $('<div class="form-check"></div>');

                            $row.append(
                                $('<input>', {
                                    type: 'radio',
                                    class: 'form-check-input',
                                    name: 'order_payment_method_radio',
                                    id: id,
                                    value: method.id,
                                    checked: index === 0,
                                    'data-name': method.name,
                                })
                            );

                            $row.append(
                                $('<label>', {
                                    class: 'form-check-label',
                                    for: id,
                                    text: method.name
                                })
                            );

                            $list.append($row);
                        });

                        $content.removeClass('d-none');
                        $footer.removeClass('d-none');

                        if (window.bootstrap && window.bootstrap.Modal) {
                            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
                        }
                    },

                    error: function () {
                        $loading.addClass('d-none');

                        $error.removeClass('d-none').text('Error loading payment methods.');
                    }
                });

                $('#order-payment-method-modal-confirm').on('click', function () {
                    const $radio = $('input[name="order_payment_method_radio"]:checked');

                    if (!$radio.length) {
                        return;
                    }

                    $('#order-payment-method-code').val($radio.val());
                    $('#order-payment-method-name').val($radio.data('name'));
                    $('#order-payment-method-input').val($radio.data('name'));

                    updateOrderConfirmState();

                    const modalEl = document.getElementById('order-payment-method-modal');

                    if (window.bootstrap && window.bootstrap.Modal) {
                        const instance = window.bootstrap.Modal.getInstance(modalEl);

                        if (instance) {
                            instance.hide();
                        }
                    }
                });
            });
        }
    });
})(jQuery);
