$(document).ready(function () {
    $("form#contactForm").submit(function (event) {
        var form = $(this);

        // Reset form control
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Get data
        var formData = {
            inputFullname: $("#inputFullname").val(),
            inputEmail: $("#inputEmail").val(),
            inputPhone: $("#inputPhone").val(),
            inputMessage: $("#inputMessage").val(),
        };

        // Send mail and manage return
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: formData,
            dataType: "json",
            encode: true,
        }).done(function (data) {
            console.log(data);
            if (!data.success) {
                if (data.errors.envoi) {
                    $('#submitForm').after('<div class="alert alert-danger d-flex align-items-center" role="alert">\n' +
                        '  <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>\n' +
                        '  <div>\n' +
                        data.errors.envoi +
                        '  </div>\n' +
                        '</div>')
                }
                $.each(data.errors, function(input, error) {
                    $('#' + input).addClass('is-invalid');
                    $('#' + input).after('<div class="invalid-feedback">'+error+'</div>')
                });
            }
        }).fail(function(data) {
            console.log(data);
        });

        event.preventDefault();
    });
});
