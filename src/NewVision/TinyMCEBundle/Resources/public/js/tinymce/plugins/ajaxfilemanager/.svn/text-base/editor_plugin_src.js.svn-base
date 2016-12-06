/**
 * editor_plugin.js
 *
 * Copyright 2011, Marin Ivanov
 *
 */

(function() {
    var AjaxFileManagerPlugin = {
        setup: function() {
			var t = this, o, d = document;
			// Find script base URL
			function get(nl) {
				var i, n;

				for (i=0; i<nl.length; i++) {
					n = nl[i];

					if (n.src && /ajaxfilemanager\/editor_plugin\.js/g.test(n.src)) {
						return n.src.substring(0, n.src.lastIndexOf('/'));
                    }
				}
			};

			o = d.documentElement;
			if (o && (o = get(o.getElementsByTagName('script'))))
				return t.baseURL = o;

			o = d.getElementsByTagName('script');
			if (o && (o = get(o)))
				return t.baseURL = o;

			o = d.getElementsByTagName('head')[0];
			if (o && (o = get(o.getElementsByTagName('script'))))
				return t.baseURL = o;

        },
        filebrowserCallback: function(field_name, url, type, win) {
            var t = AjaxFileManagerPlugin, ed = tinyMCE.activeEditor;

            var ajaxfilemanagerurl = t.baseURL + "/ajaxfilemanager.php";
            
            switch (type) {
            	case "image":
            		break;
            	case "media":
            		break;
            	case "flash":
            		break;
            	case "file":
            		break;
            	default:
            		return false;
            }
            ed.windowManager.open({
                url: ajaxfilemanagerurl,
                width: 782,
                height: 440,
                inline : "yes",
                close_previous : "no"
            },{
                window : win,
                input : field_name
            });
        },

    };
    AjaxFileManagerPlugin.setup();
    
    
    
    tinymce.create('tinymce.plugins.AjaxFileManagerPlugin', {
        init : function(ed, url) {
            ed.settings.file_browser_callback = AjaxFileManagerPlugin.filebrowserCallback;
        },
        getInfo : function() {
            return {
                longname : 'Ajax File Manager',
                author : 'Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)',
                authorurl : 'http://www.phpletter.com',
                infourl : 'http://www.phpletter.com/Our-Projects/Tinymce-Ajax-File-Manager-Project/',
                version : '1.0 RC4'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('ajaxfilemanager', tinymce.plugins.AjaxFileManagerPlugin);
})();