<div id="northBar">
	<div id="northWrap">
		<ul id="northNav">
			<!--<li><a href="#">Aluetietopalvelu</a></li>-->
			<li><a href="http://paikkatietokeskus.lounaispaikka.fi">Lounaispaikka</a></li>
			<li><a href="http://ymparisto.lounaispaikka.fi">Ymp&auml;rist&ouml; Nyt</a></li>
			<li><a href="/" style="font-weight:bold;">Horisontti</a></li>
			<li><a href="http://www.lounaispaikka.fi">Kartat</a></li>
 		</ul>

		<!-- Login Starts Here -->
            <div id="loginContainer">
                <a href="#" id="loginButton"><span><? if(!isset($_SESSION['user_id'])) echo 'Kirjaudu'; else echo 'Asetukset'; ?> </span> <em></em></a>
                <div style="clear:both"></div>
                <div id="loginBox">
					<? if (!isset($_SESSION['user_id'])) { ?>
                    <form id="loginForm" method="post">
					
                        <fieldset id="body">
                            <fieldset>
                                <label for="email">S&auml;hk&ouml;posti</label>
                                <input type="text" name="email" id="email" />
                            </fieldset>
                            <fieldset>
                                <label for="password">Salasana</label>
                                <input type="password" name="password" id="password" />
                            </fieldset>
							<fieldset>
								<? if( !empty($errMsg) ) { ?>
									<p id="LoginErrorMsg"><?=$errMsg?><p>
								<? } ?>
							 </fieldset>
                            <input type="submit" id="login" value="Kirjaudu" />
                     <!--       <label for="checkbox"><input type="checkbox" id="checkbox" />Muista minut</label> -->
                        </fieldset>
                        <span><a href="#">Salasana unohtunut?</a></span>
                    
					</form>
					<? }
					else {  
					require_once(PATH_SERVER.'utility/UsersAndGroups/User.php');
					$Uinfo = new \Lougis\utility\User();
					$Uinfo_array[] = $Uinfo->getLoggedUserInfo();
				?> 
					<div id="loginForm">
						<fieldset id="body">
							<fieldset>
								<p><? echo $Uinfo_array[0]["firstname"]." ".$Uinfo_array[0]["lastname"];?></p>
								<p><? echo $Uinfo_array[0]["email"];?></p>
							</fieldset>
							<fieldset>
								<p><a href="/hallinta/" class="linkJs">Avaa sis&auml;ll&ouml;nhallinta</a></p>
								<p><a href="javascript:void(0)" class="linkJs" id="openToimiala">Muokkaa toimialoja</a></p>
							</fieldset>
							<!--<fieldset>
								<p><a href="javascript:void(0)" class="linkJs">Vaihda salasana</a></p>
								<p><a href="javascript:void(0)" class="linkJs">Vaihda s&auml;hk&ouml;postisoite</a></p>
							</fieldset>						-->
						</fieldset>
						<span><a href="/run/lougis/usersandgroups/logoutUser/">Kirjaudu ulos</a></span>
					</div>
				<?	
					
				} ?>
					
                </div>
            </div>
            <!-- Login Ends Here -->
			
			<div id="hakukentta" style="float:right" ><form id="cse_input" action="http://dev.ymparisto.lounaispaikka.fi/fi/haku/" >
                                    <input type="hidden" name="cx" value="000855416783566672576:b3jhy9nir_w" />
                                    <input type="hidden" name="ie" value="UTF-8" />
                                    <input style="" type="text" name="q" size="31" placeholder="Hae..." />
                                    <input class="submit" type="submit" name="sa" >
                                </form>
            </div>  
    </div>

    
    
<!--	<img id="logo" src="/img/lounaispaikka-logo.png" />-->
	<script type="text/javascript">
		$(function() {
			$('#openToimiala').bind('click', function() {
				console.log("clickta");
				openToimialaDialog();
				return false;
			});
			
					$("body").on({
						ajaxStart: function() { 
							$(this).addClass("loading"); 
						},
						ajaxStop: function() { 
							$(this).removeClass("loading"); 
						}    
					});
		});
	</script
</div>