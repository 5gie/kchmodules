<div class="summary-description-container">
	<fieldset class="form-group">    	
		<div class="tab-content">
			<div class="tab-pane panel panel-default active" id="subtitle">                      
				<div class="translations tabbable" id="form_step1_subtitle">      
					<div class="translationsFields tab-content">        
						{foreach from=$languages item=language}
							<div data-locale="{$language.iso_code}" class="translationsFields-form_step1_subtitle_1 tab-pane translation-field translation-label-{$language.iso_code} {if count($languages) == 1 || $default_language == $language.id_lang}active{/if}">
								<div class="form-group">
									<label>Podtytuł</label>
									<input name="subtitle_{$language.id_lang}" class="form-control" value="{if isset($subtitle[$language.id_lang])}{$subtitle[$language.id_lang]}{/if}">
								</div>
							</div> 
						{/foreach}   
					</div>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset class="form-group">    	
		<div class="tab-content">
			<div class="tab-pane panel panel-default active" id="additional_description">                      
				<div class="translations tabbable" id="form_step1_additional_description">      
					<div class="translationsFields tab-content">        
						{foreach from=$languages item=language }
						<div data-locale="{$language.iso_code}" class="translationsFields-form_step1_description_short_1 tab-pane translation-field translation-label-{$language.iso_code} {if count($languages) == 1 || $default_language == $language.id_lang}active{/if}">
							<div class="form-group">
								<label>Tekst na głównej</label>
								<textarea name="additional_description_{$language.id_lang}" class="autoload_rte">{if isset({$additional_description[$language.id_lang]}) && {$additional_description[$language.id_lang]} != ''}{$additional_description[$language.id_lang]}{/if}</textarea>    
							</div>
						</div> 
                        {/foreach}  
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</div>