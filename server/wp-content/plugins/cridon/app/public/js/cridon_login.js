/**
 * @description Script for ajax login action
 * @author Etech - Joelio
 * @version 1.0
 */
(function($) {
    'use strict';

    // check if docReady is defined
    $(function() {
        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'token';
        nonce.id    = 'token';
        nonce.value = jsvar.login_nonce;

        // get default var of login form parameters
        // @see hook.inc.php
        var loginFormId = jsvar.form_id,
            errorBlocId = jsvar.error_bloc_id,
            loginFieldId = jsvar.login_field_id,
            passwordFieldId = jsvar.password_field_id;

        // override login form params from template if specified
        if (typeof loginFormIdOverride !== 'undefined') {
            loginFormId = loginFormIdOverride;
        }
        if (typeof errorBlocIdOverride !== 'undefined') {
            errorBlocId = errorBlocIdOverride;
        }
        if (typeof loginFieldIdOverride !== 'undefined') {
            loginFieldId = loginFieldIdOverride;
        }
        if (typeof passwordFieldIdOverride !== 'undefined') {
            passwordFieldId = passwordFieldIdOverride;
        }

        $('#' + loginFormId).append(nonce);
        $('#' + loginFormId).submit(function () {
            $('#' + errorBlocId).html('');
            if ($('#' + loginFieldId).val() != '' && $('#' + passwordFieldId).val() != '') {
                jQuery.ajax({
                    type: 'POST',
                    url: jsvar.ajaxurl,
                    data: {
                        action: 'logins_connect',
                        login: $('#' + loginFieldId).val(),
                        token: $('#token').val(),
                        password: $('#' + passwordFieldId).val()
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        if(data == 'invalidlogin')
                        {
                            $('#' + errorBlocId).html(jsvar.error_msg);
                        }
                        else
                        {
                            window.location.href = data;
                        }
                        return false;
                    }
                });
            } else {
                //alert(jsvar.empty_error_msg);
                $('#' + errorBlocId).html(jsvar.empty_error_msg);
            }

            return false;
        });
    });
})(jQuery);