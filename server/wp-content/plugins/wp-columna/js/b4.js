(function() {
    tinymce.create('tinymce.plugins.Columna4', {
        init : function(ed, url) {
            ed.addButton('Columna4', {
                title : '3 Columns',
                image :  url.substring(0, url.length - 2) +'img/3columns.gif',
                onclick : function() {
                     ed.selection.setContent('<div class="all-columns"><div class="one-third first"><p>Column 1</p></div><div class="one-third"><p>Column 2</p></div><div class="one-third last"><p>Column 3</p></div></div>&nbsp;');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('Columna4', tinymce.plugins.Columna4);
})();