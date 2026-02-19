(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    var token = csrfToken ? csrfToken.getAttribute('content') : '';

    $(document).on('click', '.currency-selector a[data-currency-id]', function (e) {
        e.preventDefault();
        var $link = $(this);
        var currencyId = $link.data('currency-id');
        var $selector = $link.closest('.currency-selector');
        var url = $selector.data('set-currency-url');
        if (!url || !currencyId) {
            return;
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                currency_id: currencyId,
                _token: token
            },
            success: function () {
                location.reload();
            }
        });
    });

    const FRONT_ALERT_AUTO_HIDE_MS = 4000;
    window.FRONT_ALERT_AUTO_HIDE_MS = FRONT_ALERT_AUTO_HIDE_MS;

    function showFrontAlert(message, type) {
        var $container = $('#front-ajax-alerts');
        $container.empty();
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var $alert = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        $container.append($alert);
        $('html, body').animate({ scrollTop: $container.offset().top - 20 }, 300);
        setTimeout(function () {
            $alert.alert('close');
        }, FRONT_ALERT_AUTO_HIDE_MS);
    }

    function showResponseMessage(response) {
        if (response.success && response.message) {
            showFrontAlert(response.message, 'success');
        } else if (response.message) {
            showFrontAlert(response.message, 'danger');
        }
    }

    function showAjaxError(xhr) {
        var data = xhr.responseJSON;
        var msg = (data && data.message) ? data.message : 'Error.';
        showFrontAlert(msg, 'danger');
    }

    function addToCart(url, productId, quantity) {
        if (!url || !productId || quantity < 1) {
            return;
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                _token: token
            },
            success: function (response) {
                if (response.success && typeof response.cart_count !== 'undefined') {
                    $('.cart-products-count').text('(' + response.cart_count + ')');
                }
                showResponseMessage(response);
            },
            error: showAjaxError
        });
    }

    $(document).on('click', '.js-add-to-cart', function () {
        var $btn = $(this);
        var url = $btn.data('cart-add-url');
        var productId = $btn.data('product-id');
        var quantity;
        if ($('#product-quantity').length) {
            quantity = parseInt($('#product-quantity').val(), 10);
        } else {
            quantity = 1;
        }
        addToCart(url, productId, quantity);
    });

    window.showFrontAlert = showFrontAlert;
    window.showResponseMessage = showResponseMessage;
    window.showAjaxError = showAjaxError;
})();
