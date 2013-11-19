$(document).ready(function()
{
	$('select.access').change(function() {
		if ($(this).val() == 'restricted') {
			$(this).closest('tr').find('.restricted').show();
		}
		else {				
			$(this).closest('tr').find('.restricted').hide();
		}
	});
});