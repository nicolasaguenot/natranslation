{*?template charset=UTF-8*}

  {def
	    $localeGet    = false()
	    $sourceKeyGet = false()
	    $dataKeyGet   = false()
	}
	{if ezhttp_hasvariable( 'sourceKey', 'get' )}
	    {set $sourceKeyGet = ezhttp( 'sourceKey', 'get' )}
	{else}
	    {set $sourceKeyGet = $sourceKey}        
	{/if}
	<div class="box-header">
	    <div class="button-left">
	        <h1 class="context-title">{'Translator / List'|i18n('natranslate')} ({$numberTotal}&nbsp;{'translations'|i18n('natranslate')})</h1>
	    </div>
	    <br class="clearfloat" />
	</div>
	<div class="context-block">
	    <div class="box-header">
		    <div class="content-navigation-childlist">
		    	{def $urlnotice = 'translate/notice'|ezurl(no, 'full')}
		       	<p>
		       		<strong>{"Before to begin, please check the %1 notice %2"|i18n('natranslate', , array(concat("<a href='", $urlnotice, "'>"),"</a>"))}</strong>
		       	</p>
		    </div>
		</div>	
    </div>
	<div class="box-content">    
	    <div class="box-ml">
	    	<div class="box-mr">
	    		<div class="box-content">
  
			        <div class="content-navigation-childlist">                            
						{if is_set($dataList)}
					        {include uri='design:translate/translationtable.tpl'}	   
						{/if}                                        
			        </div>
         
			    </div>
			</div>
		</div>
	</div>
	{undef}
{/if}
