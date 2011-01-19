<?php
/**
 * File Upload - Install
 *
 * @author	   John Etherton
 * @package	   File Upload
 */

class Autolocate_Install {

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
		//nothing to do right now
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