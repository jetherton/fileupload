<?php
/**
 * File Upload - Install
 *
 * @author	   John Etherton
 * @package	   File Upload
 */

class Fileupload_Install {

	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db = Database::instance();
	}

	/**
	 * Creates the required database tables for the actionable plugin
	 */
	public function run_install()
	{
		// Create the database tables.
		// Also include table_prefix in name
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.Kohana::config('database.default.table_prefix').'fileupload` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `incident_id` int(11) DEFAULT NULL,
				  `page_id` int(11) DEFAULT NULL,
				  `association_type` tinyint(4) default NULL COMMENT \'1 - reports, 2 - pages\',
				  `file_title` varchar(255) default NULL,
				  `file_link` varchar(511) default NULL,
				  `file_date` datetime default NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

	}

	/**
	 * Deletes the database tables for the actionable module
	 */
	public function uninstall()
	{
		// I worry that someone will upload lots of files, and then uninstall this not realizing that they've just destroyed all the meta
		// data associated with thier files, so right now uninstalling this doesn't do anything.
	}
}