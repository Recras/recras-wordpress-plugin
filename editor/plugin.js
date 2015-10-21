(function() {
    tinymce.create('tinymce.plugins.recras', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         */
        init : function(ed, url) {
            ed.addButton('arrangement', {
                title : 'Arrangement',
                image : url + '/../assets/arrangement.svg',
                onclick : function(){
                    tb_show('Arrangement', 'admin.php?page=form-arrangement');
                }
            });

            ed.addButton('recras-contact', {
                title : 'Contact Form',
                image : url + '/../assets/contact.svg',
                onclick : function(){
                    tb_show('Contact', 'admin.php?page=form-contact');
                }
            });
        },

        getInfo : function() {
            return {
                longname : 'Recras arrangement shortcode',
                author : 'Recras',
                authorurl : 'https://www.recras.nl/',
                infourl : 'https://www.recras.nl/',
                version : "0.14.1"
            };
        }
    });

    tinymce.PluginManager.add('recras', tinymce.plugins.recras);
})();
