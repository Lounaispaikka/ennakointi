<?php

require_once(PATH_TEMPLATE.'ymparisto/include_header.php'); 

?>

<!--<div id="breadcrumb"><? // $Cms->outputBreadcrumb() ?></div>-->
<div id="leftCol" class="col2">
<? /*	
$Co = new \Lougis\utility\Compiler("indikaattori", "js");
$Co->addJs("/js/lougis/History.js");
$Co->addJs("/js/lougis/lib/general.functions.js");
//$Co->addJs("/js/lougis/view/Panel.js");
//$Co->addJs("/js/ymparisto/view/Indikaattori.js");
if ( isset($_REQUEST['debug']) && strpos(PATH_SERVER, 'development') != false ) {
        $Co->outputFilesScriptTags();
} else {
        $Co->outputScriptHtml();
}	*/?>
      
      
      
       <script type="text/javascript">
               console.log("3");
               Ext.onReady(function(){
                       
                        console.log("4");   
                    createPanels();
                });
              
	
	</script>
</div>
        
<div id="content" class="col2">
        <div id="cms_data" style="display: block;">
<?
$Con = $Page->getContentHtml();
if ( !empty($Con) ) print $Con; 
?>
        </div>
        <div id="chartdiv"></div>        
    
        <div id="social">

            <div id="email">
            <a href="mailto:?Subject=<?=$Site->title?> - <?=$Pg->title?>&Body=<?=$Pg->getPageFullUrl()?>">Sähköposti</a>
            </div>

            <div id="fb">
            <iframe src="//www.facebook.com/plugins/like.php?href=<?=urlencode($Pg->getPageFullUrl())?>&amp;locale=fi_FI&amp;layout=button_count&amp;show_faces=false&amp;width=125&amp;action=recommend&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:125px; height:21px;" allowTransparency="true"></iframe>
            </div>

            <div id="twitter">
            <a href="https://twitter.com/share" class="twitter-share-button" data-lang="fi" data-hashtags="ymparistonyt">Twiittaa</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            </div>

    </div>

    <? require_once(PATH_PUBLIC.'ymparisto/kommentointi.php');  ?>
</div>
<? require_once(PATH_TEMPLATE.'ymparisto/include_footer.php'); ?>
