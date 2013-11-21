$(document).ready(function()
{
	$('select.access').change(function() {
		if ($(this).val() == 'restricted') {
			$(this).closest('tr').find('.options.restricted').show();
		}
		else {
			$(this).closest('tr').find('.options.restricted').hide();
		}

		$(this).closest('tr').find('.status').attr('class', 'status ' + $(this).val());
	});
});