<div class="row file_upload" >
    <h4> Upload Documents </h4>
	<script type="text/javascript">

			
		function addFileField(div, field, hidden_id, field_type) {
			var id = document.getElementById(hidden_id).value;
			$("#" + div).append("<div class=\"row link-row second\" id=\"" + field + "_" + id + "\"><a href=\"#\" class=\"rem\"   style=\"float:right;\" onClick='removeFileField(\"#" + field + "_" + id + "\"); return false;'>remove</a><a href=\"#\" class=\"add\" style=\"float:right;\" onClick=\"addFileField('" + div + "','" + field + "','" + hidden_id + "','" + field_type + "'); return false;\">add</a><div class=\"file_upload_description\">Description: <input type=\"text\" name=\"fileUpload_description_"+id+"\" id=\"fileUpload_description_"+id+"\"/></div><input type=\"" + field_type + "\" name=\"" + field + "_" + id + "\" class=\"fileuploadinput " + field_type + " long\" /></div>");
			
			//$("#" + field + "_" + id).effect("highlight", {}, 800);

			id = (id - 1) + 2;
			document.getElementById(hidden_id).value = id;
		}

		function removeFileField(id) {
			var answer = confirm("Are You Sure You Want To Delete This File?");
		    if (answer){
				$(id).remove();
		    }
			else{
				return false;
		    }
		}

		$(document).ready(function() {
			  // register an event hanlder for on change events on the file inputs
			  $('input.fileuploadinput').change(function(){
				  $("#file_upload_status").show('slow').delay(1000).hide('slow');
			  });
			});
	</script>


	
	
	<div id="divFileUpload">
		<div class="row link-row-file">
			<a href="#" class="add" style="float:right;" onClick="addFileField('divFileUpload','incident_fileUpload','fileUpload_id','file'); return false;">
				<?php echo Kohana::lang('fileupload.add');?>
			</a>
			
			<div class="file_upload_description"><?php echo Kohana::lang('fileupload.description');?>:<input type="text" name="fileUpload_description_1" id="fileUpload_description_1" value=""/></div>
			<input type="file" name="incident_fileUpload_1" value=""  style="border:none;width:200px; float:none;"class="fileuploadinput text long" /> 			
			<input type="hidden" name="fileUpload_id" value="2" id="fileUpload_id">
		</div>
		
	</div>


</div>