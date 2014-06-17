(function() {
    tinymce.create('tinymce.plugins.Columna2', {
        init : function(ed, url) {
            ed.addButton('Columna2', {
                title : '2/1 Columns',
                image :  url.substring(0, url.length - 2) +'img/2-1columns.gif',
                onclick : function() {
                     ed.selection.setContent('&nbsp;<div class="all-columns"><div class="two-third first"><p>Column 1</p></div><div class="one-third"><p>Column 2</p></div></div>&nbsp; ');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('Columna2', tinymce.plugins.Columna2);
})();
