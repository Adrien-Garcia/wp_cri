(function($) {
    'use strict';

    // check if docReady is defined
    //console.log(jQuery);
    $(function() {
        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'token';
        nonce.id    = 'token';
        nonce.value = jsvar.login_nonce;

        $('#loginForm').append(nonce);
        $('#loginForm').submit(function () {
            $('#loginError').html('');
            if ($('#login').val() != '' && $('#password').val() != '') {
                jQuery.ajax({
                    type: 'POST',
                    url: jsvar.ajaxurl,
                    data: {
                        action: 'logins_connect',
                        login: $('#login').val(),
                        token: $('#token').val(),
                        password: $('#password').val()
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        if(data == 'invalidlogin')
                        {
                            $('#loginError').html(jsvar.error_msg);
                        }
                        else
                        {
                            window.location.href = data;
                        }
                        return false;
                    }
                });
            } else {
                alert('Merci de bien remplir votre identifiant et mot de passe !');
            }

            return false;
        });
    });
})(jQuery);