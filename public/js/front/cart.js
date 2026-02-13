(function () {
    'use strict';

    var token = $('meta[name="csrf-token"]').attr('content');
    var showResponseMessage = window.showResponseMessage;
    var showAjaxError = window.showAjaxError;

    function updateCartCount(count) {
        $('.cart-products-count').text('(' + count + ')');
    }

    function updateCartTotals(subtotalFormatted) {
        $('.cart-subtotal').text(subtotalFormatted);
        $('.cart-total').text(subtotalFormatted);
    }

    $(document).on('submit', '.js-cart-update-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var url = $form.attr('action');
        var data = {
            product_id: $form.find('input[name="product_id"]').val(),
            quantity: $form.find('.cart-quantity-input').val(),
            _token: token,
            _method: 'PUT'
        };
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function (response) {
                if (response.success) {
                    updateCartCount(response.cart_count);
                    $form.find('.cart-quantity-input').val(response.quantity);
                    $form.closest('tr').find('.cart-row-total').text(response.row_total_formatted);
                    updateCartTotals(response.subtotal_formatted);
                }
                showResponseMessage(response);
            },
            error: showAjaxError
        });
    });

    $(document).on('submit', '.js-cart-remove-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var url = $form.attr('action');
        var data = {
            product_id: $form.find('input[name="product_id"]').val(),
            _token: token,
            _method: 'DELETE'
        };
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function (response) {
                if (response.success) {
                    updateCartCount(response.cart_count);
                    $form.closest('tr.cart-row').fadeOut(200, function () {
                        $(this).remove();
                    });
                    updateCartTotals(response.subtotal_formatted);
                    if (response.empty) {
                        $('#cart-content').hide();
                        $('#cart-empty-placeholder').removeClass('d-none');
                    }
                }
                showResponseMessage(response);
            },
            error: showAjaxError
        });
    });
})();
