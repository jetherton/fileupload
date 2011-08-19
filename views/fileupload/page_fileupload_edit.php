<div class="tab_form_item2">

	<script type="text/javascript">
		function deleteFile (id, div)
		{
			var answer = confirm("Are You Sure You Want To Delete This File?");
			if (answer){
				$("#" + div).effect("highlight", {}, 800);
				$.get("<?php echo url::base() . 'admin/fileupload/delete/' ?>" + id);
				$("#" + div).remove();
			}
			else{
				return false;
			}
		}
			
		function addFileField(div, field, hidden_id, field_type) {
			var id = document.getElementById(hidden_id).value;
			$("#" + div).append("<div class=\"row link-row second\" id=\"" + field + "_" + id + "\" style=\"width: 420px;\"><a href=\"#\" class=\"add\" style=\"float:right;\" onClick=\"addFileField('" + div + "','" + field + "','" + hidden_id + "','" + field_type + "'); return false;\">add</a><a href=\"#\" class=\"rem\"  style=\"float:right;\" onClick='removeFileField(\"#" + field + "_" + id + "\"); return false;'>remove</a>Description: <input type=\"text\" style=\"width:300px;\" name=\"fileUpload_description_"+id+"\" id=\"fileUpload_description_"+id+"\"/> <br/>File:<input type=\"" + field_type + "\" name=\"" + field + "_"+ id + "\" style=\"float:none; width:300px;\"  class=\"" + field_type + " long\" /></div>");
			
			$("#" + field + "_" + id).effect("highlight", {}, 800);

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
		
		function getFilesForPage(id, div){
			$.get("<?php echo url::base() . 'admin/fileupload/getfilesforpage/' ?>" + id, { },
				function(data){
					$("#fileList").empty();
					$("#fileList").append(data);
				});
		}
				
		function addFile(fileLink, fileTitle){
			tinyMCE.execCommand("mceInsertContent",false,"<a href=\""+fileLink+"\">"+fileTitle+"</a>");
			return false;
		}
	</script>


	<h4><?php echo Kohana::lang('fileupload.uploaded_files');?><br/>
		<span style="font-size:10px;"><?php echo Kohana::lang('fileupload.page');?>
			</span>
	</h4>
	<ul id="fileList">
		<?php /*
			foreach ($files as $file) 
			{
				print "<li id=\"file_". $file->id ."\"  >";
				$prefix = url::base().Kohana::config('upload.relative_directory');
				$file_name = $file->file_link;
				print '<a href="'.$prefix.'/'.$file_name.'">'.$file->file_title.'</a>';
				print "&nbsp;&nbsp;--&nbsp;&nbsp;<a style=\"color:red;\" href=\"#\" onClick=\"deleteFile('".$file->id."', 'file_".$file->id."'); return false;\" >".Kohana::lang('ui_main.delete')." file</a>";
				print "</li>";
			}*/
		?>
	</ul>
	
<p>	
<div id="getFiles"><a href="#" onClick="getFilesForPage(document.getElementById('page_id').value, 'fileList'); return false;"><?php echo Kohana::lang('fileupload.refresh');?></a></div>	
<br/>
</p>

	<h4>
		<?php echo Kohana::lang('fileupload.uploadfiles');?>
		<br/>
		<span style="font-size:10px;"><?php echo Kohana::lang('fileupload.uploadfiles_description');?></span>		
	</h4>
	<div id="divFileUpload">

		<div class="row link-row-file" style="width: 420px;">
			<a href="#" class="add" style="float:right;" onClick="addFileField('divFileUpload','page_fileUpload','fileUpload_id','file'); return false;"><?php echo Kohana::lang('fileupload.add');?>
			</a>
			<?php echo Kohana::lang('fileupload.description');?>: <input type="text" name="fileUpload_description_1" id="fileUpload_description_1" value="" style="width:300px;"/> <br/>
			<?php echo Kohana::lang('fileupload.file');?>: <input type="file" name="page_fileUpload_1" value=""  style="float:none; width:300px;"class="text long" /> 			
			<input type="hidden" name="fileUpload_id" value="2" id="fileUpload_id" class="text long">
		</div>
	</div>


</div>