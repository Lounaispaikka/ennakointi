/**
* login.ui.jquery.js
* 
* Kirjautuminen
* jquery, jquery ui, dform(jquery), form(jquery), validate(jquery)
* @author Ville Glad
* 
*/

/*jQuery('#cmsForm_info').dform({
		"action" : "testiNotRela.php",//"/run/lougis/cms/createNewPage/",
		"method" : "get",
		"html" :
			[
				{
					"name": "email"
					,"id": "email"
					,"type": "email"
				},
				{
					"name": "password"
					,"id": "password"
					,"type": "password"
				}
*/

function openModalLogin() {
		
			var loginBox = '#login-box';

			//Fade in the Popup
			$(loginBox).fadeIn(300);
			
			//Set the center alignment padding + border see css style
			var popMargTop = ($(loginBox).height() + 24) / 2; 
			var popMargLeft = ($(loginBox).width() + 24) / 2; 
			
			$(loginBox).css({ 
				'margin-top' : -popMargTop,
				'margin-left' : -popMargLeft
			});
			
			// Add the mask to body
			$('body').append('<div id="mask"></div>');
			$('#mask').fadeIn(300);
			
			//return false;
}



function logout() {
	
	jQuey.ajax({
		url: '/run/lougis/usersandgroups/logoutUser/'
	}).done(function() { alert("Kirjauduit ulos onnistuneesti"); })
	.fail(function() { alert("Uloskirjautuminen epäonnistui"); });	
	
}


jQuery(function() {
	
	$('#logout_a').click(logout);
	$('a.login-window').click(openModalLogin);
	// When clicking on the button close or the mask layer the popup closed
	$('a.close, #mask').live('click', function() { 
		$('#mask , .login-popup').fadeOut(300 , function() {
			$('#mask').remove();  
			$('#LoginErrorMsg').remove();
			window.location.hash="";
		}); 
	return false;
	});
});
