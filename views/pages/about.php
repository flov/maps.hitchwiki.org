<h2><?php echo _("About Hitchwiki Maps"); ?></h2>

<div class="textbox">

	<div class="align_right" style="width: 300px; margin-left: 30px;">
	
		<!--<img src="badge.png" alt="" class="align_right" style="margin: 0 0 20px 20px;" />-->
		
		<p><a href="#" class="bigger icon email" onclick="javascript:open_card('contact','<?php echo _("Contact us!"); ?>'); return false;"><?php echo _("Contact us!"); ?></a></p>
		
		<br />

		<fb:like-box profile_id="133644853341506" width="300" connections="5" stream="true" header="true"></fb:like-box>
		
		<br  /><br  />
		
		<fb:activity recommendations="true" width="300"></fb:activity>
	
		<br /><br />
		
		<small><?php echo _("You can show your love to us and recommend Hitchwiki Maps to your hitchhiker mates in Facebook by pressing this cute little button with a familiar icon on it:"); ?></small>
		
		<fb:like layout="standard" width="300" href="<?php echo urlencode($settings["base_url"]); ?>">
	
		<script type="text/javascript">
		    $(function() {
		    	FB.XFBML.parse(document.getElementById('pages'));
		    });
		</script>
	</div>


	<h3><?php echo _("What is this?"); ?></h3>
	<p><?php echo _("This is supposed to be a worldmap for hitchhikers, showing good and bad hitching places. Feel free to add all your favourite hitching places (or even more) to the map."); ?></p>
	
	<h3><?php echo _("How can I add places?"); ?></h3>
	<p><?php echo _("Just click on <i>Add place</i> in the menu. Set the orange marker to the place and click on <i>Add place</i>. Make sure to zoom as close as possible, so the point will be more accurate. It is also helpful if you give your points a rating and maybe a little description (i.e. what kind of place this is or how to get there). Please write description at least in English."); ?></p>
	
	<h3><?php echo _("Why should I sign up?"); ?></h3>
	<p><?php echo _("If you are logged in, you will have some more features on this site. I.e. you will be able to modify your places later, and your nickname will be shown on each of your places and comments."); ?> <a href="#" onclick="open_page('why_register'); return false;" title="<?php echo _("Why register?"); ?>"><?php echo _("Read more..."); ?></a></p>
	
	<h3><?php echo _("Why is the map always centered on Europe?"); ?></h3>
	<p><?php echo _("Most of this maps hitchhiking places are in Europe. If you have registered, you can set a point of your current country from settings. The map will center there whenever you log in."); ?></p>
	
	<h3><?php echo _("Can I use HTML tags in descriptions and comments?"); ?></h3>
	<p><?php echo _('No, but you can use <a href="http://en.wikipedia.org/wiki/Markdown" target="_blank">Markdown</a> syntax.'); ?></p>
	

	<h3><?php echo _("People involved"); ?></h3>
	<ul>
		<li><a href="http://www.ihminen.org">Mikael Korpela</a></li>
		<li><a href="http://hitchwiki.org/en/User:MrTweek">MrTweek</a></li>
	</ul>
	
	
	<h3><?php echo _("Translators"); ?> &mdash; <em><?php echo _("Thank you very much!"); ?></em></h3>
	<ul>
		<li><?php echo _("English"); ?> &mdash; Mikael, Platschi</li>
		<li><?php echo _("Finnish"); ?> &mdash; Mikael</li>
		<li><?php echo _("German"); ?> &mdash; MrTweek, Platschi</li>
		<li><?php echo _("Dutch"); ?> &mdash; Platschi</li>
		<li><?php echo _("Russian"); ?> &mdash; Siberian explorer, Platschi, rAndoM</li>
		<li><?php echo _("Romanian"); ?> &mdash; montaniard</li>
		<li><?php echo _("Portuguese"); ?> &mdash; Joao</li>
		<li><?php echo _("Chinese"); ?> &mdash; Mipplor</li>
		<li><?php echo _("Lithuanian"); ?> &mdash; Mindo, Prino</li>
		<li><?php echo _("Italian"); ?> &mdash; Maurizio</li>
		<li><?php echo _("Polish"); ?> &mdash; Robert, Iza</li>
		<li><?php echo _("Spanish"); ?> &mdash; Prino</li>
		<li><?php echo _("French"); ?> &mdash; Perilisk</li>
		<!-- 
		<li><?php echo _("Latvian"); ?> &mdash; Reinis</li>
		<li><?php echo _("Swedish"); ?> &mdash; </li>
		-->
		<li><?php echo _("English")." ("._("Pirate").")"; ?> &mdash; Mikael <em>(Yarr!)</em></li>
	</ul>
	
	<p><a href="#" onclick="open_page('translate'); return false;" class="icon world"><?php echo _("Help us with translating!"); ?></a><br /></p>
	
	
	
	<div id="used_tech" style="display: none;">
	<h3><?php echo _("Used technologies"); ?></h3>
	
	<h4><?php echo _("Server side"); ?></h4>
	<ul>
		<li><a href="http://sourceforge.net/projects/phpolait/">PHPOLait</a></li>
		<li><a href="http://sourceforge.net/projects/snoopy/">Snoopy</a></li>
		<li><a href="http://curl.haxx.se/">cURL</a></li>
		<li><a href="http://www.gnu.org/software/gettext/">Gettext</a></li>
		<li><a href="http://michelf.com/projects/php-markdown/">Markdown</a></li>
	</ul>
	
	<h4><?php echo _("Client side"); ?></h4>
	<ul>
		<li><a href="http://openlayers.org/">Open Layers</a></li>
		<li><a href="http://jquery.com/">jQuery</a></li>
		<li><a href="http://jqueryui.com/">jQuery UI</a></li>
		<li><a href="http://plugins.jquery.com/project/cookie">jQuery Cookie Plugin</a></li>
		<li><a href="http://plugins.jquery.com/project/pstrength">jQuery Password Strength Field Plugin</a></li>
		<li><a href="http://code.google.com/p/jquery-json/">jQuery JSON</a></li>
		<li><a href="http://www.famfamfam.com/lab/icons/">Fam Fam Fam Silk &amp; Flag icons</a></li>
		<li><a href="http://www.aiga.org/content.cfm/symbol-signs">Aiga - Symbol Signs</a></li>
	</ul>
	
	<h4><?php echo _("Services"); ?></h4>
	<ul>
		<li><a href="http://ipinfodb.com/">IPInfoDB API</a></li>
		<li><a href="http://www.openstreetmap.org/">Open Street Map</a></li>
		<li><a href="http://maps.google.com/">Google Maps</a></li>
		<!--
		<li><a href="http://maps.bing.com/">Bing Maps</a></li>
		-->
		<li><a href="http://www.geonames.org/">Geonames</a></li>
		<li><a href="http://wiki.openstreetmap.org/wiki/Nominatim">Nominatim</a></li>
		<li><a href="http://en.gravatar.com/">Gravatar</a></li>
		<li><a href="http://latitude.google.com/">Google Latitude</a></li>
	</ul>
	</div>
	<a href="#" id="used_tech_link" class="icon wrench"><?php echo _("Used technologies"); ?></a>
	<script type="text/javascript">
	    $(function() {
	    	$("#used_tech_link").click(function(e){
	    		$(this).slideUp("fast");
	    		$("#used_tech").slideDown("fast");
	    	});
	    });
	</script>
	
	

<div class="clear"></div>
</div><!-- /textblock -->


