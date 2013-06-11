<?php

global $Site, $Page;

require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 
?>
<div id="content">
<script type="text/javascript" src="/js/jqueryPlugins/jquery.dform-1.0.1.js"></script>
<script type="text/javascript" src="/js/lougis/lib/kysely.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		showForm();
	}
</script>
<div id="kysely">
	<form id="kysely_form">
	</form>	
</div>
</div>

<div>


<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>