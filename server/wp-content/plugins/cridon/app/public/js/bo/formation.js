/**
 * @description Script Handle for listing formation
 */
(function($) {
    'use strict';

    // document ready
    $(function() {
        // formation option filter
        formationOptionfilter();
    });

    /**
     * @name formationOptionfilter
     * @description Gestion critere affichage formation
     * @author eTech - Joelio
     */
    function formationOptionfilter() {
        if ($('#formationFilter').length > 0) {
            $('#formationFilter').change(function() {
                window.location.href = $('#baseUrl').val() + '&option=' + $(this).val();
            });
        }

        if ($('#custom_post_date').length > 0) {
            $('#custom_post_date').datepicker();
        }
    }

})(jQuery);