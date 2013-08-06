<?php
global $Site, $Lang, $Cms, $Pg, $LayoutConf;

if ( !isset($LayoutConf['outputTopNav']) ) $LayoutConf['outputTopNav'] = true;

require_once(PATH_SERVER.'utility/CMS/CmsPublic.php');
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');

$Pg = $Cms->getPage();

require_once(PATH_SERVER.'utility/LouGIS/mLogin.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=$Site->title?> - <?=$Pg->title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="all" />
	<meta name="author" content="Lounaispaikka - www.lounaispaikka.fi" />
        
	<meta http-equiv="X-UA-Compatible" content="IE=8">
	<? if ( !empty($Pg->keywords) ) { ?><meta name="keywords" content="<?=$Pg->keywords?>" /><? } ?>
	<? if ( !empty($Pg->description) ) { ?><meta name="description" content="<?=$Pg->description?>" /><? } ?>
	<link rel="stylesheet" type="text/css" href="/css/reset_css.css" />
	<!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
 <!--   <script type="text/javascript" src="/js/ext/ext-all.js"></script>-->
	<script type="text/javascript">
		if (!console) console = {log: function() {}};
	</script>
    <script type="text/javascript" src="/js/ext/ext-all-debug.js"></script>
	<script type="text/javascript" src="/js/ymparisto/loader.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="/js/jqueryPlugins/jquery.prettyPhoto.js"></script>
	<script type="text/javascript" src="/js/jqueryPlugins/jquery.ba-outside-events.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/js/ext/resources/css/ext-all.css" />
	
	<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.0/themes/ui-lightness/jquery-ui.css" />
	<script type="text/javascript" src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
	
	<!--<script type="text/javascript" src="js/lougis/lib/login.jquery.js"></script>-->
	<script type="text/javascript" src="/js/lougis/lib/login.js"></script>
  
    <!--<link rel="stylesheet" type="text/css" href="/js/jpaginate/css/style.css" />-->
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Maven+Pro:400,700" />
	<link rel="stylesheet" type="text/css" href="/css/aluetietopalvelu.css" /> 
	<link rel="stylesheet" type="text/css" href="/css/ymparisto.css" />
	<link rel="stylesheet" type="text/css" href="/css/ennakointi.css" />
    <link rel="stylesheet" type="text/css" href="/css/prettyPhoto.css" /> 
	<link rel="Stylesheet" type="text/css" href="/js/jHtmlArea/style/jHtmlArea.css" />

	<!--<link rel="stylesheet" type="text/css" href="/css/modal_login.css" />-->
	<link rel="stylesheet" type="text/css" href="/css/loginStyle.css" />
	<script type="text/javascript" src="/js/lougis/lib/charts.ui.extjs.js"></script>
	<script type="text/javascript" src="/js/lougis/lib/admin_menu.ui.extjs.js"></script>
	
<? if ( isset($_SESSION['user_id']) ) { ?>
	
	<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script> 
	<script type="text/javascript" src="/js/lougis/lib/toimiala.ui.jquery.js"></script>
	<script type="text/javascript" src="/js/jqueryPlugins/jquery.dform-1.0.1.js"></script>
	<!--<script type="text/javascript" src="/js/jHtmlArea/scripts/jHtmlArea-0.7.5.min.js"></script>-->
	<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
<? } ?>	
	<!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="/css/aluetietopalvelu-ie8.css" /><![endif]-->
	<!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="/css/ymparisto-ie8.css" /><![endif]-->
     
</head>
<body>
<!--<div id="beta">BETA - Kehitysversio</div>-->

<? 

require_once('template/aluetietopalvelu/aluetietopalvelu_northbar.php');
 ?>
<div id="site"> 

        <div id="header" class="ymparisto">
                <div id="title_container">
                        <div id="title_left">
                                <div id="title">
                                    <h1>Koulutustarve-ennakointi</h1>
									<p>E-palvelualusta &ndash; työkalu verkostoyhteistyölle</p>
                                </div>
                        </div>
                        <div id="title_pic">
                                 <img src="/img/oasis_mv.jpg" alt="" />
						</div>
                </div>
                <? if ( $LayoutConf['outputTopNav'] ) { ?>  
                
                        <div id="topNav">
			<? $Cms->ouputTopNavigation(); ?>
			
		</div>
		<? } ?>
		<span id="topnavclr" class="clr" style="height: 0px;" />
		
        </div>
	<div id="middle">
	
		<!--  admin tools -->

		<? include('template/everkosto/page/toimiala.php'); ?>
		