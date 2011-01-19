=== About ===
name: File Upload
website: http://apps.ushahidi.com
description: Allows users to upload any file to reports and pages
version: 1.0
requires: 2.0
tested up to: 2.0
author: John Etherton
author website: http://johnetherton.com

== Description ==
Adds the ability to upload any file to the site and associate it with a report or a page. Note that for pages to work the following plugins need to either be added to a skin or customized

The "Insert file into description" link only works if the description box uses TinyMCE, which I strongly recommend you do.

* /application/views/admin/reports_edit.php
    line 107: Event::run('ushahidi_action.report_form_admin', $id);
    
* /applications/controllers/admin/reports.php
    line 862-863:
        $incident_post_id = array("incident" => $incident, "post" => $post, "id" => $id); //note custom data array to include all needed info
        Event::run('ushahidi_action.report_edit', $incident_post_id);
        
* /themes/default/views/reports_view.php
    line 84: Event::run('ushahidi_action.report_extra', $incident_id);
    
* /themes/default/views/reports_submit.php
    line 43: Event::run('ushahidi_action.report_form_submit');
    
* /application/controllers/reports.php
    line 553-557:
        //special data structure to save all the details we'll need
        $incident_post_id = array("incident" => $incident, "post" => $post, "id" => $id);
        // Action::report_add - Added a New Report
        Event::run('ushahidi_action.report_add', $incident_post_id);
        
* /application/views/admin/pages.php
    line 149: Event::run('ushahidi_action.page_form_admin');
    
* /application/controllers/admin/manage.php
    line 427-428:
        $page_post_id = array("page" => $page, "post" => $post, "id" => $page->id);
        Event::run('ushahidi_action.page_edit', $page_post_id);
* /themes/default/views/page.php
    line 8: Event::run('ushahidi_action.page_extra', $incident_id);


== Installation ==
1. Copy the entire /fileupload/ directory into your /plugins/ directory.
2. Activate the plugin.

== Changelog ==