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
		$this->milestone = null; //iniatilize this for later use
		
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
					Event::add('ushahidi_action.report_form_admin', array($this, '_incident_edit_upload_file'));					
					// hook in to get the data in the the form
					Event::add('ushahidi_action.report_submit_admin', array($this, '_get_post_data'));
					Event::add('ushahidi_action.report_submit_members', array($this, '_get_post_data'));
					// Hook into the report_edit (post_SAVE) event
					Event::add('ushahidi_action.report_edit', array($this, '_incident_save_upload_file'));
					Event::add('incidenttimeline_action.display_timeline_object', array($this, '_grab_milestone'));
					Event::add('incidenttimeline_action.display_timeline_event', array($this, '_alter_timeline'));
					break;
				
				// Hook into the Report view (front end)
				case 'view':
					Event::add('report_view.upload_files_display', array($this, '_incident_view'));
					Event::add('incidenttimeline_action.display_timeline_object', array($this, '_grab_milestone'));
					Event::add('incidenttimeline_action.display_timeline_event', array($this, '_alter_timeline'));
					Event::add('usermsg.display_message_submit', array($this, '_usermsg_send'));
					break;
				
				//Hook into frontend Submit View
				case 'submit':
					//Hook into the form on the frontend
					Event::add('ushahidi_action.file_upload', array($this, '_incident_submit_upload_file'));
					Event::add('ushahidi_action.report_submit', array($this, '_get_post_data'));
					Event::add('ushahidi_action.report_add', array($this, '_incident_save_upload_file'));
					break;
				
					
				default:
					break;
			}//end of switch
		}//end of if reports
		
		//for the user message plugin
		if(Router::$controller == 'usermsg')
		{
			switch(Router::$method)
			{
				case 'send_msg_report':
					Event::add('usermsg.process_incoming_msg', array($this, '_usermsg_data'));
					break;
				case 'getmsg':
					Event::add('usermsg.display_msg', array($this, '_usermsg_view'));
					Event::add('usermsg.display_message_submit', array($this, '_usermsg_send'));
					break;
				case 'send_reply':
					Event::add('usermsg.process_incoming_msg', array($this, '_usermsg_data'));
					break;
				case 'inbox':
					Event::add('usermsg.delete', array($this, '_usermsg_delete'));
					break;
			}
		}
		
		if (Router::$controller == 'incidenttimeline')
		{
			switch (Router::$method)
			{
				// Hook into the Report Add/Edit Form in Admin
				case 'edit':
					Event::add('incidenttimeline_action.edit_milestone_form', array($this, '_milestone_edit_upload_file'));
					Event::add('incidenttimeline_action.timeline_edit_post', array($this, '_get_post_data'));
					Event::add('incidenttimeline_action.timeline_edit', array($this, '_milestone_save_upload_file'));
					break;
					
				case 'view':
					Event::add('incidenttimeline_action.view_milestone_form', array($this, '_milestone_view'));
					
			}
		}//end of incidenttimeline
		

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
	
	/* Grabs the current milestone object for use in inserting files*/
	public function _grab_milestone()
	{		
		$this->milestone = Event::$data;
		
	}
	
	/* Used to create html for a milestone*/
	public function _alter_timeline()
	{
		//find all the files associated with this incident
		$files = ORM::factory('fileupload')
			->where('incident_id', $this->milestone->id)
			->where('association_type', 3)
		->find_all();
		if(count($files)>0)
		{
			$prefix = url::base().Kohana::config('upload.relative_directory');
			$event = Event::$data;
			$event['description'] .= "<strong>".Kohana::lang('fileupload.incident_files').':</strong><ul>';
			foreach($files as $file)
			{
				
				$file_name = $file->file_link;
				$event['description'] .= '<li><a href="'.$prefix.'/'.$file_name.'">'.$file->file_title.'</a></li>';
			}
			$event['description'] .= '</ul>';
			Event::$data = $event;
		}
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

		if(count($files) > 0)
		{
			// Load the View		
			$form = View::factory('fileupload/page_fileupload_view');
			$form->files = $files;
			$form->incident = $id; 
			$form->render(TRUE);
		}
	}
	
	
	/**
	 * Renders the files for a milestone
	 */
	public function _milestone_view()
	{
		$id = Event::$data; //get the id of the incident
		//find all the files associated with this incident
		$files = ORM::factory('fileupload')
		->where('incident_id', $id)->where('association_type', 3)
		->find_all();
		
		if(count($files) > 0)
		{
			// Load the View
			$form = View::factory('fileupload/milestone_fileupload_view');
			$form->files = $files;
			$form->incident = $id;
			$form->render(TRUE);
		}
	}
	
	/*Renders the files that are associated with a report*/
	public function _incident_view()
	{
		//$id = Event::$data; //get the id of the incident
		$id = 0;
		if(isset(Router::$arguments[0]))
		{
			$id = Router::$arguments[0];
		}
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
		//$id = Event::$data; //get the id of the incident
		$id = 0;
		if(isset(Router::$arguments[0]))
		{
			$id = Router::$arguments[0];
		}
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
	
	public function  _milestone_edit_upload_file()
	{
		$id = Event::$data; //get the id of the incident
		
		//find all the files associated with this incident
		$files = ORM::factory('fileupload')
			->where('incident_id', $id)
			->where('association_type', 3)
			->find_all();
	
	
		// Load the View
		$form = View::factory('fileupload/incident_fileupload_edit');
		$form->files = $files;
		$form->incident = $id;
		$form->message = Kohana::lang('fileupload.file_will_upload_milestone');
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
	public function _milestone_save_upload_file()
	{
		$post = $this->post_data;
		$incident = Event::$data;
		$id = $incident->id;
	
	
		$this->save_upload_files("milestone", $incident, $post, $id);
	
	
	}//end of method

	
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
		$association_type = 0;
		
		$filenames = null;
		if($type == "report")
		{
			$formIdPrefix = "incident_fileUpload_";
			$numberOfFileFields = $post["fileUpload_id"];
			$association_type = 1;
		}
		if($type == "page")
		{
			$formIdPrefix = "page_fileUpload_";
			$numberOfFileFields = $post["fileUpload_id"];
			$association_type = 2;
		}
		if($type == "milestone")
		{
			$formIdPrefix = "incident_fileUpload_";
			$numberOfFileFields = $post["fileUpload_id"];
			$association_type = 3;
		}
		if($type == "usermsg")
		{
			$formIdPrefix = "usermsg_fileUpload_";
			$numberOfFileFields = $post["fileUpload_id"];
			$association_type = 4;
		}
		//for each file that may or may not have been submitted
		for($i = 0; $i < $numberOfFileFields; $i++)
		{
		
			//check to see if there's a file that corresponds to this ID
			if(array_key_exists($formIdPrefix.$i, $_FILES) && ($_FILES[$formIdPrefix.$i]['size'] > 0))
			{
					
				$filename = upload::save($formIdPrefix.$i);
				//get just the file name
				//remove any harmful characters
				$harmful_characters = array('#', '&', '?', '*', '/');
				$new_filename = str_replace($harmful_characters, '', $_FILES[$formIdPrefix.$i]['name']);
				//make a folder if we need to
				if (!is_dir(Kohana::config('upload.directory', TRUE).$type ."/".$item->id))
				{
					mkdir(Kohana::config('upload.directory', TRUE).$type ."/".$item->id, 0777, true);
				}
				//make sure we have a unique file name
				$prefix = "";
				$counter = 0;
				while(file_exists(Kohana::config('upload.directory', TRUE).$type ."/".$item->id."/". $prefix.$new_filename ))
				{
					$counter++;
					$prefix = $counter.'_';
				}
				copy($filename, Kohana::config('upload.directory', TRUE).$type ."/".$item->id."/". $prefix.$new_filename );

				// Remove the temporary file
				unlink($filename);
				
				
				
				//update the DB
				$fileupload_item = ORM::factory('fileupload');
				$fileupload_item->file_link = $type ."/".$item->id."/". $prefix.$new_filename;
				
				$fileupload_item->file_title = $_FILES[$formIdPrefix.$i]['name'];
				
				/*
				if ( isset($post['fileUpload_description_'.$i]) && !empty($post['fileUpload_description_'.$i]) ) 
				{
					$title =$post['fileUpload_description_'.$i];
					$fileupload_item->file_title = substr($title, 0, 250);
				}
				else
				{
					$fileupload_item->file_title = "NO DESCRIPTION GIVEN";
				}
				*/
				
				//set the time of this file being uploaded
				$fileupload_item->file_date = date('c');
				
				//set the type of item this file is associated with
				if($type == "report" OR $type == "milestone" OR $type=="usermsg")
				{
					$fileupload_item->association_type = $association_type;
					$fileupload_item->incident_id = $item->id;
				}
				elseif($type == "page")
				{
					$fileupload_item->association_type = $association_type;
					$fileupload_item->page_id = $item->id;
				}
				$fileupload_item->save(); 
			}
		}
	}
	
	/**
	 * Used to render the file upload needed to send files
	 * with messages. This needs the usermsg plugin to work.
	 */
	public function _usermsg_send()
	{
		$form = View::factory('fileupload/usermsg_fileupload_submit');
		echo $form;
	}
	
	/**
	 * Used to process incoming file data from a message being sent.
	 */
	public function _usermsg_data()
	{
		$msg = Event::$data;
		$post = $_POST;
		$id = $msg->id;
		$this->save_upload_files("usermsg", $msg, $post, $id);
	}
	
	/**
	 * Used to display the files that are contained in a message
	 */
	public function _usermsg_view()
	{
		$msg = Event::$data;
		//find all the files associated with this incident
		$files = ORM::factory('fileupload')
		->where('incident_id', $msg->id)->where('association_type', 4)
		->find_all();
		
		if(count($files) > 0)
		{
			// Load the View
			echo '<br/><br/>';
			$form = View::factory('fileupload/milestone_fileupload_view');
			$form->files = $files;
			$form->incident = $msg->id;
			$form->render(TRUE);
		}
	}
	
	/**
	 * if a message is deleted, delete the files along with it
	 */
	public function _usermsg_delete()
	{
		$msg = Event::$data;
		//find all the files associated with this incident
		$files = ORM::factory('fileupload')
		->where('incident_id', $msg->id)->where('association_type', 4)
		->find_all();
		
		foreach($files as $file)
		{
			$prefix = Kohana::config('upload.directory', TRUE);
			if(file_exists($prefix.$file->file_link))
			{
				unlink($prefix.$file->file_link);
			}
			$file->delete();
		}
	}
	
	
	
}//end of class

new fileupload;
