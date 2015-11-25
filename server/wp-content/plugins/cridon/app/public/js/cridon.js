/**
 * @description Script for ajax login action
 * @author Etech - Joelio
 * @version 1.0
 */
(function($) {
    'use strict';

    // document ready
    $(function() {
        // login
        criLoginAction();

        // lost pwd
        criLostPwd();

        // post question
        criQuestion();
    });

    /**
     * @name criLoginAction
     * @description Action for user authentication
     * @author Etech - Joelio
     */
    function criLoginAction() {
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
        $('#' + loginFormId).submit(function() {
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

    /**
     * @name criLostPwd
     * @description Action for user lost password
     * @author Etech - Joelio
     */
    function criLostPwd() {
        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'tokenpwd';
        nonce.id    = 'tokenpwd';
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
        $('#' + lostPwdFormId).submit(function() {
            $('#' + msgBlocId).html('');
            if ($('#' + emailFieldId).val() != '' && $('#' + crpcenFieldId).val() != '') {
                jQuery.ajax({
                    type: 'POST',
                    url: jsvar.ajaxurl,
                    data: {
                        action: 'lost_password',
                        email: $('#' + emailFieldId).val(),
                        token: $('#tokenpwd').val(),
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

    /**
     * @name criQuestion
     * @description Action for post question
     * @author Etech - Joelio
     */
    function criQuestion() {
        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'tokenquestion';
        nonce.id    = 'tokenquestion';
        nonce.value = jsvar.question_nonce;

        // get default var of question form parameters
        // @see hook.inc.php
        var questionFormId = jsvar.question_form_id,
            msgBlocId = jsvar.question_msgblock,
            supportFieldId = jsvar.question_support,
            matiereFieldId = jsvar.question_matiere,
            competenceFieldId = jsvar.question_competence,
            objectFieldId = jsvar.question_objet,
            messageFieldId = jsvar.question_message;

        // form data
        var formdata = new FormData(), len = 0,
            inputFile = document.getElementById(jsvar.question_fichier);

        $('#' + questionFormId).append(nonce);
        $('#' + questionFormId).submit(function() {
            if (inputFile) {
                var i = 0, file;
                len = inputFile.files.length;
                for (; i < len; i++) {
                    file = inputFile.files[i];
                    if (formdata && (parseInt(inputFile.files[i].size) <= parseInt(jsvar.question_max_file_size))) {
                        formdata.append(jsvar.question_fichier + '[]', file);
                    }
                }
            }

            formdata.append("action", 'add_question');
            formdata.append(supportFieldId, $('#' + supportFieldId).val());
            formdata.append(matiereFieldId, $('#' + matiereFieldId).val());
            formdata.append(competenceFieldId, $('#' + competenceFieldId).val());
            formdata.append(objectFieldId, $('#' + objectFieldId).val());
            formdata.append(messageFieldId, $('#' + messageFieldId).val());

            $('#' + msgBlocId).html('');

            // max nb file
            if (parseInt(len) > parseInt(jsvar.question_nb_file)) {
                $('#' + msgBlocId).html(jsvar.question_nb_file_error);

                // stop action
                return false;
            }

            jQuery.ajax({
                type: 'POST',
                url: jsvar.ajaxurl,
                data: formdata,
                processData: false,
                contentType: false,
                success: function (data) {
                    // init formdata
                    formdata = new FormData();

                    data = JSON.parse(data);
                    // show message response
                    $('#' + msgBlocId).html(data);

                    return false;
                }
            });

            return false;
        });
    }
})(jQuery);