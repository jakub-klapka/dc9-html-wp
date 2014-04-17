(function() {
	tinymce.create('tinymce.plugins.dc9_shortcodes', {
		init : function(ed, url) {
			ed.addButton('dc9_code', {
				title : 'Zdrojový kód',
				image : url+'/code.png',
				onclick : function() {
					var selected = tinyMCE.activeEditor.selection.getContent();
					if( selected != '' ) {
						ed.execCommand('mceInsertContent', false, '[code title="Nadpis kódu"]<br>' + selected + '<br>[/code]');
					} else {
						var title = prompt('Nadpis kódového bloku?');
						if( title === null ) {
							return;
						}
						ed.execCommand('mceInsertContent', false, '[code title="' + title + '"]<br>Kód....<br>[/code]');
					}
				}
			});
			ed.addButton('dc9_youtube', {
				title : 'Youtube video',
				image : url+'/youtube.png',
				onclick : function() {
					var url = prompt('URL videa?');
					if( url === null ) {
						return;
					}
					if( url == false ) {
						url = 'http://www.youtube.com/watch?v=JoGeKdNxH4U';
					}
					ed.execCommand('mceInsertContent', false, '[youtube url="' + url + '"]');
				}
			});

		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "DC9 Editor Shortcodes",
				author : 'Jakub Klapka',
				authorurl : 'http://www.lumiart.cz',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('dc9_shortcodes', tinymce.plugins.dc9_shortcodes);
})();