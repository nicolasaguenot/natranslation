{*?template charset=UTF-8*}
<table class="list" cellspacing="0">


{def $numberCol = 4}
{def $compteur = 0}
{def $file = ""}
{def $context = ""}
{foreach $dataList as $sourceKey => $dataSource}
    {foreach $dataSource as $key => $data}

        {if ne($file, $sourceKey|explode("--").0|explode("|")|implode("/"))}
            {set $file = $sourceKey|explode("--").0|explode("|")|implode("/")}
            <tr class="{cond($compteur|mod(2)|eq(0), 'bgdark', 'bglight')}">
                <th  style="width:100%; text-align:center; font-size:14px; font-weight:bolder;" colspan="{$numberCol}">
                    {$file}
                </th>
            </tr>
            {set $compteur = $compteur|inc()} 
        {/if}
         {if ne($context, $sourceKey|explode("--").1|explode("|")|implode("/"))}
            {set $context = $sourceKey|explode("--").1|explode("|")|implode("/")}
            <tr class="{cond($compteur|mod(2)|eq(0), 'bgdark', 'bglight')}">
                <th  style="width:100%; text-align:center; font-size:12px; font-weight:bolder;" colspan="{$numberCol}">
                    {$context}
                </th>
            </tr>
            {set $compteur = $compteur|inc()} 
        {/if}
  	
			{def $translation = nachecktranslation($data, $file, $context, ezini("Translation", "LanguageTranslationViewInList", "natranslate.ini")))
			{$translation|count_chars()}
			{if eq($translation|count_chars(), 0)}
				<tr class="{cond($compteur|mod(2)|eq(0), 'bgdark', 'bglight')}" style="background:{ezini('Translation', 'BackgroundColorUntranslatedWord', 'natranslate.ini')}">
			{else}
				{if eq($translation, $data)}
					<tr class="{cond($compteur|mod(2)|eq(0), 'bgdark', 'bglight')}" style="background:{ezini('Translation', 'BackgroundColorSameTranslationWord', 'natranslate.ini')}">
				{else}
					{if ne(ezini('Translation', 'BackgroundColorTranslatedWord', 'natranslate.ini'), "none")}
						<tr class="{cond($compteur|mod(2)|eq(0), 'bgdark', 'bglight')}" style="background:{ezini('Translation', 'BackgroundColorTranslatedWord', 'natranslate.ini')}">
					{else}
						<tr class="{cond($compteur|mod(2)|eq(0), 'bgdark', 'bglight')}">
					{/if}
				{/if}
			{/if}
			
			<td  style="width:10%">
				{nachecktranslationlanguage($data, $file, $context)}
			</td>
            <td  style="width:30%">
                {set $data = $data|explode("%")|implode("[param]")}
                <strong>{$data}</strong> 
            </td>
	
			<td style="width:30%;">
				{$translation}
			</td>
            <td  style="width:20%; text-align:right">
                <a title="{$data}" href={concat('translate/edit/', '(file)/', $file, '/(context)/', $context, '/(sourceKey)/', $sourceKey, '/(dataKey)/', $data)|ezurl()} class="defaultbutton">
                    {'Click to edit'|i18n('natranslate')}
                </td>
            </td>
            
        </tr>
        
        {if and(ge($languageList|count(), 6), $localeGet|not())}
            {include uri='design:translate/translationline.tpl' class=cond($compteur|mod(2)|eq(0), 'bglight', 'bgdark') id=$compteur}
        {/if}    
                    
        {set $compteur = $compteur|inc()} 
    {/foreach}
{/foreach}
</table>
