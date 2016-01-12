/**
 * @description Script Handle for jsvar localize script
 */
(function($) {
    'use strict';

    // document ready
    $(function() {
        //Filter Veille
        $('.js-filter-veille-matiere').on('click',function(){
            var queryParameters = {}, queryString = location.search.substring(1),
                re = /([^&=]+)=([^&]*)/g, m;
            // Creates a map with the query string parameters
            while (m = re.exec(queryString)) {
                queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
            }
            if( queryParameters.matiere ){
                var s = queryParameters.matiere.split(',');                
            }else{
                var s = [];
            }
            if( $(this).prop('checked')){
                var r = s.concat([$(this).val()]); 
                queryParameters.matiere = r.join(',');
                location.search = $.param(queryParameters);
            }else{
                var n = [];
                for(var i = 0;i < s.length;i++ ){
                    if( s[i] !== $(this).val() ){
                        n.push(s[i]);
                    }
                }
                if( n.length > 0 ){
                    queryParameters.matiere = n.join(',');                   
                }else{
                    if(queryParameters.matiere){
                        delete queryParameters.matiere;//unset property
                    }
                }
                location.search = $.param(queryParameters);
            }
        });
        $('.js-filter-veille-matiere-all').on('click',function(e){
            e.preventDefault();
            var queryParameters = {}, queryString = location.search.substring(1),
                re = /([^&=]+)=([^&]*)/g, m;
            // Creates a map with the query string parameters
            while (m = re.exec(queryString)) {
                queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
            }
            var c = [];
            $('.js-filter-veille-matiere').each(function(){
                c.push($(this).val());
            });
            queryParameters.matiere = c.join(',');
            location.search = $.param(queryParameters);
        });
        //End Filter Veille
    });

})(jQuery);