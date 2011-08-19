<?php defined('SYSPATH') or die('No direct script access.');
/**
 * File Upload - sets up the hooks
 *
 * @author	   John Etherton
 * @package	   File Upload
 */

class fileupload {
	
	/**
	 * Registers the main event add method
	 */
	 
	 
	public function __construct()
	{
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
		$this->post_data = null; //initialize this for later use
		
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
	
	// Only add the events if we are on that controller
		if (Router::$controller == 'reports')
		{
			switch (Router::$method)
			{
				// Hook into the Report Add/Edit Form in Admin
				case 'edit':

					// Hook into the form itself on the admin side
					//Event::add('ushahidi_action.report_form_admin', array($this, '_incident_edit_upload_file'));
					Event::add('ushahidi_action.report_form_admin_after_time', array($this, '_incident_edit_upload_file'));					
					// hook in to get the data in the the form
					Event::add('ushahidi_action.report_submit_admin', array($this, '_get_post_data'));
					// Hook into the report_edit (post_SAVE) event
					Event::add('ushahidi_action.report_edit', array($this, '_incident_save_upload_file'));
					break;
				
				// Hook into the Report view (front end)
				case 'view':
					Event::add('ushahidi_action.report_extra', array($this, '_incident_view'));
					break;
				
				//Hook into frontend Submit View
				case 'submit':
					//Hook into the form on the frontend
					Event::add('ushahidi_action.report_form', array($this, '_incident_submit_upload_file'));
					Event::add('ushahidi_action.report_submit', array($this, '_get_post_data'));
					Event::add('ushahidi_action.report_add', array($this, '_incident_save_upload_file'));
					break;
				
					
				default:
					break;
			}//end of switch
		}//end of if reports

		if (Router::$controller == 'manage')
		{
			switch (Router::$method)
			{

				case 'pages':
					//hook into the editing of pages
					Event::add('ushahidi_action.page_form_admin', array($this, '_page_edit_upload_file'));
					//hook in and get the post data
					Event::add('ushahidi_action.page_submit', array($this, '_get_post_data'));
					// Hook into the page_edit (post_SAVE) event
					Event::add('ushahidi_action.page_edit', array($this, '_page_save_upload_file'));
					break;
			}
		}//end of if manage
		
		if (Router::$controller == 'page')
		{
			//hook into the frontside displaying of pages
			Event::add('ushahidi_action.page_extra', array($this, '_page_view'));

		}//end of if page
	}
	
	/* Saves the post data for later use*/
	public function _get_post_data()
	{
		$this->post_data = Event::$data;
	}
	
	/*Renders the files that are associated with a page*/
	public function _page_view()
	{
		$id = Router::$arguments[0];
		//find all the files associated with this incident
		$files = ORM::factory('fileupload')
				->where('page_id', $id)->where('association_type', 2)
				->find_all();
				
				
		// Load the View		
		$form = View::factory('fileupload/page_fileupload_view');
		$form->files = $files;
		$form->incident = $id; 
		$form->render(TRUE);
	}
	
	/*Renders the files that are associated with a report*/
	public function _incident_view()
	{
		$id = Event::$data; //get the id of the incident
		//find all the files associated with this incident
		$files = ORM::factory('fileupload')
				->where('incident_id', $id)->where('association_type', 1)
				->find_all();
				
				
		// Load the View		
		$form = View::factory('fileupload/incident_fileupload_view');
		$form->files = $files;
		$form->incident = $id; 
		$form->render(TRUE);
	}
	
	
	/**
	 * Show the web form to edit what files are to be added and deleted
	 */
	public function _incident_edit_upload_file()
	{
		$id = Event::$data; //get the id of the incident
		//find all the files associated with this incident
		$files = ORM::factory('fileupload')
				->where('incident_id', $id)->where('association_type', 1)
				->find_all();
				
				
		// Load the View		
		$form = View::factory('fileupload/incident_fileupload_edit');
		$form->files = $files;
		$form->incident = $id; 
		$form->render(TRUE);
	}//end method
	
	
	/**
	 * Show the web form to edit what files are assoicated with this page
	 */
	public function _page_edit_upload_file()
	{				
				
		// Load the View		
		$form = View::factory('fileupload/page_fileupload_edit');
		$form->render(TRUE);
	}//end method


	/**
	 * Show the web form to edit what files are to be added and deleted
	 */
	public function _incident_submit_upload_file()
	{
				
				
		// Load the View		
		$form = View::factory('fileupload/incident_fileupload_submit');
		$form->render(TRUE);
	}//end method

	
	/**
	* For saving the files to the hard drive and updating the database
	*/
	public function _incident_save_upload_file()
	{
		$post = $this->post_data;
		$incident = Event::$data;
		$id = $incident->id;
		
	
		$this->save_upload_files("report", $incident, $post, $id);

		
	}//end of method
	
	/**
	* For saving the files to the hard drive and updating the database
	*/
	public function _page_save_upload_file()
	{
		$post = $this->post_data;
		$page = Event::$data;
		$id = $page->id;
		
		
		
	
		$this->save_upload_files("page", $page, $post, $id);

		
	}//end of method	
	
	//handles both pages and reports
	function save_upload_files($type, $item, $post, $id)
	{
		if(!isset($post["fileUpload_id"]))
		{
			return;
		}
	
		$formIdPrefix = null;
		$numberOfFileFields = 0;
		
		$filenames = null;
		if($type == "report")
		{
			$formIdPrefix = "incident_fileUpload_";
			$numberOfFileFields = $post["fileUpload_id"];
		}
		if($type == "page")
		{
			$formIdPrefix = "page_fileUpload_";
			$numberOfFileFields = $post["fileUpload_id"];
		}
			
		//for each file that may or may not have been submitted
		for($i = 0; $i < $numberOfFileFields; $i++)
		{
			//check to see if there's a file that corresponds to this ID
			if(array_key_exists($formIdPrefix.$i, $_FILES) && ($_FILES[$formIdPrefix.$i]['size'] > 0))
			{
				$filename = upload::save($formIdPrefix.$i);
				//get just the file name
				$new_filename = $_FILES[$formIdPrefix.$i]['name'];
				//make a folder if we need to
				if (!is_dir(Kohana::config('upload.directory', TRUE).$type ."/".$item->id))
				{
					mkdir(Kohana::config('upload.directory', TRUE).$type ."/".$item->id, 0777, true);
				}
				copy($filename, Kohana::config('upload.directory', TRUE).$type ."/".$item->id."/". $new_filename );

				// Remove the temporary file
				unlink($filename);
				
				//update the DB
				$fileupload_item = ORM::factory('fileupload');
				$fileupload_item->file_link = $type ."/".$item->id."/". $new_filename;
				
				
				if ( isset($post['fileUpload_description_'.$i]) && !empty($post['fileUpload_description_'.$i]) ) 
				{
					$title =$post['fileUpload_description_'.$i];
					$fileupload_item->file_title = substr($title, 0, 250);
				}
				else
				{
					$fileupload_item->file_title = "NO DESCRIPTION GIVEN";
				}
				
				//set the time of this file being uploaded
				$fileupload_item->file_date = time();
				
				//set the type of item this file is associated with
				if($type == "report")
				{
					$fileupload_item->association_type = 1;
					$fileupload_item->incident_id = $item->id;
				}
				elseif($type == "page")
				{
					$fileupload_item->association_type = 2;
					$fileupload_item->page_id = $item->id;
				}
				$fileupload_item->save(); 
			}
		}
	}
}

new fileupload;
