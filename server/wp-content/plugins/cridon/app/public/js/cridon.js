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

        // post newsletter
        criNewsletter();
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
                            $('#' + msgBlocId).html(jsvar.crpcen_error_msg);
                        }
                        return false;
                    }
                });
            } else {
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
        var formdata = new FormData(), len = 0;

        // reset file list
        resetFileList(jsvar.question_fichier);

        $('#' + questionFormId).append(nonce);
        $('#' + questionFormId).submit(function() {
            // check required field
            if ($('*[name="' + objectFieldId + '"]').first().val() == '') {
                $('#' + msgBlocId).html('Merci de bien remplir le champ "Objet de la question"');
                $('*[name="' + objectFieldId + '"]').focus();

                // stop action
                return false;
            }

            len = jQuery('[id^=' + jsvar.question_fichier + '_').length;
            if (len > 0) {
                jQuery('[id^=' + jsvar.question_fichier + '_').each(function () {
                    var file = $(this).get(0).files[0];
                    if (file) {
                        if (formdata && (parseInt(file.size) <= parseInt(jsvar.question_max_file_size))) {
                            formdata.append(jsvar.question_fichier + '[]', file);
                        }
                    }
                });
            }

            formdata.append("action", 'add_question');
            formdata.append(supportFieldId, $('*[name="' + supportFieldId + '"]').first().val() );
            formdata.append(matiereFieldId, $('*[name="' + matiereFieldId + '"]').first().val() );
            formdata.append(competenceFieldId, $('*[name="' + competenceFieldId + '"]').first().val() );
            formdata.append(objectFieldId, $('*[name="' + objectFieldId + '"]').first().val() );
            formdata.append(messageFieldId, $('*[name="' + messageFieldId + '"]').first().val() );

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
    function resetFileList(eltId) {
        if (jQuery('[id^=' + eltId + '_').length > 0) {
            jQuery('[id^=' + eltId + '_').each(function () {
                if ($(this).get(0).files[0]) {
                    $(this).get(0).files[0] = null;
                }
            });
        }
    }

    /**
     * @name criNewsletter
     * @description Action for newsletter
     * @author Etech - Joelio
     */
    function criNewsletter() {
        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'tokennewsletter';
        nonce.id    = 'tokennewsletter';
        nonce.value = jsvar.newsletter_nonce;

        // get default var of newsletter form parameters
        // @see hook.inc.php
        var newsFormId = jsvar.newsletter_form_id,
            newsMsgBlocId = jsvar.newsletter_msgblock_id,
            emailField = jsvar.newsletter_user_email;

        $('#' + newsFormId).append(nonce);
        $('#' + newsFormId).submit(function() {
            $('#' + newsMsgBlocId).html('');
            var email = $('[name=' + emailField + ']').val();
            if (email != '') {
                jQuery.ajax({
                    type: 'POST',
                    url: jsvar.ajaxurl,
                    data: {
                        action: 'newsletter',
                        email: email,
                        token: $('#tokennewsletter').val()
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        if(data == 'success')
                        {
                            $('#' + newsMsgBlocId).html(jsvar.newsletter_success_msg);
                        }
                        else
                        {
                            $('#' + newsMsgBlocId).html(jsvar.newsletter_email_error);
                        }
                        return false;
                    }
                });
            } else {
                $('#' + newsMsgBlocId).html(jsvar.newsletter_empty_error);
            }

            return false;
        });
    }
})(jQuery);