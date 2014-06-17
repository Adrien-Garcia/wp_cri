(function() {
    tinymce.create('tinymce.plugins.Columna3', {
        init : function(ed, url) {
            ed.addButton('Columna3', {
                title : '1/2 Columns',
                image :  url.substring(0, url.length - 2) +'img/1-2columns.gif',
                onclick : function() {
                     ed.selection.setContent('&nbsp;<div class="all-columns"><div class="one-third first"><p>Column 1</p></div><div class="two-third"><p>Column 2</p></div></div>&nbsp; ');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('Columna3', tinymce.plugins.Columna3);
})();
