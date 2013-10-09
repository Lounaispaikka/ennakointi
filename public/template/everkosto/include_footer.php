	</div>
</div>

<div id="footer">
	<ul id="partners">
	
		<li id="vsl">
			<a href="http://www.varsinais-suomi.fi/" target="_blank" title="Varsinais-Suomen liitto">
			<img src="/img/kumppanit/color/vsl.png" alt="Varsinais-Suomen liitto"></a>
		</li>
		
		<li id="vsl">
			<a href="http://www.ely-keskus.fi/varsinais-suomi" target="_blank" title="Varsinais-Suomen ELY-keskus">
			<img src="/img/kumppanit/color/ely.png" alt="Varsinais-Suomen ELY-keskus"></a>
		</li>
		
		<li id="vipu">
			<a href="#" target="_blank" title="Euroopan unioni: Vipuvoimaa EU:lta 2007-2013">
			<img src="/img/kumppanit/color/vipuvoimaa.png" alt="Vipuvoimaa EU:lta 2007-2013"></a>
		</li>
		
		<li id="esr">
			<a href="#" target="_blank" title="Euroopan unioni: Euroopan sosiaalirahasto">
			<img src="/img/kumppanit/color/esr.png" alt="Euroopan sosiaalirahasto"></a>
		</li>
	</ul>
	<!--<ul id="partners">
		<li id="ely">
			<a href="http://www.ely-keskus.fi/varsinais-suomi" target="_blank" title="Varsinais-Suomen ELY-keskus">
			<img src="/img/spacer.png" alt="Varsinais-Suomen ELY-keskus" style="width: 50px;height: 50px;"></a>
		</li>
		<li id="vsl">
			<a href="http://www.varsinais-suomi.fi/" target="_blank" title="Varsinais-Suomen liitto">
			<img src="/img/spacer.png" alt="Varsinais-Suomen liitto" style="width: 36px;height: 53px;"></a>
		</li>
		<li id="esr">
			<a href="#" target="_blank" title="Euroopan unioni: Euroopan sosiaalirahasto">
			<img src="/img/spacer.png" alt="Euroopan sosiaalirahasto"" style="width: 95px;height: 57px;"></a>
		</li>
	</ul>-->
</div>
<?
/*$Co = new \Lougis\utility\Compiler("ymparisto-ui-jquery", "js");
$Co->addJs("/js/ymparisto/ymparisto.ui.jquery.js");
if ( isset($_REQUEST['debug']) && strpos(PATH_SERVER, 'development') != false ) {
	$Co->outputFilesScriptTags();
} else {
	$Co->outputScriptHtml();
}*/
?>
<script type="text/javascript">
	$(document).ready(function(){
		$("a[rel^='prettyPhoto']").prettyPhoto({
			deeplinking:false,
			social_tools:false
		});
		
		/*if(window.location.hash == "#login-box") {
			openModalLogin();
		}*/
		
	});

</script>
</body>
</html>
