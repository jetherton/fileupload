<?php defined('SYSPATH') or die('No direct script access.');
/**
 * SMS Automate Administrative Controller
 *
 * @author	   John Etherton
 * @package	   SMS Automate
 */

class Fileupload_Controller extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'settings';

		// If this is not a super-user account, redirect to dashboard
		if(!$this->auth->logged_in('admin') && !$this->auth->logged_in('superadmin'))
		{
			url::redirect('admin/dashboard');
		}
	}
	
	public function index()
	{
		$this->auto_render = FALSE;
		$this->template = "";
		echo "No settings here";
	}//end index method
	
	/**************************
	/* Use this to delete files
	***************************/
	public function delete($id)
	{
		$this->auto_render = FALSE;
		$this->template = "";
		if ( $id )
		{	
			$file = ORM::factory('fileupload', $id);
			$file_link = $file->file_link;

			// Delete Files from Directory
			if (!empty($file_link))
			{
				unlink(Kohana::config('upload.directory', TRUE) . $file_link);
			}
			//check if that's the last thing in this directory?
			$files_left = $this->getDirectoryList(dirname(Kohana::config('upload.directory', TRUE) . $file_link));
			if(count($files_left) == 0)
			{ //delete folder too
				rmdir(dirname(Kohana::config('upload.directory', TRUE) . $file_link));
			}
			
			// Finally Remove from DB
			$file->delete();
			
		}

	}
	
	function getDirectoryList ($directory) 
	{

	    // create an array to hold directory list
	    $results = array();

	    // create a handler for the directory
	    $handler = opendir($directory);

	    // open directory and walk through the filenames
	    while ($file = readdir($handler)) 
	    {

		// if file isn't this directory or its parent, add it to the results
		if ($file != "." && $file != "..") 
		{
			$results[] = $file;
		}

	    }

	    // tidy up: close the handler
	    closedir($handler);

	    // done!
	    return $results;
	
	}
	
	
	/************************
	 * Given a page id this returns a 
	 * list of files
	 */
	public function getfilesforpage($id)
	{
	
		$this->auto_render = FALSE;
		$this->template = "";
		
		//get the files for this page
		$files = ORM::factory('fileupload')
				->where('page_id', $id)->where('association_type', 2)
				->find_all();

		// Load the View		
		$form = View::factory('fileupload/page_get_files_edit');
		$form->files = $files;
		$form->render(TRUE);	
	}
}