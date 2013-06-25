{*?template charset=UTF-8*}
<div class="box-header">
    <div class="button">
        <h1 class="context-title">{'Generation files translation'|i18n('natranslate')}</h1>
    </div>
    <div class="float-break"></div>
</div>

     <div class="float-break"></div>
{if is_set($generation)}
    {if $generation}
        <p>{'The generation passed well'|i18n('natranslate')}</p>
        <p><a href="{'translate/list'|ezurl(no, 'full')}">{'Go to translation list'|i18n('natranslate')}</a></p>
    {else}
        <p style="color:red">{"A problem arose during the files generation"|i18n('natranslate')}</p>
    {/if} 
{else} 
    {def
        $exludeExtension = ezini( 'ExcludeExtension', 'extension', 'natranslate.ini' )
    }
<div class="box-content">    
    <form action={'translate/generatefiles'|ezurl()} method="post">
        <input type="hidden" name="todo" value="chooseExtension" />
       
         <div class="float-break"></div>
        
    {foreach $extensionList as $extension}        
        {if $exludeExtension|contains($extension)|not()}
        <div style="margin:10px 5px;">            
            <span style="float:left;margin-right:10px;">

                <input type="checkbox" value="{$extension}" name="extension[]"  {if eq($extension, "main")}disabled{/if} />
      	</span>
            <span><label>{$extension}</label></span>
        </div>
        {/if}
    {/foreach}    
        <div class="controlbar">
            <div class="block">
                <input type="submit" title="{'Validate generation'|i18n('natranslate')}" value="{'Send generation'|i18n('natranslate')}" class="defaultbutton">
                <input type="submit" title="{"Cancel generation"|i18n('natranslate')}" onclick="return confirm( '{'Do you really want to cancel the generation?'|i18n('natranslate')}' );" value="{"Cancel generation"|i18n('natranslate')}" class="button">
            </div>
        </div>       
    </form>
</div>
    {undef}
{/if}

