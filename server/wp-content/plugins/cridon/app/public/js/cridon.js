/**
 * @description Script for ajax login action
 * @author Etech - Joelio
 * @version 1.0
 */
(function($) {
    'use strict';

    // document ready
    $(function() {

        // post newsletter
        criNewsletter();
    });


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