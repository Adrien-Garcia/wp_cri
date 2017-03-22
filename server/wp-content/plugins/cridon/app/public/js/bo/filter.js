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
        if ($('#jsContentFilter').length > 0) {
            $('#jsContentFilter').change(function() {
                $(this).closest('form').submit();
            });
        }
    }

})(jQuery);
