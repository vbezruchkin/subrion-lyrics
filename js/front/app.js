$(document).ready(function ()
{
	$("#action-delete").click(function()
	{
		return confirm(intelli.lang.delete_coupon_confirmation);
	});

	// thumbs actions
	$('[class^="thumbs-"]').click(function()
	{
		var id = $(this).data("id");
		var trigger = $(this).data("trigger");

		$.ajax(
		{
			url: intelli.config.packages.coupons.url + 'coupon.json',
			type: 'get',
			dataType: 'json',
			data:  { action: 'thumbs', trigger: trigger, id: id },
			success: function (data)
			{
				if ('undefined' != typeof data.error)
				{
					var result = $('#thumb_result_' + id);
					if (!data.error)
					{
						result.html(data.data);
					}
					result.tooltip('hide').attr('data-original-title', data.msg).tooltip('fixTitle').tooltip('show');
				}
			}
		});

		return false;
	});
});