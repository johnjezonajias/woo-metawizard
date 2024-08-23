jQuery(document).ready(function($) {
    $('#woo_metawizard_generate_seo').on('click', function(e) {
        e.preventDefault();

        const data = {
            action: 'woo_metawizard_generate_seo',
            post_id: $('#post_ID').val(),
            nonce: woo_metawizard.nonce
        };

        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                // Parse the suggested data.
                const suggestedData = JSON.parse(response.data);

                // Populate fields with the response data.
                $('#woo_metawizard_meta_title').val(suggestedData.meta_title);
                $('#woo_metawizard_meta_description').val(suggestedData.meta_description);
                $('#woo_metawizard_meta_keywords').val(suggestedData.meta_keywords);

                // Append the new suggestion section.
                /*$('#woo_metawizard_suggestions').append(`
                    <div class="woo-metawizard-suggestion">
                        <h3>Product Meta Suggestions:</h3>
                        <p><strong>Suggested Meta Title:</strong><br>${suggestedData.meta_title}</p>
                        <p><strong>Suggested Meta Description:</strong><br>${suggestedData.meta_description}</p>
                        <p><strong>Suggested Meta Keywords:</strong><br>${suggestedData.meta_keywords}</p>
                    </div>
                `);*/
            } else {
                alert(response.data.message);
            }
        });
    });

    // Handle the delete action
    $('.woo-metawizard-delete').on('click', function(e) {
        e.preventDefault();
        
        var $row = $(this).closest('tr');
        var index = $(this).data('index');
        var $spinner = $row.find('.spinner');

        if (confirm('Are you sure you want to delete this suggestion?')) {
            $row.css('opacity', '0.6');
            $spinner.show();

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
                    } else {
                        alert('Failed to delete the suggestion.');
                        $row.css('opacity', '1');
                        $spinner.hide();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('An error occurred while processing your request: ' + textStatus + ' - ' + errorThrown);
                    $row.css('opacity', '1');
                    $spinner.hide();
                },
                complete: function() {
                    $spinner.hide();
                }
            });
        }
    });
});
