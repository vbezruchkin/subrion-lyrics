<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}
	{capture name='title' append='field_after'}
		<div class="row" id="field-title-alias"{if 'edit' != $pageAction && empty($smarty.post.save)} style="display: none;"{/if}>
			<label class="col col-lg-2 control-label" for="field_title_alias">{lang key='title_alias'}</label>

			<div class="col col-lg-4">
				<input type="text" name="title_alias" id="field_title_alias" value="{if isset($item.title_alias)}{$item.title_alias}{/if}">
				<p class="help-block">{lang key='page_url_will_be'}: <span class="text-danger" id="title_url">{$smarty.const.IA_URL}</span></p>
			</div>
		</div>
	{/capture}

	{include file='field-type-content-fieldset.tpl' item_sections=$fields_groups isSystem=true}
</form>

{ia_add_js}
	var itemname = 'genres';
{/ia_add_js}

{ia_print_js files='_IA_URL_packages/lyrics/js/admin/footer'}