$(function () {
    var $form = $('#category-filters-form');
    if (!$form.length) return;

    var $qInput = $('#filter-q');
    var $inStock = $('#filter-in-stock');
    var $featureCheckboxes = $form.find('.category-filter-feature-value');
    var $rangeMin = $form.find('.category-price-range-min');
    var $rangeMax = $form.find('.category-price-range-max');
    var $inputMin = $form.find('.category-price-input-min');
    var $inputMax = $form.find('.category-price-input-max');
    var priceRangeMin = $rangeMin.length ? (parseFloat($rangeMin.attr('min')) || 0) : 0;
    var priceRangeMax = $rangeMax.length ? (parseFloat($rangeMax.attr('max')) || 0) : 0;

    function parseQ(q) {
        if (!q || !q.trim()) return { stock: false, price: null, features: {} };
        var parts = q.trim().split('/');
        var result = { stock: false, price: null, features: {} };
        for (var i = 0; i < parts.length; i++) {
            var part = parts[i];
            if (part === 'stock-1' || part === 'stock') result.stock = true;
            else if (part.indexOf('price-') === 0) {
                var range = part.slice(6).split('-');
                if (range.length >= 2) result.price = range[0] + '-' + range[1];
            } else if (part.indexOf('feature-') === 0) {
                var rest = part.slice(8);
                var dash = rest.indexOf('-');
                if (dash > 0) {
                    var fid = rest.slice(0, dash);
                    var vids = rest.slice(dash + 1).split(',').filter(Boolean);
                    if (fid && vids.length) result.features[fid] = vids;
                }
            }
        }
        return result;
    }

    function buildQ(state) {
        var parts = [];
        if (state.stock) parts.push('stock-1');
        if (state.price) parts.push('price-' + state.price);
        var fids = Object.keys(state.features).sort(function(a,b){return parseInt(a,10)-parseInt(b,10);});
        for (var i = 0; i < fids.length; i++) {
            if (state.features[fids[i]].length) parts.push('feature-' + fids[i] + '-' + state.features[fids[i]].join(','));
        }
        return parts.join('/');
    }

    function prepareFormForSubmit() {
        var state = parseQ($qInput.val() || '');
        var hadPriceInUrl = state.price !== null;
        state.stock = $inStock.is(':checked');
        state.features = {};
        $featureCheckboxes.filter(':checked').each(function() {
            var fid = $(this).attr('data-feature-id');
            var vid = $(this).attr('data-value-id');
            if (fid && vid) {
                if (!state.features[fid]) state.features[fid] = [];
                state.features[fid].push(String(vid));
            }
        });
        var min = $rangeMin.length ? (parseFloat($rangeMin.val()) || priceRangeMin) : priceRangeMin;
        var max = $rangeMax.length ? (parseFloat($rangeMax.val()) || priceRangeMax) : priceRangeMax;
        var epsilon = 0.01;
        var userNarrowedRange = min > priceRangeMin + epsilon || max < priceRangeMax - epsilon;
        if (hadPriceInUrl || userNarrowedRange) {
            state.price = Math.round(min) + '-' + Math.round(max);
        } else {
            state.price = null;
        }
        $qInput.val(buildQ(state));
        $featureCheckboxes.removeAttr('name');
        if ($inputMin.length) $inputMin.removeAttr('name');
        if ($inputMax.length) $inputMax.removeAttr('name');
    }

    function buildFilterQueryString(qVal, sortVal, sortInUrl) {
        var params = [];
        if (qVal && qVal.trim()) {
            params.push('q=' + encodeURIComponent(qVal.trim()).replace(/%2F/g, '/').replace(/%2C/g, ','));
        }
        if (sortVal && (sortVal !== 'position' || sortInUrl)) {
            params.push('sort=' + encodeURIComponent(sortVal));
        }
        return params.join('&');
    }

    function navigateToFilterUrl(baseUrl, queryString) {
        window.location = baseUrl + (queryString ? '?' + queryString : '');
    }

    function submitFormWithReadableUrl() {
        prepareFormForSubmit();
        var sortInUrl = $form.data('sort-in-url');
        var queryString = buildFilterQueryString($qInput.val() || '', $('#category-sort').val(), sortInUrl);
        navigateToFilterUrl($form.attr('action'), queryString);
    }

    $form.on('submit', function (e) {
        e.preventDefault();
        submitFormWithReadableUrl();
    });
    $inStock.on('change', submitFormWithReadableUrl);
    $featureCheckboxes.on('change', submitFormWithReadableUrl);

    function initPriceSlider() {
        if (!$rangeMin.length || !$rangeMax.length) return;

        var $track = $form.find('.front-price-range-slider__track');

        function updateTrack() {
            if (!$track.length || priceRangeMax <= priceRangeMin) return;
            var min = parseFloat($rangeMin.val()) || priceRangeMin;
            var max = parseFloat($rangeMax.val()) || priceRangeMax;
            var start = ((min - priceRangeMin) / (priceRangeMax - priceRangeMin)) * 100;
            var end = ((max - priceRangeMin) / (priceRangeMax - priceRangeMin)) * 100;
            start = Math.max(0, Math.min(100, start));
            end = Math.max(0, Math.min(100, end));
            if (end < start) {
                var t = start;
                start = end;
                end = t;
            }
            $track.css('--range-start', start + '%').css('--range-end', end + '%');
        }

        function clampMinMax() {
            var min = parseFloat($rangeMin.val());
            var max = parseFloat($rangeMax.val());
            if (min > max) $rangeMax.val(min);
            if (max < min) $rangeMin.val(max);
            $inputMin.val($rangeMin.val());
            $inputMax.val($rangeMax.val());
            updateTrack();
            submitFormWithReadableUrl();
        }

        function syncFromInputs() {
            var min = Math.max(priceRangeMin, Math.min(priceRangeMax, parseFloat($inputMin.val()) || priceRangeMin));
            var max = Math.max(priceRangeMin, Math.min(priceRangeMax, parseFloat($inputMax.val()) || priceRangeMax));
            if (min > max) {
                $inputMin.val(max);
                min = max;
            }
            if (max < min) {
                $inputMax.val(min);
                max = min;
            }
            $rangeMin.val(min);
            $rangeMax.val(max);
            updateTrack();
            submitFormWithReadableUrl();
        }

        updateTrack();
        $rangeMin.on('input', clampMinMax);
        $rangeMax.on('input', clampMinMax);
        $inputMin.on('change', syncFromInputs);
        $inputMax.on('change', syncFromInputs);
    }

    initPriceSlider();

    var $sortForm = $('#category-sort-form');
    var $sortSelect = $('#category-sort');
    if ($sortForm.length && $sortSelect.length) {
        $sortSelect.on('change', function () {
            var sortInUrl = $form.data('sort-in-url');
            var queryString = buildFilterQueryString($qInput.val() || '', $sortSelect.val(), sortInUrl);
            navigateToFilterUrl($sortForm.attr('action'), queryString);
        });
    }
});
