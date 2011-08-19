
	<ul>
		<?php
			foreach ($files as $file) 
			{
				print "<li style=\"float:none; list-style:none;\" id=\"file_". $file->id ."\"  >";
				$prefix = url::base().Kohana::config('upload.relative_directory');
				$file_name = $file->file_link;
				print '<a href="'.$prefix.'/'.$file_name.'" style="border:none; float:none; background:none; text-decoration:underline;">'.$file->file_title.'</a>';
				print "<div style=\"margin-left:10px;font-size:80%;\">";
				print "<a href=\"#\" style=\"border:none; float:none; background:none; text-decoration:underline;\" onclick=\"addFile('".$prefix."/".$file_name."','".$file->file_title."'); return false;\">Insert file into description</a>";
				print "&nbsp;&nbsp;--&nbsp;&nbsp;<a style=\"color:red;border:none; float:none; background:none; text-decoration:underline;\" href=\"#\" onClick=\"deleteFile('".$file->id."', 'file_".$file->id."'); return false;\" >".Kohana::lang('ui_main.delete')." file</a>";
				print "</div></li>";
			}
		?>
	</ul>
	