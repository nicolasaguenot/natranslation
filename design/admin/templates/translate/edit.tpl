{*?template charset=UTF-8*}
{if is_set($dataforEdit)}
<div class="content-block">
    <form action={'translate/edit'|ezurl(no, "full")} method="post">
        <input type="hidden" name="todo" value="validEdit" />
        <input type="hidden" name="redirectURI" value={'translate/list'|ezurl(no, "full")} />
        <input type="hidden" name="dataKey" value="{$dataKey}" />
        <input type="hidden" name="context" value="{$context}" />
        <input type="hidden" name="file" value="{$file}" />

        <input type="hidden" name="sourceKey" value="{$sourceKey}" />
      <div class="controlbar" id="controlbar-top"><div class="box-bc"><div class="box-ml">
	        <div class="button-left">
	            <input type="submit" title="{'Validate translation'|i18n('natranslate')}" value="{'Send traduction'|i18n('natranslate')}" class="defaultbutton">
	            <input type="submit" title="{"Cancel translation"|i18n('natranslate')}" onclick="return confirm( '{'Do you really want to cancel the translation?'|i18n('natranslate')}' );" value="{'Cancel translation'|i18n('natranslate')}" class="button">
	        </div>	        
	    <div class="float-break"></div></div></div></div>
        
        <div class="box-header">
            <h1 class="context-title">&nbsp;{'Edit translation'|i18n('natranslate')} &lt;{$dataKey}&gt;</h1>
            <div class="header-mainline"></div>
        </div>
        
    {foreach $dataforEdit as $keyLanguage => $value}        
        <div style="margin:10px 5px;">

            <label><img src="{concat('/share/icons/flags/', $languageList.$keyLanguage.locale|extract( 0, 6 ), '.gif')}" />&nbsp;{$languageList.$keyLanguage.name}</label>
            <textarea name="translate[{$keyLanguage}]" rows="4" cols="50">{$value}</textarea>
        </div>
    {/foreach}    
	    <div class="controlbar">
	        <div class="block">
	            <input type="submit" title="{'Validate translation'|i18n('natranslate')}" value="{'Send traduction'|i18n('natranslate')}" class="defaultbutton">
	            <input type="submit" title="{"Cancel translation"|i18n('natranslate')}" onclick="return confirm( '{'Do you really want to cancel the translation?'|i18n('natranslate')}' );" value="{"Cancel translation"|i18n('natranslate')}" class="button">
	        </div>
	    </div>       
    </form>         
</div>
{/if}
{undef}
