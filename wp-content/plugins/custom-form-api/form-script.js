jQuery(document).ready(function ($) {
    $('#custom-form').on('submit', function (e) {
        e.preventDefault();

        const $submitBtn = $('#submit-btn');
        const $btnText = $submitBtn.find('.btn-text');
        const $spinner = $submitBtn.find('.spinner-border');
        const $message = $('#form-message');

        // Reset message
        $message.html('');

        $submitBtn.prop('disabled', true);
        $btnText.addClass('d-none');
        $spinner.removeClass('d-none');

        const formData = new FormData(this);
        formData.append('action', 'cfapi_submit_form');
        formData.append('security', cfapi_obj.nonce);

        $.ajax({
            url: cfapi_obj.ajax_url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    $message.html('<div class="alert alert-success">' + response.data.message + '</div>');
                    $('#custom-form')[0].reset();
                } else {
                    $message.html('<div class="alert alert-danger">' + response.data.message + '</div>');
                }
            },
            error: function () {
                $message.html('<div class="alert alert-danger">Something went wrong on the server.</div>');
            },
            complete: function () {
                $submitBtn.prop('disabled', false);
                $btnText.removeClass('d-none');
                $spinner.addClass('d-none');
            }
        });
    });
});
