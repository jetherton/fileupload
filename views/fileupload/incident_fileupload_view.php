<div class="content">
	<h2 style="font-size:24px;padding-top:5px;border-top:1px dotted #C0C2B8;clear:both;margin-top:10px;color:#000;"><?php echo Kohana::lang('fileupload.incident_files');?></h2>
	<span style="font-style:italic;"><?php echo Kohana::lang('fileupload.uploaded');?></span>
	<ul style="margin:15px;font-size:14px;">
		<?php
			foreach ($files as $file) 
			{
				print "<li id=\"file_". $file->id ."\"  style=\"margin-left: 15px;\">";
				$prefix = url::base().Kohana::config('upload.relative_directory');
				$file_name = $file->file_link;
				print '<a href="'.$prefix.'/'.$file_name.'">'.$file->file_title.'</a>';
				print "</li>";
			}
		?>
	</ul>
</div>