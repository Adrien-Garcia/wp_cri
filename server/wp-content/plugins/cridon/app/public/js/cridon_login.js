/**
 * @description Script for ajax login action
 * @author Etech - Joelio
 * @version 1.0
 */
(function($) {
    'use strict';

    // document ready
    $(function() {
        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'token';
        nonce.id    = 'token';

        criLoginAction(nonce);

        criLostPwd(nonce);
    });

    /**
     * @name criLoginAction
     * @description Action for user authentication
     * @param nonce
     * @author Etech - Joelio
     */
    function criLoginAction(nonce) {
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
    }

    function criLostPwd(nonce) {
        nonce.value = jsvar.lostpwd_nonce;

        // get default var of login form parameters
        // @see hook.inc.php
        var lostPwdFormId = jsvar.pwdform_id,
            msgBlocId = jsvar.pwdmsg_block,
            emailFieldId = jsvar.email_field_id,
            crpcenFieldId = jsvar.crpcen_field_id;

        // override login form params from template if specified
        if (typeof lostPwdFormIdOverride !== 'undefined') {
            lostPwdFormId = lostPwdFormIdOverride;
        }
        if (typeof msgBlocIdOverride !== 'undefined') {
            msgBlocId = msgBlocIdOverride;
        }
        if (typeof emailFieldIdOverride !== 'undefined') {
            emailFieldId = emailFieldIdOverride;
        }
        if (typeof crpcenFieldIdOverride !== 'undefined') {
            crpcenFieldId = crpcenFieldIdOverride;
        }

        $('#' + lostPwdFormId).append(nonce);
        $('#' + lostPwdFormId).submit(function () {
            $('#' + msgBlocId).html('');
            if ($('#' + emailFieldId).val() != '' && $('#' + crpcenFieldId).val() != '') {
                jQuery.ajax({
                    type: 'POST',
                    url: jsvar.ajaxurl,
                    data: {
                        action: 'lost_password',
                        email: $('#' + emailFieldId).val(),
                        token: $('#token').val(),
                        crpcen: $('#' + crpcenFieldId).val()
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        if(data == 'success')
                        {
                            $('#' + msgBlocId).html(jsvar.crpcen_success_msg);
                        }
                        else
                        {
                            $('#' + msgBlocId).html(jsvar.crpcen_error_msg);;
                        }
                        return false;
                    }
                });
            } else {
                //alert(jsvar.empty_error_msg);
                $('#' + msgBlocId).html(jsvar.empty_crpcen_msg);
            }

            return false;
        });
    }
})(jQuery);