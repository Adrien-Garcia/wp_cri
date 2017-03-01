/**
 * @description Script Handle for datepicker
 */
(function($) {
    'use strict';

    // document ready
    $(function() {
        datepicker();
    });

    function datepicker() {
        if ($('.datepicker').length > 0) {
            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        }
    }

})(jQuery);
