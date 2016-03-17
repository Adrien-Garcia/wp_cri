/**
 * @description Script Handle for listing document
 */
(function($) {
    'use strict';

    // document ready
    $(function() {
        // document option filter
        documentOptionfilter();
    });

    /**
     * @name documentOptionfilter
     * @description Gestion affichage type document
     */
    function documentOptionfilter() {
        if ($('#documentFilter').length > 0) {
            $('#documentFilter').change(function() {
                window.location.href = $('#baseUrl').val() + '&option=' + $(this).val();
            });
        }
    }

})(jQuery);