<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine Open API Library
 *
 * @package			Open API
 * @category		Libraries
 * @description		Front-end API with authentication and CRUD functionality
 * @author			Ben Croker
 * @link			http://www.putyourlightson.net/open-api/
 */
 
 
class Open_api_lib
{

	/**
	  *  Constructor
	  */
	function __construct()
	{
		// make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Call Method
	 */
	function call_method($method='')
	{	
		// start output buffer so we can catch any errors
		ob_start();

		// check if method exists
		if (!method_exists('Open_api_lib', $method))
		{
			$this->response('Method does not exist', 400);
		}

		// call the method
		$this->$method();
	}
	
	// --------------------------------------------------------------------
	// Authentication
	// --------------------------------------------------------------------
	
	/**
	 * Authenticate Username
	 */
	function authenticate_username()
	{
		// get variables
		$vars = $this->_get_vars('post', array('username', 'password'));

		// get member id
		$query = $this->EE->db->get_where('members', array('username' => $vars['username']));

		if (!$row = $query->row())
		{
			$this->response('Authentication failed', 401);
		}
		
		$member_id = $row->member_id;


		// authenticate member
		$this->_authenticate_member($member_id, $vars['password']);
	}
	
	/**
	 * Authenticate Email
	 */
	function authenticate_email()
	{
		// get variables
		$vars = $this->_get_vars('post', array('email', 'password'));

		// get member id
		$query = $this->EE->db->get_where('members', array('email' => $vars['email']));

		if (!$row = $query->row())
		{
			$this->response('Authentication failed', 401);
		}
		
		$member_id = $row->member_id;


		// authenticate member
		$this->_authenticate_member($member_id, $vars['password']);
	}
	
	/**
	 * Authenticate Member ID
	 */
	function authenticate_member_id()
	{
		// get variables
		$vars = $this->_get_vars('post', array('member_id', 'password'));

		// validate id
		$this->_validate_id($vars['member_id']);

		// authenticate member
		$this->_authenticate_member($vars['member_id'], $vars['password']);
	}
	
	// --------------------------------------------------------------------
	// Channels
	// --------------------------------------------------------------------
	
	/**
	 * Get Channel
	 */
	function get_channel()
	{
		// get variables
		$vars = $this->_get_vars('get', array('channel_id'));

		// start hook
		$vars = $this->_hook('get_channel_start', $vars);

		// validate id
		$this->_validate_id($vars['channel_id']);

		// load channel data library
		$this->_load_library('channel_data');
		
		$data = $this->EE->channel_data_lib->get_channel($vars['channel_id'])->result();

		// end hook
		$data = $this->_hook('get_channel_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Channels
	 */
	function get_channels()
	{
		// start hook
		$vars = $this->_hook('get_channels_start', array());

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_channels()->result();

		// end hook
		$data = $this->_hook('get_channels_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Create Channel
	 */
	function create_channel()
	{
		// get posted variables
		$vars = $this->_get_vars('post', array('channel_name', 'channel_title'));

		// authenticate session
		$this->_authenticate_session();

		// load and instantiate api library
		$this->_load_library('api', 'channel_structure');

		$data = $this->EE->api_channel_structure->create_channel($vars);

		// check if there was an error
		if ($data == FALSE)
		{
			$this->response($this->EE->api_channel_structure->errors, 400);
		}

		$this->response(array('channel_id' => $data));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update Channel
	 */
	function update_channel()
	{
		// get posted variables
		$vars = $this->_get_vars('post', array('channel_id', 'channel_name', 'channel_title'));
		
		// validate id
		$this->_validate_id($vars['channel_id']);

		// authenticate session
		$this->_authenticate_session();

		// load and instantiate api library
		$this->_load_library('api', 'channel_structure');

		$data = $this->EE->api_channel_structure->modify_channel($vars);

		// check if there was an error
		if ($data == FALSE)
		{
			$this->response($this->EE->api_channel_structure->errors, 400);
		}

		$this->response(array('channel_id' => $data));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete Channel
	 */
	function delete_channel()
	{
		// get posted variables
		$vars = $this->_get_vars('post', array('channel_id'));
		
		// authenticate session
		$this->_authenticate_session();

		// load and instantiate api library
		$this->_load_library('api', 'channel_structure');

		$data = $this->EE->api_channel_structure->delete_channel($vars);

		// check if there was an error
		if ($data == FALSE)
		{
			$this->response($this->EE->api_channel_structure->errors, 400);
		}

		$this->response(array('channel_id' => $vars['channel_id']));
	}

	// --------------------------------------------------------------------
	// Channels Entries
	// --------------------------------------------------------------------
	
	/**
	 * Get Channel Entry
	 */
	function get_channel_entry()
	{
		// get variables
		$vars = $this->_get_vars('get', array('entry_id'));
		
		// start hook
		$vars = $this->_hook('get_channel_entry_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_channel_entry($vars['entry_id'])->result();
		
		// expand file fields
		if (count($data)) 
		{
			$channel_id = $data[0]->channel_id;
			$data = $this->_expand_file_fields($channel_id, $data);
		}

		// end hook
		$data = $this->_hook('get_channel_entry_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Channel Entries
	 */
	function get_channel_entries()
	{
		// get variables and prepopulate with defaults
		$vars = $this->_get_vars('get', array(), array(
			'channel_id' => FALSE,
			'select' => array(),
			'where' => array(),
			'order_by' => 'entry_date',
			'sort' => 'desc',
			'limit' => FALSE,
			'offset' => 0
		));

		// prepare variables for sql
		$vars = $this->_prepare_sql($vars, array('entry_id', 'channel_id'), 'channel_data');

		// start hook
		$vars = $this->_hook('get_channel_entries_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_channel_entries($vars['channel_id'], $vars['select'], $vars['where'], $vars['order_by'], $vars['sort'], $vars['limit'], $vars['offset'])->result();

		// expand file fields
		if (count($data)) 
		{
			$data = $this->_expand_file_fields($vars['channel_id'], $data);
		}

		// end hook
		$data = $this->_hook('get_channel_entries_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Create Channel Entry
	 */
	function create_channel_entry()
	{
		// get posted variables
		$vars = $this->_get_vars('post', array('channel_id', 'title', 'entry_date'));
		
		// authenticate session
		$this->_authenticate_session();

		// check if user has permission
		$this->_check_permission('channel_entry', 'channel_id', $vars['channel_id']);

		// map custom fields
		$vars = $this->_map_custom_fields($vars, $vars['channel_id']);

		// load and instantiate api library
		$this->_load_library('api', 'channel_entries');

		$data = $this->EE->api_channel_entries->submit_new_entry($vars['channel_id'], $vars);

		// check if there was an error
		if ($data == FALSE)
		{
			$this->response($this->EE->api_channel_entries->errors, 400);
		}

		$this->response(array('entry_id' => $this->EE->api_channel_entries->entry_id));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update Channel Entry
	 */
	function update_channel_entry()
	{
		// get posted variables
		$vars = $this->_get_vars('post', array('channel_id', 'entry_id'));

		// authenticate session
		$this->_authenticate_session();
		
		// check if user has permission
		$this->_check_permission('channel_entry', 'entry_id', $vars['entry_id']);

		// map custom fields
		$vars = $this->_map_custom_fields($vars, $vars['channel_id']);

		// load and instantiate api library
		$this->_load_library('api', 'channel_entries');

		$data = $this->EE->api_channel_entries->update_entry($vars['entry_id'], $vars);

		// check if there was an error
		if ($data == FALSE)
		{
			$this->response($this->EE->api_channel_entries->errors, 400);
		}

		$this->response(array('entry_id' => $vars['entry_id']));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete Channel Entry
	 */
	function delete_channel_entry()
	{
		// get posted variables
		$vars = $this->_get_vars('post', array('entry_id'));

		// authenticate session
		$this->_authenticate_session();

		// check if user has permission
		$this->_check_permission('channel_entry', 'entry_id', $vars['entry_id']);
		
		// load and instantiate api library
		$this->_load_library('api', 'channel_entries');

		$data = $this->EE->api_channel_entries->delete_entry($vars['entry_id']);

		// check if there was an error
		if ($data == FALSE)
		{
			$this->response($this->EE->api_channel_entries->errors, 400);
		}

		$this->response(array('entry_id' => $vars['entry_id']));
	}
	
	// --------------------------------------------------------------------
	// Categories
	// --------------------------------------------------------------------
	
	/**
	 * Get Category
	 */
	function get_category()
	{
		// get variables
		$vars = $this->_get_vars('get', array('cat_id'));
		
		// start hook
		$vars = $this->_hook('get_category_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_category($vars['cat_id'])->result();

		// end hook
		$data = $this->_hook('get_category_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Categories
	 */
	function get_categories()
	{
		$vars = $this->_get_vars('get', array(), array(
			'select' => array(),
			'where' => array(),
			'order_by' => 'cat_order',
			'sort' => 'asc',
			'limit' => FALSE,
			'offset' => 0
		));

		// prepare variables for sql
		$vars = $this->_prepare_sql($vars);

		// start hook
		$vars = $this->_hook('get_categories_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_categories($vars['select'], $vars['where'], $vars['order_by'], $vars['sort'], $vars['limit'], $vars['offset'])->result();

		// end hook
		$data = $this->_hook('get_categories_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Categories by Group
	 */
	function get_categories_by_group()
	{
		// get variables
		$vars = $this->_get_vars('get', array('group_id'));

		// start hook
		$vars = $this->_hook('get_categories_by_group_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_category_by_group($vars['group_id'])->result();

		// end hook
		$data = $this->_hook('get_categories_by_group_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Categories by Channel
	 */
	function get_categories_by_channel()
	{
		// get variables
		$vars = $this->_get_vars('get', array('channel_id'), array(
			'select' => array(),
			'where' => array(),
			'order_by' => 'cat_order',
			'sort' => 'asc',
			'limit' => FALSE,
			'offset' => 0
		));

		// prepare variables for sql
		$vars = $this->_prepare_sql($vars);
		
		// start hook
		$vars = $this->_hook('get_categories_by_channel_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_channel_categories($vars['channel_id'], $vars['select'], $vars['where'], $vars['order_by'], $vars['sort'], $vars['limit'], $vars['offset'])->result();

		// end hook
		$data = $this->_hook('get_categories_by_channel_end', $data);

		$this->response($data);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Create Category
	 */
	function create_category()
	{
		// get variables
		$vars = $this->_get_vars('post', array('group_id', 'cat_url_title', 'cat_name'));
		
		// validate category url title
		$this->EE->load->library('form_validation');
		$word_separator = $this->EE->config->item('word_separator');
		$vars['cat_url_title'] = url_title($vars['cat_url_title'], $word_separator);


		// load category model
		$this->EE->load->model('category_model');

		// check for duplicate category url title
		if ($this->EE->category_model->is_duplicate_category_name($vars['cat_url_title'], '', $vars['group_id']))
		{
			$this->response('Duplicate category name', 400);
		}


		// insert into categories table
		$insert = $this->EE->db->insert('categories', $vars);

		if (!$insert)
		{
			$this->response('Error creating category', 400);
		}

		$this->response(array('cat_id' => $this->EE->db->insert_id()));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update Category
	 */
	function update_category()
	{
		// get variables
		$vars = $this->_get_vars('post', array('cat_id'));
		
		// validate category url title
		if (isset($vars['cat_url_title']))
		{
			$this->EE->load->library('form_validation');
			$word_separator = $this->EE->config->item('word_separator');
			//$vars['cat_url_title'] = url_title($vars['cat_url_title'], $word_separator);
		}


		// update in categories table
		$this->EE->db->where('cat_id', $vars['cat_id']);
		$update = $this->EE->db->update('categories', $vars);

		if (!$update)
		{
			$this->response('Error updating category', 400);
		}

		$this->response(array('cat_id' => $vars['cat_id']));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete Category
	 */
	function delete_category()
	{
		// get variables
		$vars = $this->_get_vars('post', array('cat_id'));

		// validate id
		$this->_validate_id($vars['cat_id']);

		// load category model
		$this->EE->load->model('category_model');

		// delete category
		$this->EE->category_model->delete_category($vars['cat_id']);

		$this->response(array('cat_id' => $vars['cat_id']));
	}
	
	// --------------------------------------------------------------------
	// Category Groups
	// --------------------------------------------------------------------
	
	/**
	 * Get Category Group
	 */
	function get_category_group()
	{
		// get variables
		$vars = $this->_get_vars('get', array('group_id'));
		
		// start hook
		$vars = $this->_hook('get_category_group_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_category_group($vars['group_id'])->result();

		// end hook
		$data = $this->_hook('get_category_group_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Category Groups
	 */
	function get_category_groups()
	{	
		$vars = $this->_get_vars('get', array(), array(
			'select' => array(),
			'where' => array(),
			'order_by' => 'group_id',
			'sort' => 'asc',
			'limit' => FALSE,
			'offset' => 0
		));

		// prepare variables for sql
		$vars = $this->_prepare_sql($vars);

		// start hook
		$vars = $this->_hook('get_category_groups_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_category_groups($vars['select'], $vars['where'], $vars['order_by'], $vars['sort'], $vars['limit'], $vars['offset'])->result();

		// end hook
		$data = $this->_hook('get_category_groups_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Create Category Group
	 */
	function create_category_group()
	{
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete Category Group
	 */
	function delete_category_group()
	{
		// get variables
		$vars = $this->_get_vars('post', array('group_id'));

		// validate id
		$this->_validate_id($vars['group_id']);

		// load category model
		$this->EE->load->model('category_model');

		// delete category group
		$this->EE->category_model->delete_category_group($vars['group_id']);

		$this->response(array('group_id' => $vars['group_id']));
	}
	
	// --------------------------------------------------------------------
	// Members
	// --------------------------------------------------------------------
	
	/**
	 * Get Member
	 */
	function get_member()
	{
		// get variables
		$vars = $this->_get_vars('get', array('member_id'));
		
		// start hook
		$vars = $this->_hook('get_member_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_member($vars['member_id'])->result();

		// end hook
		$data = $this->_hook('get_member_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Members
	 */
	function get_members()
	{
		$vars = $this->_get_vars('get', array(), array(
			'select' => array('member_id', 'group_id', 'email', 'username', 'screen_name'),
			'where' => array(),
			'order_by' => 'member_id',
			'sort' => 'asc',
			'limit' => FALSE,
			'offset' => 0
		));

		// prepare variables for sql
		$vars = $this->_prepare_sql($vars, array('member_id'), 'members');

		// start hook
		$vars = $this->_hook('get_members_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_members($vars['select'], $vars['where'], $vars['order_by'], $vars['sort'], $vars['limit'], $vars['offset'])->result();

		// end hook
		$data = $this->_hook('get_members_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	// Member Groups
	// --------------------------------------------------------------------
	
	/**
	 * Get Member Group
	 */
	function get_member_group()
	{
		// get variables
		$vars = $this->_get_vars('get', array('group_id'), array(
			'select' => array()
		));
		
		// prepare variables for sql
		$vars = $this->_prepare_sql($vars);

		// start hook
		$vars = $this->_hook('get_member_group_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_member_group($vars['group_id'], $vars['select'])->result();

		// end hook
		$data = $this->_hook('get_member_group_end', $data);

		$this->response($data);
	}
	
	/**
	 * Get Member Groups
	 */
	function get_member_groups()
	{
		$vars = $this->_get_vars('get', array(), array(
			'select' => array(),
			'where' => array(),
			'order_by' => 'group_id',
			'sort' => 'asc',
			'limit' => FALSE,
			'offset' => 0
		));

		// prepare variables for sql
		$vars = $this->_prepare_sql($vars);

		// start hook
		$vars = $this->_hook('get_member_groups_start', $vars);

		// load channel data library
		$this->_load_library('channel_data');

		$data = $this->EE->channel_data_lib->get_member_groups($vars['select'], $vars['where'], $vars['order_by'], $vars['sort'], $vars['limit'], $vars['offset'])->result();

		// end hook
		$data = $this->_hook('get_member_groups_end', $data);

		$this->response($data);
	}
	
	// --------------------------------------------------------------------
	// Public Helper Methods
	// --------------------------------------------------------------------
	
	/**
	 * Response
	 */
	function response($data='', $response_code=200)
	{
		// clear output buffer 
		ob_clean();

		// json encode response
		$response = json_encode($data);

		// if is an error response
		if ($response_code >= 400)
		{
			$response = is_array($data) ? implode(', ', $data) : $data;
		}

		else
		{
			header('Content-Type: application/json');
		}

		// set the response code in the header
		$this->EE->output->set_status_header($response_code);

		// output the response and end the script
		echo $response;

		// stop any further processing
		die();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Channel Id of Entry
	 */
	function get_channel_id($entry_id)
	{	
		$this->EE->db->select('channel_id');
		$this->EE->db->where('entry_id', $entry_id);
		$query = $this->EE->db->get('channel_titles');
		$row = $query->row();
		
		return ($row ? $row->channel_id : '');
	}

	// --------------------------------------------------------------------
	// Private Helper Methods
	// --------------------------------------------------------------------
	
	/**
	 * Load Library
	 */
	private function _load_library($library='', $instantiate='')
	{	
		if ($library == 'channel_data')
		{
			// load channel data library
			$this->EE->load->library('channel_data_lib');
		}

		else if ($library == 'api')
		{
			// load native EE API library
			$this->EE->load->library('api');

			// instantiate API
			$this->EE->api->instantiate($instantiate);

			// load stats library
			$this->EE->load->library('stats');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Validate ID
	 */
	private function _validate_id($id)
	{
		if (!is_numeric($id))
		{
			$this->response('Invalid ID', 400);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Authenticate Member
	 */
	private function _authenticate_member($member_id, $password)
	{
		// start hook
		$member_id = $this->_hook('authenticate_member_start', $member_id);

		// load auth library
		$this->EE->load->library('auth');
		
		// authenticate member id
		$userdata = $this->EE->auth->authenticate_id($member_id, $password);

		if (!$userdata)
		{
			$this->response('Authentication failed', 401);
		}

		// success hook
		$this->_hook('authenticate_member_success', $member_id);

		// create a new session id
		$session_id = $this->EE->session->create_new_session($member_id);

		// get member details
		$query = $this->EE->db->get_where('members', array('member_id' => $member_id));
		$member = $query->row();
		
		$this->response(array(
			'session_id' => $session_id, 
			'member_id' => $member_id, 
			'username' => $member->username,
			'screen_name' => $member->screen_name
		));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Authenticate Session
	 */
	private function _authenticate_session()
	{
		// get session id from post or cookie variable
		$session_id = $this->EE->input->post('session_id') ? $this->EE->input->post('session_id') : $this->EE->input->cookie('sessionid');

		// check for session id
		if (!$session_id)
		{
			$this->response('Authentication required', 401);
		}
		

		// check if session id exists in database and get member id
		$this->EE->db->select('member_id');
		$this->EE->db->where('session_id', $session_id);
		$query = $this->EE->db->get('sessions');

		if (!$row = $query->row())
		{
			$this->response('Authentication required', 401);
		}
		
		$member_id = $row->member_id;


		// get group id
		$this->EE->db->select('group_id');
		$this->EE->db->where('member_id', $member_id);
		$query = $this->EE->db->get('members');
		
		$group_id = $query->row()->group_id;


		// get assigned channels
		$assigned_channels = array();

		if ($group_id == 1)
		{
			$this->EE->db->select('channel_id');
			$query = $this->EE->db->get('channels');
		}

		else
		{
			$this->EE->db->select('channel_id');
			$this->EE->db->where('group_id', $group_id);
			$query = $this->EE->db->get('channel_member_groups');
		}
		
		foreach ($query->result() as $row)
		{
			$assigned_channels[] = $row->channel_id;
		}


		// create userdata
		$userdata = array(
			'member_id' => $member_id,
			'assigned_channels' => $assigned_channels
		);

		// get member group data
		$this->EE->db->where('group_id', $group_id);
		$query = $this->EE->db->get('member_groups');

		// append member group data to userdata
		$userdata = array_merge($userdata, $query->row_array());

		// set session userdata
		$this->EE->session->userdata = $userdata;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Check Permission
	 */
	private function _check_permission($data_type, $var_name, $value)
	{
		$group_id = $this->EE->session->userdata['group_id'];

		// if super admin then return
		if ($group_id == 1)
		{
			return;
		}

		if ($data_type == 'channel_entry')
		{
			// get assigned channels
			$assigned_channels = $this->EE->session->userdata['assigned_channels'];

			// get channel id
			$channel_id = $value;
			
			// if entry id was passed as the variable
			if ($var_name == 'entry_id')
			{
				// get channel id
				$channel_id = $this->get_channel_id($value);
			}

			// check assigned channels
			if (!in_array($channel_id, $assigned_channels))
			{
				$this->response('You are not permitted to perform this action', 403);
			}
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get Variables
	 */
	private function _get_vars($method, $required=array(), $defaults=array())
	{	
		$vars = ($method == 'post') ? $_POST : $_GET;

		// populate the variables
		foreach ($vars as $key => $val) 
		{
			$vars[$key] = ($method == 'post') ? $this->EE->input->post($key) : $this->EE->input->get($key);
		}


		$missing = array();

		// check if any required variables are not set or blank
		foreach ($required as $key) 
		{
			if (!isset($vars[$key]) OR $vars[$key] == '')
			{
				$missing[] = $key;
			}
		}

		if (count($missing))
		{
			$this->response('Required variables missing: '.implode(', ', $missing), 400);
		}


		// populate fields with defaults if not set
		foreach ($defaults as $key => $val) 
		{
			if (!isset($vars[$key]))
			{
				$vars[$key] = $val;
			}
		}


		return $vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Expand file fields
	 */
	private function _expand_file_fields($channel_id, $data)
	{
		// load file field library
		$this->EE->load->library('file_field');

		// get fields for this channel
		$fields = $this->EE->channel_data_lib->get_channel_fields($channel_id)->result();

		// loop through fields
		foreach ($fields as $field_data) 
		{
			// if this is a file or safecracker_file field
			if ($field_data->field_type == 'file' OR $field_data->field_type == 'safecracker_file') 
			{
				$field_name = $field_data->field_name;

				// expand field contents
				foreach ($data as $key => $value) 
				{
					$data[$key]->$field_name = $this->EE->file_field->parse_string($data[$key]->$field_name);
				}
			}
		}
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Map Custom Fields
	 */
	private function _map_custom_fields($vars, $channel_id)
	{	
		// load channel data library
		$this->_load_library('channel_data');

		// get channel fields
		$fields = $this->EE->channel_data_lib->get_channel_fields($channel_id)->result();

		// map custom fields
		foreach ($fields as $field) 
		{
			if (isset($vars[$field->field_name]))
			{
				$vars['field_id_'.$field->field_id] = $vars[$field->field_name];
				unset($vars[$field->field_name]);
			}
		}
		
		return $vars;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Prepare for SQL
	 */
	private function _prepare_sql($vars, $ambiguous=array(), $table='')
	{	
		$prepped_vars = array('select', 'where');

		// loop through variables
		foreach ($vars as $key => &$var) 
		{
			if (in_array($key, $prepped_vars))
			{
				// convert to array
				$var = is_array($var) ? $var : explode(',', str_replace(' ', '', $var));

				// loop through fields
				foreach ($var as $key => $val) 
				{
					// conditionals to search for
					$conditionals = array('!=', '<=', '>=', '<', '>', '=', 'like', 'LIKE');

					// if this is an associative array
					if (!isset($var[0]))
					{
						// if the key is ambiguous
						if (in_array($key, $ambiguous))
						{
							// prepend table to field
							$var[$table.'.'.$key] = $var[$key];

							// delete ambiguous field
							unset($var[$key]);
						}

						// search for conditionals
						foreach($conditionals as $condition)
						{
							$matches = array();

							// if the value contains a conditional
							if (preg_match('/^(.*?)'.$condition.'(.*?)$/', $val, $matches))
							{
								// create an associated key value pair
								$var[$key.' '.$matches[1].$condition] = $matches[2];

								// unset the key
								unset($var[$key]);

								break;
							}
						}
					}

					else
					{
						// if the value is ambiguous
						if (in_array($val, $ambiguous))
						{
							// prepend table to value
							$var[$key] = $table.'.'.$val;
						}

						// search for conditionals
						foreach($conditionals as $condition)
						{
							$matches = array();

							// if the value contains a conditional
							if (preg_match('/^(.*?)'.$condition.'(.*?)$/', $val, $matches))
							{
								// create an associated key value pair
								$var[$matches[1].$condition] = $matches[2];

								// unset the key
								unset($var[$key]);

								break;
							}
						}
					}
				}
			}
		}

		return $vars;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Hook - allows each method to check for relevant hooks
	 */
	private function _hook($hook='', $data=array())
	{
		if ($hook AND $this->EE->extensions->active_hook('open_api_'.$hook) === TRUE)
		{
			$data = $this->EE->extensions->call('open_api_'.$hook, $data);
			if ($this->EE->extensions->end_script === TRUE) return;
		}
		
		return $data;
	}

}
// END CLASS

/* End of file Open_api_lib.php */
/* Location: ./system/expressionengine/third_party/open_api/libraries/Open_api_lib.php */