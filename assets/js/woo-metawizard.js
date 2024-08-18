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

                // Append the new suggestion section.
                $('#woo_metawizard_suggestions').append(`
                    <div class="woo-metawizard-suggestion">
                        <h3>Product Meta Suggestions:</h3>
                        <p><strong>Suggested Meta Title:</strong><br>${suggestedData.meta_title}</p>
                        <p><strong>Suggested Meta Description:</strong><br>${suggestedData.meta_description}</p>
                        <p><strong>Suggested Meta Keywords:</strong><br>${suggestedData.meta_keywords}</p>
                    </div>
                `);
            } else {
                alert(response.data.message);
            }
        });
    });
});
