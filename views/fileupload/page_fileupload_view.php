<div  class="content" style="margin-top:15px;">
<hr/>
	<h4 style="margin:2px;">
		Files
		<br/>
		<span style="font-size:10px;">
			Uploaded files associated with this page:
		</span>
	</h4>
	<ul style="padding-left:20px;">
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