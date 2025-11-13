(function($) {
    'use strict';

    let featureCounter = 0;
    let featuresData = [];
    let texts = {};
    let $featuresContainer;
    let validationErrors = {};

    $(document).ready(function() {
        $featuresContainer = $('#features-container');

        if (!$featuresContainer.length) {
            return;
        }

        featuresData = $featuresContainer.data('features') || [];
        validationErrors = $featuresContainer.data('errors') || {};
        texts = {
            feature: $featuresContainer.data('labelFeature') || 'Feature',
            previousFeatureValue: $featuresContainer.data('labelPreviousFeatureValue') || 'Previous feature value',
            selectFeature: $featuresContainer.data('selectFeatureText') || 'Select feature',
            selectValue: $featuresContainer.data('selectValueText') || 'Select value',
        };

        // Handle add feature button click
        $('#add-feature-btn').on('click', function() {
            addFeatureRow();
        });

        // Handle delete feature row
        $(document).on('click', '.remove-feature-btn', function() {
            $(this).closest('.feature-row').remove();
        });

        // Handle feature selection change - load values
        $(document).on('change', '.feature-select', function() {
            const featureId = $(this).val();
            const row = $(this).closest('.feature-row');
            loadFeatureValues(featureId, row);
        });

        // Restore features from old input after validation errors
        restoreFeaturesFromOld();
    });

    function addFeatureRow() {
        featureCounter++;
        
        let featureOptions = '<option value="">' + texts.selectFeature + '</option>';
        
        featuresData.forEach(function(feature) {
            featureOptions += '<option value="' + feature.id + '">' + feature.name + '</option>';
        });
        
        const featureError = getFeatureError(featureCounter, 'feature_id');
        const featureValueError = getFeatureError(featureCounter, 'feature_value_id');
        
        const rowHtml = `
            <div class="feature-row mb-3 p-3 border rounded" data-feature-index="${featureCounter}">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">${texts.feature}</label>
                        <select name="features[${featureCounter}][feature_id]" class="form-select feature-select ${featureError ? 'is-invalid' : ''}">
                            ${featureOptions}
                        </select>
                        ${featureError ? '<div class="invalid-feedback">' + featureError + '</div>' : ''}
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">${texts.previousFeatureValue}</label>
                        <select name="features[${featureCounter}][feature_value_id]" class="form-select feature-value-select ${featureValueError ? 'is-invalid' : ''}">
                            <option value="">${texts.selectValue}</option>
                        </select>
                        ${featureValueError ? '<div class="invalid-feedback">' + featureValueError + '</div>' : ''}
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-feature-btn">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#features-container').append(rowHtml);
    }

    function loadFeatureValues(featureId, row) {
        const selectValueText = texts.selectValue;
        
        if (!featureId) {
            row.find('.feature-value-select').html('<option value="">' + selectValueText + '</option>');
            return;
        }

        const feature = featuresData.find(f => f.id == featureId);
        
        let options = '<option value="">' + selectValueText + '</option>';
        if (feature && feature.values) {
            feature.values.forEach(function(value) {
                const label = value.value || '';
                options += '<option value="' + value.id + '">' + label + '</option>';
            });
        }
        row.find('.feature-value-select').html(options);
    }

    function getFeatureError(index, field) {
        const errorKey = 'features.' + index + '.' + field;
        if (validationErrors[errorKey] && validationErrors[errorKey].length > 0) {
            return validationErrors[errorKey][0];
        }
        // If feature_id has duplicate error, also show it for feature_value_id
        if (field === 'feature_value_id') {
            const featureIdErrorKey = 'features.' + index + '.feature_id';
            if (validationErrors[featureIdErrorKey] && validationErrors[featureIdErrorKey].length > 0) {
                const errorMessage = validationErrors[featureIdErrorKey][0];
                // Check if it's a duplicate error (contains "duplicate" or "already used")
                if (errorMessage.toLowerCase().indexOf('duplicate') !== -1 || 
                    errorMessage.toLowerCase().indexOf('already used') !== -1) {
                    return errorMessage;
                }
            }
        }
        return null;
    }

    function restoreFeaturesFromOld() {
        const oldFeatures = $featuresContainer.data('oldFeatures') || [];
        
        if (!oldFeatures || Object.keys(oldFeatures).length === 0) {
            return;
        }

        // Find the highest index from old features to set featureCounter correctly
        let maxIndex = 0;
        Object.keys(oldFeatures).forEach(function(key) {
            const numKey = parseInt(key);
            if (!isNaN(numKey) && numKey > maxIndex) {
                maxIndex = numKey;
            }
        });

        // Restore each feature row with original index
        Object.keys(oldFeatures).forEach(function(key) {
            const feature = oldFeatures[key];
            const featureId = feature.feature_id || null;
            const featureValueId = feature.feature_value_id || null;

            // Skip if both are empty
            if (!featureId && !featureValueId) {
                return;
            }

            // Create row with original index
            const rowIndex = parseInt(key);
            featureCounter = rowIndex - 1; // Set to rowIndex - 1 because addFeatureRow() increments it
            addFeatureRow();
            const row = $('.feature-row').last();

            // Set feature_id
            if (featureId) {
                row.find('.feature-select').val(featureId);
                
                // Load feature values first
                loadFeatureValues(featureId, row);
                
                // Then set feature_value_id after values are loaded
                if (featureValueId) {
                    // Use setTimeout to ensure options are loaded
                    setTimeout(function() {
                        row.find('.feature-value-select').val(featureValueId);
                    }, 50);
                }
            }
            
            // Display validation errors
            const featureError = getFeatureError(rowIndex, 'feature_id');
            const featureValueError = getFeatureError(rowIndex, 'feature_value_id');
            
            if (featureError) {
                row.find('.feature-select').addClass('is-invalid');
                if (row.find('.feature-select').next('.invalid-feedback').length === 0) {
                    row.find('.feature-select').after('<div class="invalid-feedback">' + featureError + '</div>');
                }
            }
            
            if (featureValueError) {
                row.find('.feature-value-select').addClass('is-invalid');
                if (row.find('.feature-value-select').next('.invalid-feedback').length === 0) {
                    row.find('.feature-value-select').after('<div class="invalid-feedback">' + featureValueError + '</div>');
                }
            }
        });
        
        // Set featureCounter to maxIndex + 1 so new rows continue from there
        featureCounter = maxIndex;
    }

})(jQuery);
