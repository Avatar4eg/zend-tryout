$(function () {
    main.init();
});

var main = {
    init: function () {
        var $email_form = $('#change-email');
        if ($email_form) {
            formajax.initForm($email_form, function(res) {
                if (res === true) {
                    $('.email-alert-success').show();
                } else {
                    $('.email-alert-error').show();
                }
            });
        }
        var $password_form = $('#change-password');
        if ($password_form) {
            formajax.initForm($password_form, function(res) {
                if (res === true) {
                    $('.password-alert-success').show();
                } else {
                    $('.password-alert-error').show();
                }
            });
        }
        var $data_form = $('#change-data');
        if ($data_form) {
            formajax.initForm($data_form, function(res) {
                if (res === true) {
                    $('.data-alert-success').show();
                } else {
                    $('.data-alert-error').show();
                }
            });
        }
    }
};

var formajax = {
    name: "form",
    minLength: 2,

    initForm: function(form, callback)
    {
        form.on('submit', function(e){
            e.preventDefault();
            $('.alert').hide();

            var $submitBtn = form.find('button[type="submit"]');
            if($submitBtn){
                var formText = $submitBtn.text();
                $submitBtn.html('<i class="glyphicon glyphicon-refresh"></i>')
            }

            form.ajaxSubmit({
                'dataType': 'json',
                success: function (response) {
                    if(response.success == 1){
                        formajax.log('submit success');

                        if(response.redirect){
                            location.href = response.redirect;
                        } else {
                            callback(true);
                        }
                    } else {
                        if($submitBtn){
                            $submitBtn.text(formText);
                        }

                        formajax.log('submit error');
                        if(response.messages){
                            formajax.showMessages(form, response.messages);
                        } else {
                            callback(false);
                        }
                    }
                },
                error: function(a, b, c) {
                    console.log(a, b, c);
                }
            });
        });
        formajax.initCheckSystem(form);

        formajax.log('form inited');
    },

    initCheckSystem: function(form)
    {
        formajax.checkFields(form);
        form.find('[data-required="yes"]').on('input change keyup paste', function(){
            var $elem = $(this);
            if($elem.data('submit') == true && $elem.val()){
                form.trigger('submit');
            } else {
                formajax.removeError($elem);
                formajax.checkFields(form);
            }
        });
    },

    checkFields: function(form)
    {
        var allFieldsOk = true;
        var val;

        form.find('[data-required="yes"]').each(function() {
            val = $.trim($(this).val());
            if (!val) {
                allFieldsOk = false;
            }
            if ((this.tagName === "INPUT" || this.tagName === "TEXTAREA") && val.length < form.minLength) {
                allFieldsOk = false;
            }
        });

        var method = allFieldsOk ? 'enableButton' : 'disableButton';
        formajax[method](form);
    },

    disableButton: function(form)
    {
        form.find('button[type=submit]').attr('disabled','disabled');
    },

    enableButton: function(form)
    {
        form.find('button[type=submit]').removeAttr('disabled');
    },

    showMessages: function(form, data)
    {
        formajax.log('error messages:');
        formajax.log(data);

        formajax.clearMessages(form);

        var input;
        $.each(data, function(field, messages){
            input = form.find('[name='+field+']');
            if(input){
                $.each(messages, function(key, message){
                    formajax.renderError(input, message);
                });
            }
        })
    },

    renderError: function(input, message)
    {
        var error = $('<div class="form-error"><p class="text-danger">'+ message +'</p></div>');
        input.after(error);
        input.closest('.form-group').addClass('has-error');
    },

    removeError: function(input)
    {
        input.siblings('.form-error').fadeOut(300, function(){ $(this).remove() });
        input.closest('.form-group').removeClass('has-error');
    },

    clearMessages: function(form)
    {
        form.find('.form-error').remove();
        form.find('.has-error').removeClass('has-error');
    },

    log: function(message)
    {
        console.log(this.name + ": " + message);
    }
};