$(document).ready(function () {
    $("form#contactForm").submit(function (event) {
        var form = $(this);

        // Reset form control
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $('.alert').remove();

        // Get data
        var formData = {
            inputFullname: $("#inputFullname").val(),
            inputEmail: $("#inputEmail").val(),
            inputPhone: $("#inputPhone").val(),
            inputMessage: $("#inputMessage").val(),
            responseCode: grecaptcha.getResponse()
        };

        // Send mail and manage return
        $.post('process/mailer.php', formData, function(data) {
            if (!data.success) {
                if (data.errors.envoi) {
                    form.prepend('<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">\n' +
                        '  <i class="bi bi-exclamation-triangle-fill"></i>\n' +
                        '  <div>\n&nbsp;' +
                            data.errors.envoi +
                        '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                        '  </div>\n' +
                        '</div>');
                }
                $.each(data.errors, function(input, error) {
                    $('#' + input).addClass('is-invalid');
                    $('#' + input).after('<div class="invalid-feedback">'+error+'</div>')
                });
            } else {
                form.prepend('<div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">\n' +
                    '  <i class="bi bi-check-circle-fill"></i>\n' +
                    '  <div>\n&nbsp;' +
                    'Message envoyé !' +
                    '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                    '  </div>\n' +
                    '</div>');
                $("#inputFullname").val('');
                $("#inputEmail").val('');
                $("#inputPhone").val('');
                $("#inputMessage").val('');
                grecaptcha.reset();
            }
        }, 'json').fail(function(data) {
            form.prepend('<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">\n' +
                '  <i class="bi bi-exclamation-triangle-fill"></i>\n' +
                '  <div>\n&nbsp;' +
                data.errors.envoi +
                '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                '  </div>\n' +
                '</div>');
            grecaptcha.reset();
        });

        event.preventDefault();
    });
});
