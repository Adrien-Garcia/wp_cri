(function() {
    tinymce.create('tinymce.plugins.Columna1', {
        init : function(ed, url) {
            ed.addButton('Columna1', {
                title : '1/1 Columns',
                image :  url.substring(0, url.length - 2) +'img/1-1columns.gif',
                onclick : function() {
                     ed.selection.setContent('&nbsp;<div class="all-columns"><div class="one-half first"><p>Column 1</p></div><div class="one-half"><p>Column 2</p></div></div>&nbsp;');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('Columna1', tinymce.plugins.Columna1);
})();