
(function($){
    var timer;
    var xhr;
    $(document).ready(function(){
        init();
        $("#uiSearch").bind("keyup", function() {
            if ( $(this).val().length < 2 ){
                return false;
            }
            var val = $(this).val();
            if (xhr) { xhr.abort(); } // If there is an existing XHR, abort it.
            clearTimeout(timer); // Clear the timer so we don't end up with dupes.
            timer = setTimeout(function() { // assign timer a new timeout 
                if( $('#loader').length  ){//if loader is already exist
                    $('#loader').remove();
                }
                $('.cri_relationship .relationship_left ul').append('<li id="loader"><div class="cri-loading"></div></li>');
                search( val );
            }, 2000); // 2000ms delay, tweak for faster/slower
        });

        $('.cri_relationship ul li a').live('click',function( e ){
            e.preventDefault();
        }); 
        $('.cri_relationship .relationship_left ul li a').live('click',function( e ){
            $(this).parent().addClass('hide');
            appendLi( $(this) );            
        });
        $('.cri_relationship .relationship_right ul li a').live('click',function( e ){
            restore( $(this ) ); 
        }); 
        $('.cri_relationship .relationship_right').find('.relationship_list').sortable({
            axis			:	'y',
            items			:	'> li',
            forceHelperSize		:	true,
            forcePlaceholderSize	:	true,
            scroll			:	true,
            update			:	function(){

            }
        });
    });
    //Restore unselected
    function restore( c ){
        var id = $(c).attr('id');
        $(c).parent().remove();
        if( !$('.cri_relationship .relationship_left ul li #'+id ).length  ){
            html  = '<li>';
            html += '<a id="ui_a'+ id +'" href="#">';
            html += '<span class="relationship-item-info">document</span>';
            html += '<span class="cri-button-add"></span>';
            html += $(c).contents().eq(2).text()+'</a>';//only text in <a>
            html += '</li>';
            $('.cri_relationship .relationship_left ul' ).append( html );
        }else{
            $('.cri_relationship .relationship_left ul li #'+id ).parent().removeClass('hide');            
        }
    }
    //Move selected in right column
    function appendLi( c ){
        var id = c.attr('id');
        if( $('.cri_relationship .relationship_right ul li #'+id ).length ){
            return;
        }
        var input = '<input type="hidden" name="uiDocument[]" value="'+id+'" />';
        var span = '<span class="cri-button-remove"></span>';
        var clone = c.clone();
        clone.append( input ).append( span );
        var html = '<li><a id="'+id+'" href="#">'+clone.html()+'</a></li>';
        $('.cri_relationship .relationship_right ul').append(html);
    }
    //Search document
    function search( search ){
        var data = {
            action: 'admin_documents_search',
            search: search,
            type  : $('#ui-document-type').val()
        };
        xhr = $.post( ajaxurl , data, function(response){
            clearTimeout(timer);
            appendResult( response );
            $('#loader').remove();
        }, 'json');
    }
    //Append result in <ul>
    function appendResult( data ){
        $('.cri_relationship .relationship_left ul' ).empty();
        for( i = 0 ; i < data.length ; i++ ){
            html  = '<li>';
            html += '<a id="ui_a'+ data[i].id +'" href="#">';
            html += '<span class="relationship-item-info">document</span>';
            html += '<span class="cri-button-add"></span>';
            html += data[i].name+'</a>';
            html += '</li>';
            $('.cri_relationship .relationship_left ul' ).append( html );
        }
        init();
    }
    
    function init(){
        //Get all in right
        $('.cri_relationship .relationship_right ul' ).children( 'li' ).each( function(){
            $(this).find('a').each(function(){
                var id = $(this).attr('id');
                //hide if exit in left
                if( $('.cri_relationship .relationship_left ul li #'+id ).length ){
                    $('.cri_relationship .relationship_left ul li #'+id ).parent().addClass('hide');
                }                
            });
        });
    } 
})(jQuery);

