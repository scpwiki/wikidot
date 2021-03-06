

Wikijump.modules.ForumEditPostFormModule = {};

Wikijump.modules.ForumEditPostFormModule.listeners = {
	preview: function(e){
		var p = OZONE.utils.formToArray("edit-post-form");
		OZONE.ajax.requestModule("Forum/ForumPreviewPostModule", p, Wikijump.modules.ForumEditPostFormModule.callbacks.preview);
	},
	cancel: function(e){
		// remove form
		var formDiv = $('edit-post-form-container');
		formDiv.parentNode.removeChild(formDiv);

		Wikijump.Editor.shutDown();
	},
	closePreview: function(e){
		$("edit-post-preview-div").style.display = "none";
	},
	save: function(e){
		var p = OZONE.utils.formToArray("edit-post-form");
		p.action = "ForumAction";
		p.event = "saveEditPost";

		var w = new OZONE.dialogs.WaitBox();
		w.content = "Saving changes...";
		w.show();
		OZONE.ajax.requestModule(null, p, Wikijump.modules.ForumEditPostFormModule.callbacks.save);
	}
}

Wikijump.modules.ForumEditPostFormModule.callbacks = {
	preview: function(r){
		if(!Wikijump.utils.handleError(r)) {return;}

		var previewContainer = document.getElementById("edit-post-preview-div");
		var divNum;
			divNum = 0;
		previewContainer.getElementsByTagName('div').item(divNum).innerHTML=r.body;
		previewContainer.style.visibility="hidden";
		previewContainer.style.display="block";

		// a trick. scroll first FAST and...
		previewContainer.style.visibility="visible";
		OZONE.visuals.scrollTo("edit-post-preview-div");

	},
	save: function(r){
		if(r.status=="form_errors"){
			var inner = "The data you have submitted contains following errors:" +
					"<ul>";

			var errors = r.formErrors;
			for(var i in errors){
				inner += "<li>"+errors[i]+"</li>";
			}
			inner += "</ul>";
			var w = new OZONE.dialogs.ErrorDialog();
			w.content = inner;
			w.show();
			return;
		}
		if(!Wikijump.utils.handleError(r)) {return;}

		var w = new OZONE.dialogs.SuccessBox();
		w.content = "Your changes have been saved.";
		w.show()
		var hash = "post-"+r.postId;
		setTimeout('window.location.hash = "'+hash+'"; window.location.reload()', 1000);

	}
}
