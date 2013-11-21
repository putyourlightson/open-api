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
		if (!empty($this->settings['api_trigger']) AND $this->EE->uri->segment(1) == $this->settings['api_trigger'])
		{
			// get method from second segment
			$method = $this->EE->uri->segment(2);

			// load library
			$this->EE->load->library('open_api_lib');

			// set the session
			$this->EE->session = $session;
			
			// check permission
			if (!$this->check_permission($method))
			{
				// error response
				$this->EE->open_api_lib->response('You are not permitted to perform this action', 403);
			}

			// call the method in the second segment
			$this->EE->open_api_lib->call_method($method);

			// stop any further processing
			die();
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Check Permission
	 */
	function check_permission($method)
	{
		// check if this is a read method
		if (substr($method, 0, 4) != 'get_')
		{
			return TRUE;
		}


		// get group id
		$group_id = $this->EE->session->userdata['group_id'];

		// if super admin then return
		if ($group_id == 1)
		{
			return TRUE;
		}


		// if method access exists for this method
		if (!empty($this->settings['method_access'][$method]))
		{
			// check access permission for method
			if ($this->_check_access_permission($this->settings['method_access'][$method]))
			{
				// if method is get_channel_entry or get_channel_entries
				if ($method == 'get_channel_entry')// OR $method == 'get_channel_entries')
				{
					// check channel permission
					return $this->check_channel_permission($method);
				}

				return TRUE;
			}
		}
		

		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Check Channel Permission
	 */
	function check_channel_permission($method)
	{
		// get group id
		$group_id = $this->EE->session->userdata['group_id'];

		// if super admin then return
		if ($group_id == 1)
		{
			return TRUE;
		}


		// get channel id
		$channel_id = $this->EE->input->get('channel_id');
		
		// if method is get_channel_entry then get channel id from entry id
		if ($method == 'get_channel_entry')
		{
			// get entry id
			$entry_id = $this->EE->input->get('entry_id');

			// get channel id from open api library
			$channel_id = $this->EE->open_api_lib->get_channel_id($entry_id);
		}

		
		// if channel access exists for this channel
		if (!empty($this->settings['channel_access'][$channel_id]))
		{
			// check access permission for channel
			return $this->_check_access_permission($this->settings['channel_access'][$channel_id]);
		}


		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Check Access Permission
	 */
	private function _check_access_permission($access_details)
	{
		// get group id
		$group_id = $this->EE->session->userdata['group_id'];


		// get access
		$access = isset($access_details) ? $access_details['access'] : '';

		// allow public access
		if ($access == 'public')
		{
			return TRUE;
		}


		// restricted access
		if ($access == 'restricted')
		{
			// check universal api key
			if ($this->settings['universal_api_key'] AND $this->EE->input->get('api_key') == $this->settings['universal_api_key'])
			{
				return TRUE;
			}
			
			// check member group access
			$member_groups = isset($access_details['member_groups']) ? $access_details['member_groups'] : array();

			if ($group_id AND in_array($group_id, $member_groups))
			{
				return TRUE;
			}

			// check api keys
			$api_keys = isset($access_details['api_keys']) ? $access_details['api_keys'] : '';
			$api_keys = $api_keys ? explode("\n", $api_keys) : array();

			if (count($api_keys) AND in_array($this->EE->input->get('api_key'), $api_keys))
			{
				return TRUE;
			}	
		}


		return FALSE;
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

		// general settings
		$vars['settings']['general'] = array(
			'api_trigger' => isset($settings['api_trigger']) ? $settings['api_trigger'] : '',
			'universal_api_key' => isset($settings['universal_api_key']) ? $settings['universal_api_key'] : ''
		);

		// access settings
		$vars['settings']['method_access'] = isset($settings['method_access']) ? $settings['method_access'] : array();
		$vars['settings']['channel_access'] = isset($settings['channel_access']) ? $settings['channel_access'] : array();


		// access options
		$vars['data']['access_options'] = array('private', 'public', 'restricted');


		// get all methods
		$vars['data']['methods'] = array(
			'get_channel',
			'get_channels',
			'get_channel_entry',
			'get_channel_entries',
			'get_category',
			'get_categories',
			'get_categories_by_group',
			'get_categories_by_channel',
			'get_category_group',
			'get_category_groups',
			'get_member',
			'get_members',
			'get_member_group',
			'get_member_groups'
		);


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