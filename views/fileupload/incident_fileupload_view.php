<div  class="content" >

	<h5 style="font-size:13pt;"><?php echo Kohana::lang('fileupload.incident_files');?><br/>
		<span style="font-size:10px;"><?php echo Kohana::lang('fileupload.uploaded');?></span>
	</h5>
	<ul style="margin:15px;">
		<?php
			foreach ($files as $file) 
			{
				print "<li id=\"file_". $file->id ."\"  >";
				$prefix = url::base().Kohana::config('upload.relative_directory');
				$file_name = $file->file_link;
				print '<a href="'.$prefix.'/'.$file_name.'">'.$file->file_title.'</a>';
				print "</li>";
			}
		?>
	</ul>
	

</div>