var counter = $("#annonce_images .row").length;
		$("#add_image").click(function(){
			//recupere le prototype
			var tmpl = $("#annonce_images").data("prototype");
			// on remplace (g = tous les elements selectionnees et name= console du prototype)
			tmpl = tmpl.replace(/__name__/g, counter++ );
			$("#annonce_images").append(tmpl);

			deleteBlock();
	});
		function deleteBlock() {
			$(".del_image").click(function(){
				//this = objet row
				var id = $(this).data("bloc");
				//console.log(id);	
				$('#'+id).remove();			
			})
		}
		deleteBlock();