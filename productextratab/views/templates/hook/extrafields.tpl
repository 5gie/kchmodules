<div class="summary-description-container">
	<div class="custom-control custom-checkbox">
		<input type="checkbox"
			class="custom-control-input"
			id="extra_field_enabled"
			name="extra_fields_enabed"
			{if isset($extra_fields_enabled) && $extra_fields_enabled == 1}checked{/if}
		>
		<label class="custom-control-label" for="extra_field_enabled">Włącz wyświetlanie na tym produkcie</label>
	</div>
	<div class="translations tabbable">
		{for $key=0 to $fields_amount - 1}
			<div class="translationsFields tab-content form-group mt-3">        	
				{foreach from=$languages item=language}
					<div data-locale="{$language.iso_code}" class="tab-pane translation-field translation-label-{$language.iso_code} {if count($languages) == 1 || $default_language == $language.id_lang}show active{/if}">
						<div class="row">
							<div class="col-xl-5 col-lg-3">
								<div class="form-group">
									<label>Tytuł</label>
									<input type="text" class="form-control mb-1" name="extra_fields[{$key}][title][{$language.id_lang}]" value="{if isset($extra_fields.$key.title)}{$extra_fields.$key.title}{/if}">
								</div>
							</div>
							<div class="col-xl-5 col-lg-3">
								<div class="form-group">
									<label>Podtytuł</label>
									<input type="text" class="form-control mb-1" name="extra_fields[{$key}][subtitle][{$language.id_lang}]" value="{if isset($extra_fields.$key.subtitle)}{$extra_fields.$key.subtitle}{/if}">
								</div>
							</div>
							<div class="col-xl-2 col-lg-4">
								<div class="form-group">
									<label>Sortowanie</label>
									<input type="number" class="form-control mb-1" name="extra_fields[{$key}][sort][{$language.id_lang}]" value="{if isset($extra_fields.$key.sort)}{$extra_fields.$key.sort}{/if}">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Opis</label>
							<textarea name="extra_fields[{$key}][content][{$language.id_lang}]" class="autoload_rte">{if isset($extra_fields.$key.content)}{$extra_fields.$key.content}{/if}</textarea>
						</div>
					</div> 
				{/foreach}   
			</div> 
		{/for}
	</div>
</div>