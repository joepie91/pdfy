var data_object = null;
var dragging = false;
var drag_timeout = undefined;
var last_coords = [0, 0];

$(function(){
	if($().fileupload)
	{
		/* Only run this if the fileupload plugin is loaded; we don't need all this
		 * on eg. the 'view' page. */
		$("#upload_form").fileupload({
			fileInput: null,
			url: "/upload",
			type: "POST",
			dataType: "text",
			formData: function(form) {
				form = $("#upload_form");
				return form.serializeArray();
			},
			progressall: function(e, data) {
				updateUploadProgress({
					lengthComputable: true,
					loaded: data.loaded,
					total: data.total
				});
			},
			add: function(e, data) {
				data_object = data;
				
				var fileinfo = $(".fileinfo");
				var filesize = data.files[0].size;
				
				if(filesize > (50 * 1024 * 1024))
				{
					alert("You can currently only upload PDFs up to 50MB in size.");
					$("#upload_element").replaceWith($("#upload_element").clone(true));
					return;
				}
				
				if(filesize < 1024 * 1024)
				{
					var filesize_text = Math.ceil(filesize / 1024 * 100) / 100 + " KB";
				}
				else
				{
					var filesize_text = Math.ceil(filesize / 1024 / 1024 * 100) / 100 + " MB";
				}
				
				fileinfo.find(".filename").html(data.files[0].name);
				fileinfo.find(".filesize").html(filesize_text);
				
				$(".info").hide();
				fileinfo.show();
				$(".upload").addClass("faded");
			},
			done: function(e, data) {
				window.location = "/" + data.result;
			},
			autoUpload: false,
			maxNumberOfFiles: 1,
			paramName: "file"
		});
	}
	
	$("#upload_activator").on("click", function(event){
		$("#upload_element").click();
	});
	
	$("#upload_element").on("change", function(event){
		var fileinfo = $(".fileinfo");
		var filesize = this.files[0].size;
		
		if(filesize > (50 * 1024 * 1024))
		{
			alert("You can currently only upload PDFs up to 50MB in size.");
			$("#upload_element").replaceWith($("#upload_element").clone(true));
			return;
		}
		
		if(filesize < 1024 * 1024)
		{
			var filesize_text = Math.ceil(filesize / 1024 * 100) / 100 + " KB";
		}
		else
		{
			var filesize_text = Math.ceil(filesize / 1024 / 1024 * 100) / 100 + " MB";
		}
		
		fileinfo.find(".filename").html(this.files[0].name);
		fileinfo.find(".filesize").html(filesize_text);
		
		$(".info").hide();
		fileinfo.show();
		$(".upload").addClass("faded");
	});
	
	$("#upload_form").on("submit", function(event){
		event.stopPropagation();
		event.preventDefault();
		
		 $(".fileinfo").addClass("faded");
		 $(".progress").show();
		console.log(this);
		var formData = new FormData(this);
		
		if(data_object == null)
		{
			$.ajax({
				url: "/upload",
				method: "POST",
				xhr: function() {
					var customHandler = $.ajaxSettings.xhr();
					
					if(customHandler.upload)
					{
						customHandler.upload.addEventListener("progress", updateUploadProgress, false);
					}
					
					return customHandler;
				},
				success: function(result) {
					window.location = "/" + result;
				},
				data: formData,
				cache: false,
				contentType: false,
				processData: false
			});
		}
		else
		{
			data_object.submit();
		}
	});
	
	$(".autoselect").on("click", function(event){
		$(this).focus();
		$(this).select();
	});
	
	$("body").on("dragstart", function(event){
		event.dataTransfer.setDragImage($("#drag_ghost")[0], 0, 0);
	});
	
	$(".toolbar-settings input").on("change", function(event){
		var new_value = $("input[name=sparse]:checked").val();
		$(".embed_code").val(embed_template.replace("{SPARSE}", new_value));
	});
});

function updateUploadProgress(event)
{
	if(event.lengthComputable)
	{
		var percentage = event.loaded / event.total * 100;
		
		if(event.loaded < 1024 * 1024)
		{
			var done_text = Math.ceil(event.loaded / 1024 * 100) / 100 + " KB";
		}
		else
		{
			var done_text = Math.ceil(event.loaded / 1024 / 1024 * 100) / 100 + " MB";
		}
		
		/* Lazy. */
		if(event.total < 1024 * 1024)
		{
			var total_text = Math.ceil(event.total / 1024 * 100) / 100 + " KB";
		}
		else
		{
			var total_text = Math.ceil(event.total / 1024 / 1024 * 100) / 100 + " MB";
		}
		
		var progress = $(".progress");
		
		progress.find(".done").html(done_text);
		progress.find(".total").html(total_text);
		progress.find(".percentage").html(Math.ceil(percentage * 100) / 100);
		progress.find(".bar-inner").css({width: percentage + "%"});
		
		if(event.loaded >= event.total)
		{
			progress.find(".numbers").hide();
			progress.find(".wait").show();
		}
	}
}
