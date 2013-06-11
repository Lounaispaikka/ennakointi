<?php
global $Site, $Lang, $Cms, $Pg, $LayoutConf;

if ( !isset($LayoutConf['outputTopNav']) ) $LayoutConf['outputTopNav'] = true;

require_once(PATH_SERVER.'utility/CMS/CmsPublic.php');
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');
//include("hits/simphp.php");

$Pg = $Cms->getPage();

//var_dump($LayoutConf);die;
?><!DOCTYPE html>
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
        <script type="text/javascript" src="/js/ext/ext-all.js"></script>
       <!-- <script type="text/javascript" src="/js/ext/ext-all-debug.js"></script>-->
        <script type="text/javascript" src="/js/ymparisto/loader.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="/js/jqueryPlugins/jquery.prettyPhoto.js"></script>
        <script type="text/javascript" src="/js/jqueryPlugins/jquery.ba-outside-events.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/js/ext/resources/css/ext-all.css" />
  
    <!--<link rel="stylesheet" type="text/css" href="/js/jpaginate/css/style.css" />-->
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Maven+Pro:400,700" />
	<link rel="stylesheet" type="text/css" href="/css/aluetietopalvelu.css" /> 
	<link rel="stylesheet" type="text/css" href="/css/ymparisto.css" /> 
        <link rel="stylesheet" type="text/css" href="/css/prettyPhoto.css" /> 
	<!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="/css/aluetietopalvelu-ie8.css" /><![endif]-->
	<!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="/css/ymparisto-ie8.css" /><![endif]-->
     
</head>
<body>
        <script type="text/javascript">
        console.log("22");
        </script>
<? 
require_once('template/aluetietopalvelu/aluetietopalvelu_northbar.php');
 ?>
<!--<div id="beta">BETA - Kehitysversio</div>-->
<div id="site"> 
<!--	<div id="header" class="ymparisto">
		<div id="title_container">
                        <div id="title_left">
                                <div id="title">
                                        <img src="/img/ymparisto/logo-aurinko.png" alt="" />
                                        <h1>Ympäristö Nyt</h1><p>Lounais-Suomen ympäristön tila ja seuranta</p>
                                </div>
                        </div>
			<div id="title_pic">
				<img src="/img/kasvi.png" alt="" />
                                 <img src="/img/ymparisto/yn_aurinko_reuna_1000px_ylapalkkiin2.png" alt="" />
                                 <img src="/img/ymparisto/kaste_web_kaannetty.png" alt="" />
			</div>
                </div>
		<? if ( $LayoutConf['outputTopNav'] ) { ?>  
		<div id="topNav">
			<? $Cms->ouputTopNavigation(); ?>
		</div>
		<? } ?>
		<span id="topnavclr" class="clr" style="height: 0px;" />
	</div>-->
        <div id="header" class="ymparisto">
                <div id="title_container">
                        <div id="title_left">
                                <div id="title">
                                        <a href="/fi/ymparisto-nyt/"><img src="/img/ymparisto/logo-aurinko.png" alt="" />
                                                <h1>Ympäristö Nyt</h1><p>Lounais-Suomen ympäristön tila ja seuranta</p>
                                        </a>
                                </div>
                        </div>
                        <div id="title_pic">
                                 
                                 <img src="/img/ymparisto/kaste_web_kaannetty.png" alt="" />
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
