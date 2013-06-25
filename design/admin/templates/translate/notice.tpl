{*?template charset=UTF-8*}


  <div class="box-header">
	    <div class="button-left">
	        <h1 class="context-title">{'Translate / Notice'|i18n('natranslate')}</h1>
	    </div>
	    <br class="clearfloat" />
	</div>
	
	<div class="box-content">    
	    <div class="box-ml">
	    	<div class="box-mr">
	    		<div class="box-content">

    				<div class="box-header">
				        <div class="button-left">
				            <h2 class="context-title">
				                {"Introduction"|i18n("natranslate/notice")}
				            </h2>
				        </div>
				        <div class="float-break"></div>
				    </div>

				    <div class="block">
		   				<p>{"This extension aims at generate and manage files translations"|i18n('natranslate/notice')}</p>
				       	<p>{"To avoid to have a (very) big list of translation, just important translation, related of your website is present in this list"|i18n('natranslate/notice')}</p>
				       	<p>{"If, for any reason, you don't find your text line in this list, please contact your webmaster or favourite admin"|i18n('natranslate/notice')}</p>
			       	</div>

				    <div class="box-header">
				        <div class="button-left">
				            <h2 class="context-title">
				                {"Usability note"|i18n("natranslate/notice")}
				            </h2>
				        </div>
				        <div class="float-break"></div>
				    </div>
				    <div class="block">
				    	<h4>{"Presentation"|i18n("natranslate/notice")}</h4>
						<hr>
				    	<p>{"Click on the tab %1 Translation %2"|i18n("natranslate/notice", , array("<strong>", "</strong>"))}.</p>
				    	<p>
				    	{"When this webpage is loaded, you've"|i18n("natranslate/notice")} : </p>
			    		<ul>
		    				<li>{"At the left : 3 links to go to..."|i18n("natranslate/notice")}
		    					<ul>
	    							<li>{"the list of translation (your default view)"|i18n("natranslate/notice")}</li>
	    							<li>{"the generation of files"|i18n("natranslate/notice")}</li>
	    							<li>{"this notice"|i18n("natranslate/notice")}</li>
	    						</ul>
	    					</li>
	    					<li>{"At the center : Your translation list view"|i18n("natranslate/notice")}</li>
	    				</ul>

	    				<h4>{"Translation edition"|i18n("natranslate/notice")}</h4>
	    				<hr>
	    				<p>{"You can edit your translation with the right button %1 Click to edit %2"|i18n("natranslate/notice", array("<strong>", "</strong>"))}.</p>
	    				<p>{"When you click on this button, a edition page is display with as many fields as related language in admin siteaccess / SiteLanguageList tab"|i18n("natranslate/notice")}. </p>
	    				<p>{"If you want to add a brand new language, please contact the webmaster or favourite admin"|i18n("natranslate/notice")}.</p>
	    				<p>{"When your translation edition is finished, click on %1 Send %2 button"|i18n("natranslate/notice", array("<strong>", "</strong>"))}.</p>
	    				<p>{"Your webpage reload and display previous list of translation"|i18n("natranslate/notice")}</p>


					    <div style="padding:10px; border:1px solid #ccc; color:#555">
				    		<h4>{"Important note about translation"|i18n("natranslate/notice")}</h4>
				    		<p>
					    		{"When you see the list of translation content, you can see [Param]x"|i18n("natranslate/notice")}.<br>
					    		{"These info are very important because there is params use for translate elements"|i18n("natranslate/notice")}.<br>
					    		{"For example"|i18n("natranslate/notice")} : <br>
					    		{"If you've %1 Training for [Param]1 %2, you edit your content and have %1 Training for %3 %2"|i18n("natranslate/notice", ,array("<strong>", "</strong>", "[%1]"))}. <br>
					    		<strong>%1</strong> {"is an important value and must be inserted in your others translations block ( %1 Formation pour %3 %2 for French block for example)"|i18n("natranslate/notice", ,array("<strong>", "</strong>", "[%1]"))}
				    		</p>
				    	</div>
						<h4>{"I can't edit my translation in a desired language"|i18n("natranslate/notice")}</h4>
	    				<hr>
	    				<p>{"If you don't have a block to edit in the desired language, you can contact the webmaster or favourite admin to unlocked you."|i18n("natranslate/notice")}</p>
	    				<p>{"Maybe it's a problem linked to your configuration and a reload of your configuration file is concievable."|i18n("natranslate/notice")}</p>

					</div>
			       


			    </div>
			</div>
		</div>
	</div>
	{undef}
{/if}
