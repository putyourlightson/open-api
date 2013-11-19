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
	function route_url($session)
	{	
		if (isset($this->settings['api_trigger']) AND $this->settings['api_trigger'] AND $this->EE->uri->segment(1) == $this->settings['api_trigger'])
		{
			// load library
			$this->EE->load->library('open_api_lib');

			// set the session
			$this->EE->session = $session;
			
			// call the method in the second segment
			$this->EE->open_api_lib->call_method($this->EE->uri->segment(2));

			// stop any further processing
			die();
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Settings Form
	 */
	function settings_form($settings)
	{
		$this->EE->load->helper('form');
		$this->EE->load->library('table');
	
		$this->EE->cp->load_package_js('script');


		$vars = array();

		$vars['settings']['general'] = array(
			'api_trigger' => isset($settings['api_trigger']) ? $settings['api_trigger'] : '',
			'universal_api_key' => isset($settings['universal_api_key']) ? $settings['universal_api_key'] : ''
		);

		$vars['settings']['channel_access'] = isset($settings['channel_access']) ? $settings['channel_access'] : array();


		// access options
		$vars['data']['access_options'] = array('private', 'public', 'restricted');


		// get all channels
		$this->EE->db->order_by('channel_title');
		$query = $this->EE->db->get('channels');
		$vars['data']['channels'] = $query->result();


		// get all member groups except for super admins (they always have access)
		$this->EE->db->where('group_id != 1');
		$this->EE->db->order_by('group_title');
		$query = $this->EE->db->get('member_groups');
		$vars['data']['member_groups'] = $query->result();

	
		return $this->EE->load->view('settings', $vars, TRUE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Save Settings
	 */
	function save_settings()
	{
		if (empty($_POST))
		{
			show_error($this->EE->lang->line('unauthorized_access'));
		}
		
		unset($_POST['submit']);
			
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->update('extensions', array('settings' => serialize($_POST)));
	
		$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('preferences_updated'));
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