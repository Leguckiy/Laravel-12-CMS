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
})();
