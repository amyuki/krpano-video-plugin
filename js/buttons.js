(function(){
	//Register the buttons
	tinymce.create('tinymce.plugins.krpanovideo',{
		init : function(ed, url){
			ed.addButton('krpanovideo',{
				icon : 'media',
				cmd : 'add_krpanovideo',
				tooltip : 'Add krpanovideo'
			});
			ed.addCommand('add_krpanovideo',function(){
				ed.windowManager.open({
					title: 'Insert krpanovideo',
					file : url+'../../dialog.htm',
					width : 600,
					height : 300,
					inline :1
				});
			});
		},
		createControl : function(n, cm) {
                        return null;
                },
	});
	tinymce.PluginManager.add('krpanovideo',tinymce.plugins.krpanovideo);
})();