<?php

echo form_open('C=addons_extensions'.AMP.'M=save_extension_settings'.AMP.'file=open_api');


// general settings table
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    array('data' => lang('preference'), 'style' => 'width: 25%;'),
    lang('setting')
);

foreach ($settings['general'] as $key => $val)
{

	$this->table->add_row('<label>'.lang($key).'</label><br/><em>'.lang($key.'_instructions').'</em>', form_input($key, $val));
}

echo $this->table->generate();


echo '<br/><br/>';


// general channel access table
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    array('data' => lang('channel'), 'style' => 'width: 25%;'),
    lang('access'),
    lang('member_groups'),
    lang('api_keys')
);

// loop through channels
foreach ($data['channels'] as $channel)
{
	$label = '<label>'.$channel->channel_title.'</label>';
	

	// access options
	$form_options = array();

	foreach ($data['access_options'] as $option)
	{
		$form_options[$option] = lang($option);
	}

	// set default access to private
	$access = isset($settings['channel_access'][$channel->channel_id]['access']) ? $settings['channel_access'][$channel->channel_id]['access'] : 'private';

	$access_options = form_dropdown(
		'channel_access['.$channel->channel_id.'][access]',
		$form_options,
		$access,
		'class="access"'
	);


	// member groups
	$member_groups = '';

	foreach ($data['member_groups'] as $member_group)
	{
		$settings['channel_access'][$channel->channel_id]['member_groups'] = (isset($settings['channel_access'][$channel->channel_id]['member_groups']) AND is_array($settings['channel_access'][$channel->channel_id]['member_groups'])) ? $settings['channel_access'][$channel->channel_id]['member_groups'] : array();

		$selected = isset($settings['channel_access'][$channel->channel_id]['member_groups']) ? in_array($member_group->group_id, $settings['channel_access'][$channel->channel_id]['member_groups']) : FALSE;

		$checkbox = form_checkbox(
			'channel_access['.$channel->channel_id.'][member_groups][]',
			$member_group->group_id,
			$selected
		);

		$member_groups .= $checkbox.' '.$member_group->group_title.'<br/>';
	}

	// wrap in div
	$member_groups = '<div class="restricted" '.($access != 'restricted' ? 'style="display: none;"' : '').'>'.$member_groups.'</div>';


	// api keys
	$form_options = array();

	$api_keys = isset($settings['channel_access'][$channel->channel_id]['api_keys']) ? $settings['channel_access'][$channel->channel_id]['api_keys'] : '';
	
	$api_keys = form_textarea(array(
		'name' => 'channel_access['.$channel->channel_id.'][api_keys]',
		'value' => $api_keys,
		'cols' => '30',
		'rows' => '3'
	));

	// wrap in div
	$api_keys = '<div class="restricted" '.($access != 'restricted' ? 'style="display: none;"' : '').'>'.$api_keys.'</div>';


	// add row to table
    $this->table->add_row($label, $access_options, $member_groups, $api_keys);
}

echo $this->table->generate();


echo form_submit('submit', lang('submit'), 'class="submit"');

$this->table->clear();

echo form_close();

?>