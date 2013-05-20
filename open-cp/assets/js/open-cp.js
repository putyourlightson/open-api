$(document).ready(function() 
{
	// global vars
	interval = 0;

	// check if not authenticated
	if ($.cookie('exp_sessionid') == undefined) {
		// show login screen
		$('.container').load(
			'_layouts/login.html',
			function(data) {
				// button
				$('form.login').submit(function() {
					authenticate();
					return false;
				});
			}
		);
	}

	else {
		// main	
		$('.container').load(
			'_layouts/main.html',
			function(data) {
				// set username
				$('.username').text($.cookie('exp_username'));

				// logout
				$('.logout').click(function() {
					logout();
				});

				// entries
				$('.entries').click(function() {
					show_entries();
					return false;
				});

				// categories
				$('.nav a.categories').click(function() {
					show_categories();
				});

				// members
				$('.nav a.members').click(function() {
					show_members();
				});

				// nav links
				$('.nav a').click(function() {
					$('.nav li').removeClass('active');
					$(this).parent().addClass('active');
					return false;
				});

				// variations
				$('select.variations').change(function() {
					$('body').addClass($(this).val());
				});
			}
		);
	}
});


function authenticate()
{
	$.post(
		'/index.php/api/authenticate_username',
		$('form.login').serialize(),
		function(data) {
			$.cookie('exp_sessionid', data.session_id, {expires: 1, path: '/'});
			$.cookie('exp_username', data.username, {expires: 1, path: '/'});

			$('.alert-success').fadeIn();
			$('.alert-error').hide();
		},
		'json'
	).fail(
		function(error) {
			$('.alert-error').fadeIn().find('.error_text').text(error.responseText);
			$('.alert-success').hide();
		}
	);
}


function logout()
{
	$.removeCookie('exp_sessionid', {path: '/'});
	$.removeCookie('exp_username', {path: '/'});

	location.reload();
}


function show_entries() 
{
	$('#main').load(
		'_layouts/entries.html',
		function(data) {
			$('.create_entry').click(function() {
				show_entry(0);
				return false;
			});

			$('table.table tr:gt(0)').remove();

			$.get(
				'/index.php/api/get_channel_entries',
				{'channel_id': 1},
				function(data) {
					$.each(data, function(i, entry) {
						$('table.table').append(
							'<tr id="' + entry.entry_id + '"><td>' + entry.entry_id +
							'</td><td><a class="edit" href="#">' + entry.title + '</a>' + 
							'</td><td>' + entry.status +							
							'</td><td class="icons"><a class="delete" title="Delete" href="#"><i class="icon-trash"></i></a></td></tr>'
						);
					});

					$('table.table a.edit').click(function() {
						show_entry($(this).closest('tr').attr('id'));
						return false;
					});

					$('table.table a.duplicate').click(function() {
						duplicate_entry($(this).closest('tr').attr('id'));
						return false;
					});

					$('table.table a.delete').click(function() {
						delete_entry($(this).closest('tr').attr('id'));
						return false;
					});
				},
				'json'
			);
		}
	);
}


function show_entry(id) 
{
	$('#main').load(
		'_layouts/entry.html',
		function(data) {
			$('form .entries, a.cancel').click(function() {
				show_entries();
				return false;
			});

			var date = get_date(new Date());
			$('input[name=entry_date]').val(date);

			$('form.update_entry').submit(function() {
				update_entry();
				return false;
			});

			if (id) {
				$.get(
					'/index.php/api/get_channel_entry',
					{'entry_id': id},
					function(data) {
						if (data.length) {
							var entry = data[0];
							$.each(entry, function(key, value) {
								$('input[name=' + key + '], select[name=' + key + '], textarea[name=' + key + ']').val(value);
							});

							$('#title').text(entry.title);

							var date = get_date(new Date(parseInt($('input[name=entry_date]').val()) * 1000));
							$('input[name=entry_date]').val(date);
						}
					},
					'json'
				);
			}
		}
	);
}


function update_entry()
{
	var data = $('form.update_entry').serialize();

	var method = $('input[name=entry_id]').val() ? 'update_channel_entry' : 'create_channel_entry';

	$.post(
		'/index.php/api/' + method,
		data,
		function(data) {
			$('.alert-success span').text(0);
			$('.alert-success').closest('.control-group').hide().fadeIn();
			$('.alert-error').closest('.control-group').hide();
			
			window.clearInterval(interval);			
			interval = window.setInterval(function() {
					$('.alert-success span').text(parseInt($('.alert-success span').text()) + 1);
				},
				1000
			);
		}
	).fail(
		function(error) {
			$('.alert-error').closest('.control-group').hide().fadeIn().find('.error_text').text(error.responseText);
			$('.alert-success').closest('.control-group').hide();
		}
	);
}


function delete_entry(id)
{
	$.post(
		'/index.php/api/delete_channel_entry',
		{'entry_id': id},
		function(data) {
			$('table.table tr#' + id).fadeOut(function() { 
				$(this).remove(); 
			});
		}
	);
}


function show_categories() 
{
	$('#main').load(
		'_layouts/categories.html',
		function(data) {
			$('.create_category').click(function() {
				show_category(0);
				return false;
			});

			$.get(
				'/index.php/api/get_categories_by_channel',
				{'channel_id': 1},
				function(data) {
					$.each(data, function(i, category) {
						$('table.table').append(
							'<tr><td>' + category.cat_id +
							'</td><td><a id="' + category.cat_id + '" href="#">' + category.cat_name + '</a>' + 
							'</td></tr>'
						);
					});

					$('table.table a').click(function() {
						show_category($(this).attr('id'));
						return false;
					});
				},
				'json'
			);
		}
	);
}


function show_category(id) 
{
	$('#main').load(
		'_layouts/category.html',
		function(data) {
			$('form .categories, a.cancel').click(function() {
				show_categories();
				return false;
			});

			$('form.update_category').submit(function() {
				update_category();
				return false;
			});

			if (id) {
				$.get(
					'/index.php/api/get_category',
					{'cat_id': id},
					function(data) {
						if (data.length) {
							var category = data[0];
							$.each(category, function(key, value) {
								$('input[name=' + key + ']').val(value);
							});

							$('#title').text(category.cat_name);
						}
					},
					'json'
				);
			}
		}
	);
}


function update_category()
{
	var data = $('form.update_category').serialize();

	var method = $('input[name=cat_id]').val() ? 'update_category' : 'create_category';

	$.post(
		'/index.php/api/' + method,
		data,
		function(data) {
			$('.alert-success span').text(0);
			$('.alert-success').closest('.control-group').hide().fadeIn();
			$('.alert-error').closest('.control-group').hide();
			
			window.clearInterval(interval);			
			interval = window.setInterval(function() {
					$('.alert-success span').text(parseInt($('.alert-success span').text()) + 1);
				},
				1000
			);
		}
	).fail(
		function(error) {
			$('.alert-error').closest('.control-group').hide().fadeIn().find('.error_text').text(error.responseText);
			$('.alert-success').closest('.control-group').hide();
		}
	);
}


function show_members() 
{
	$('#main').load(
		'_layouts/members.html',
		function(data) {
			$('table.table tr:gt(0)').remove();

			$.get(
				'/index.php/api/get_members',
				{ },
				function(data) {
					$.each(data, function(i, member) {
						$('table.table').append(
							'<tr><td>' + member.member_id +
							'</td><td><a id="' + member.member_id + '" href="#">' + member.username + '</a>' + 
							'</td><td>' + member.screen_name + '</td></tr>'
						);
					});

					$('table.table a').click(function() {
						show_member($(this).attr('id'));
						return false;
					});
				},
				'json'
			);
		}
	);
}


function show_member(id) 
{
	$('#main').load(
		'_layouts/member.html',
		function(data) {
			$('form .members, a.cancel').click(function() {
				show_members();
				return false;
			});

			$('form.update_member').submit(function() {
				update_member();
				return false;
			});

			if (id) {
				$.get(
					'/index.php/api/get_member',
					{'member_id': id},
					function(data) {
						if (data.length) {
							var member = data[0];
							$.each(member, function(key, value) {
								$('input[name=' + key + '], select[name=' + key + '], textarea[name=' + key + ']').val(value);
							});

							$('#title').text(member.username);
						}
					},
					'json'
				);
			}
		}
	);
}


function get_date(date)
{
	return date.getFullYear() + '-' + prepend_zero(date.getMonth() + 1) + '-' + prepend_zero(date.getDate()) + ' ' + prepend_zero(date.getHours()) + ':' + prepend_zero(date.getMinutes());
}


function prepend_zero(n)
{
	return ('0' + n).slice(-2);
}