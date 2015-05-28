$(function()
{
	$("#lyric_form").ajaxForm(
	{
		data: {ajax: 1},
		dataType: 'xml',
		beforeSubmit: function(formArray, jqForm)
		{
			$(jqForm).block({message: null});
		},

		success: function showResponse(responseText, statusText)
		{
			var error = $("error", responseText).text();
			var msg = $("msg", responseText).text();

			$("#lyric_form").unblock();

			if (error)
			{
				intelli.notifBox(
				{
					id: 'notification',
					type: 'error',
					msg: msg
				});
			}
			else
			{
				$("#lyric_form").hide();

				intelli.notifBox(
				{
					id: 'notification',
					type: 'success',
					msg: msg
				});
			}
		}
	});

	$("#artist").typeahead(
	{
		source: function (query, process)
		{
			return $.ajax(
			{
				url: intelli.config.packages.lyrics.url + 'add.json',
				type: 'get',
				dataType: 'json',
				data:  { q: query },
				success: function (data)
				{
					return typeof data.options == 'undefined' ? false : process(data.options);
				}
			});
		}
	});

	// perform lyrics actions
	$("input[name='artist']").blur(function()
	{
		artistname = $("input[name='artist']").val();

		if (artistname)
		{
			$.get(intelli.config.packages.lyrics.url + 'add.json',  {artist: artistname}, function(data)
			{
				var options = $("#album");

				// remove items
				$('#album option').each(function(i, option)
				{
					$(option).remove();
				});

				options.append($("<option />").val(0).text(' - please select artist first - '));

				$.each(data.data, function(item, value)
				{
					options.append($("<option />").val(value.id).text(value.title));
				});
			});

			$("select[name='album']").removeAttr("disabled");
		}
	});
});