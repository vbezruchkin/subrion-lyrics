Ext.onReady(function()
{
	// detect page url
	var pageUrl = intelli.config.admin_url + '/lyrics/' + itemname + '/read.json';

	// autocomplete for account
	if ($('#account').length > 0)
	{
		$("#account").autocomplete({ url: intelli.config.admin_url + '/lyrics/lyrics/read.json?action=getaccount', minChars: 2});
	}

	// process albums detection by artist
	if ('lyrics' == itemname)
	{
		// autocomplete for artist
		$("#artist").autocomplete({ url: intelli.config.admin_url + '/lyrics/artists/read.json?action=getartist', minChars: 2});

		// perform lyrics actions
		$("input[name='artist']").blur(function()
		{
			artistname = $("input[name='artist']").val();

			$.get(intelli.config.admin_url + '/lyrics/albums/read.json',  {action: 'getalbums', artist: artistname}, function(data)
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
					year = value.year ? value.year + ' - ' : '';
					options.append($("<option />").val(value.id).text(year + value.title));
				});
			});

			$("select[name='album']").removeAttr("disabled");

			// fill in albums
			artistname = $("input[name='artist']").val();
			if ('' == artistname)
			{
				$("select[name='album']").attr("disabled", "disabled");
			}
		});
	}

	// process title alias functionality
	intelli.title_cache = '';
	intelli.fillUrlBox = function()
	{
		var title_alias = $("#field_title_alias").val();
		var title = ('' == title_alias ? $("#field_title").val() : title_alias);
		var category = $('#parent_id').val();
		var item_id = $('#item_id').val();
		var cache = title + '%%' + category;

		if ('' != title && intelli.title_cache != cache)
		{
			var params = {action: 'getalias', title: title, category: category, id: item_id};
			if ('' != title_alias)
			{
				params.alias = 1;
			}

			$.getJSON(pageUrl, params, function(json)
			{
				if ('' != json.data)
				{
					$("#title_url").html(intelli.config.packages.coupons.url + json.data + (json.exists ? ' <b style="color:red">' + json.exists + '</b>' : ''));
					$("#title_box").fadeIn();
				}
			});
		}

		intelli.title_cache = cache;
	}

	$('#field_title').keyup(function()
	{
		$('#field-title-alias').show();
	});

	$("#field_title, #field_title_alias").blur(intelli.fillUrlBox).blur();
});