<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine Open API Extension
 *
 * @package			Open API
 * @category		Extension
 * @description		Open API
 * @author			Ben Croker
 * @link			http://www.putyourlightson.net/open-api/	
 */
 
 
// get config
require_once PATH_THIRD.'open_api/config'.EXT;


class Open_api_ext
{
	var $name			= OPEN_API_NAME;
	var $version		= OPEN_API_VERSION;
	var $description	= OPEN_API_DESCRIPTION;
	var $settings_exist	= OPEN_API_SETTINGS_EXIST;
	var $docs_url		= OPEN_API_URL;
	
	var $settings		= array();
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	function __construct($settings = '')
	{
		$this->EE =& get_instance();
		
		$this->settings = $settings;
	} 
	
	// --------------------------------------------------------------------
	
	/**
	 * Route URL
	 */
	function route_url($sess)
	{	
		if (isset($this->settings['api_trigger']) AND $this->settings['api_trigger'] AND $this->EE->uri->segment(1) == $this->settings['api_trigger'])
		{
			// load library
			$this->EE->load->library('open_api_lib');

			// call the method in the second segment
			$this->EE->open_api_lib->call_method($this->EE->uri->segment(2));

			// stop any further processing
			die();
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Settings
	 */
	function settings()
	{	
		$settings = array();

	    $settings['api_trigger'] = array('i', '', '');
	
		return $settings;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Update Extension
	 */
	function update_extension($current='')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
		
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->update(
					'extensions',
					array('version' => $this->version)
		);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 */
	function activate_extension()
	{
		// add sessions start hook
		$data = array(
			'class'	 	=> __CLASS__,
			'method'	=> 'route_url',
			'hook'	  	=> 'sessions_start',
			'settings'  => '',
			'priority'  => 10,
			'version'   => $this->version,
			'enabled'   => 'y'
		);	
		$this->EE->db->insert('extensions', $data);		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Disable Extension
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}
	
}
// END CLASS

/* End of file ext.open_api.php */
/* Location: ./system/expressionengine/third_party/open_api/ext.open_api.php */