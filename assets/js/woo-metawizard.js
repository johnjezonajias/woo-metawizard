jQuery(document).ready(function($) {
    // Handle generate suggestion action.
    $('#woo_metawizard_generate_seo').on('click', function(e) {
        e.preventDefault();
    
        // Spinner animation.
        $('#form-spinner').show();
        $('.woo-metawizard-placeholder .woo-metawizard-wrap').css('opacity', '0.6');
        $('.woo-metawizard-placeholder input, .woo-metawizard-placeholder textarea, .woo-metawizard-placeholder button').attr('disabled', true);
    
        const data = {
            action: 'woo_metawizard_generate_seo',
            post_id: $('#post_ID').val(),
            nonce: woo_metawizard.nonce
        };
    
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    try {
                        const suggestedData = JSON.parse(response.data);
            
                        // Populate fields with the response data.
                        $('#woo_metawizard_meta_title').val(suggestedData.meta_title);
                        $('#woo_metawizard_meta_description').val(suggestedData.meta_description);
                        $('#woo_metawizard_meta_keywords').val(suggestedData.meta_keywords);
                    } catch (error) {
                        alert('Failed to parse the response data: ' + error.message);
                    }
                } else {
                    alert(response.data.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('An error occurred while generating the suggestion: ' + textStatus + ' - ' + errorThrown);
            },
            complete: function() {
                $('#form-spinner').hide();
                $('.woo-metawizard-placeholder .woo-metawizard-wrap').css('opacity', '1');
                $('.woo-metawizard-placeholder input, .woo-metawizard-placeholder textarea, .woo-metawizard-placeholder button').attr('disabled', false);
            }
        });
    });
    
    // Handle yoast suggestion population action.
    $('#woo_metawizard_use_for_yoast').on('click', function(e) {
        e.preventDefault();

        var metaTitle = $('#woo_metawizard_meta_title').val().trim();
        var metaDescription = $('#woo_metawizard_meta_description').val().trim();
        var metaKeywords = $('#woo_metawizard_meta_keywords').val().trim();

        // Populate the hidden Yoast fields.
        $('#yoast_wpseo_title').val(metaTitle);
        $('#yoast_wpseo_metadesc').val(metaDescription);
        $('#yoast_wpseo_focuskw').val(metaKeywords);

        // Update the Meta Keywords.
        $('#focus-keyword-input-metabox').val(metaKeywords);

        // Update the SEO Title.
        var titleEditorContainer = document.querySelector('#yoast-google-preview-title-metabox');
        if (titleEditorContainer) {
            var titleEditorContent = titleEditorContainer.querySelector('[data-contents="true"]');
            // Clear existing content and append new text.
            while (titleEditorContent.firstChild) {
                titleEditorContent.removeChild(titleEditorContent.firstChild);
            }
            titleEditorContent.appendChild(document.createTextNode(metaTitle));
            titleEditorContainer.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // Update the SEO Description.
        var descriptionEditorContainer = document.querySelector('#yoast-google-preview-description-metabox');
        if (descriptionEditorContainer) {
            var descriptionEditorContent = descriptionEditorContainer.querySelector('[data-contents="true"]');
            // Clear existing content and append new text.
            while (descriptionEditorContent.firstChild) {
                descriptionEditorContent.removeChild(descriptionEditorContent.firstChild);
            }
            descriptionEditorContent.appendChild(document.createTextNode(metaDescription));
            descriptionEditorContainer.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // Scroll to the Yoast section.
        $('html, body').animate({
            scrollTop: $('#wpseo_meta').offset().top
        }, 800);
    });

    // Handle save suggestion action.
    $('#woo_metawizard_save_suggestion').on('click', function(e) {
        e.preventDefault();

        // Retrieve the values from the input fields.
        var metaTitle = $('#woo_metawizard_meta_title').val().trim();
        var metaDescription = $('#woo_metawizard_meta_description').val().trim();
        var metaKeywords = $('#woo_metawizard_meta_keywords').val().trim();

        // Check if any of the fields are empty.
        if (!metaTitle || !metaDescription || !metaKeywords) {
            alert('Please fill in all the fields.');
            return;
        }

        // Spinner animation.
        $('#form-spinner').show();
        $('.woo-metawizard-placeholder .woo-metawizard-wrap').css('opacity', '0.6');
        $('.woo-metawizard-placeholder input, .woo-metawizard-placeholder textarea, .woo-metawizard-placeholder button').attr('disabled', true);

        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'woo_metawizard_save_suggestion',
                post_id: woo_metawizard.post_id,
                meta_title: metaTitle,
                meta_description: metaDescription,
                meta_keywords: metaKeywords,
                nonce: woo_metawizard.nonce_save
            },
            success: function(response) {
                if (response.success) {
                    alert('Suggestion saved successfully.');

                    // Clear the input fields.
                    $('#woo_metawizard_meta_title').val('');
                    $('#woo_metawizard_meta_description').val('');
                    $('#woo_metawizard_meta_keywords').val('');

                    // Refresh the suggestions table.
                    $('#table-spinner').show();
                    $('#woo-metawizard-table').css('opacity', '0.6');

                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'woo_metawizard_refresh_suggestions_table',
                            post_id: woo_metawizard.post_id,
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#woo-metawizard-table tbody').html(response.data);
                            } else {
                                alert('Failed to refresh suggestions table.');
                            }
                        },
                        error: function() {
                            alert('An error occurred while refreshing the suggestions table.');
                        },
                        complete: function() {
                            $('#table-spinner').hide();
                            $('#woo-metawizard-table').css('opacity', '1');
                        }
                    });
                } else {
                    alert('Failed to save suggestion.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('An error occurred while processing your request: ' + textStatus + ' - ' + errorThrown);
            },
            complete: function() {
                $('#form-spinner').hide();
                $('.woo-metawizard-placeholder .woo-metawizard-wrap').css('opacity', '1');
                $('.woo-metawizard-placeholder input, .woo-metawizard-placeholder textarea').attr('disabled', false);
            }
        });
    });

    // Handle the delete action.
    $('#woo-metawizard-table').on('click', '.woo-metawizard-delete', function(e) {
        e.preventDefault();

        var $row = $(this).closest('tr');
        var index = $(this).data('index');
        var $td = $row.find('td');

        if (confirm('Are you sure you want to delete this suggestion?')) {
            // Append the spinner to the row.
            $row.append('<span id="row-spinner" class="spinner" style="display:none;"></span>');

            $td.css('opacity', '0.6');
            $('#row-spinner').show();

            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'woo_metawizard_delete_suggestion',
                    post_id: woo_metawizard.post_id,
                    index: index,
                    nonce: woo_metawizard.nonce_delete
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                        });

                        // Refresh the suggestions table.
                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            data: {
                                action: 'woo_metawizard_refresh_suggestions_table',
                                post_id: woo_metawizard.post_id
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#woo-metawizard-table tbody').html(response.data);
                                } else {
                                    alert('Failed to refresh suggestions table.');
                                }
                            },
                            error: function() {
                                alert('An error occurred while refreshing the suggestions table.');
                            },
                            complete: function() {
                                $('#row-spinner').hide();
                            }
                        });
                    } else {
                        alert('Failed to delete the suggestion.');
                        $td.css('opacity', '1');
                        $('#row-spinner').hide();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('An error occurred while processing your request: ' + textStatus + ' - ' + errorThrown);
                    $td.css('opacity', '1');
                    $('#row-spinner').hide();
                },
                complete: function() {
                    $('#row-spinner').hide();
                }
            });
        }
    });

    function toggleSaveButton() {
        // Check if any of the input fields are non-empty.
        var metaTitle = $('#woo_metawizard_meta_title').val().trim();
        var metaDescription = $('#woo_metawizard_meta_description').val().trim();
        var metaKeywords = $('#woo_metawizard_meta_keywords').val().trim();

        // Enable the button if at least one field is not empty.
        if (metaTitle && metaDescription && metaKeywords) {
            $('#woo_metawizard_use_for_yoast').prop('disabled', false);
            $('#woo_metawizard_save_suggestion').prop('disabled', false);
        } else {
            $('#woo_metawizard_use_for_yoast').prop('disabled', true);
            $('#woo_metawizard_save_suggestion').prop('disabled', true);
        }
    }

    // Initial check to set the button state.
    toggleSaveButton();

    // Monitor changes in the input fields.
    $('#woo_metawizard_meta_title, #woo_metawizard_meta_description, #woo_metawizard_meta_keywords').on('input', function() {
        toggleSaveButton();
    });
});
