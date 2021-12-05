var base_url = $('#base_url').attr('class');
function reset_time_out(res) {
	/*var chk_time_out = '';
	clearTimeout(chk_time_out);	
	let startDate = localStorage.getItem('auth_time_check');

	// chk_time_out = setTimeout(function(){ location.href = base_url+'main_menu/logout?res='+res; }, 60  60  1000);
	chk_time_out = setTimeout(function(){
											var currentDate = new Date();
											var pass_sec = (currentDate.getTime() - startDate)  / 1000;

											if(pass_sec > 360) {
												location.href = base_url+'main_menu/logout?res='+res;
											} else {
												reset_time_out(res);
											}
										}, 1000 * 60 * 1000);
	*/									
}

function set_active_cache() {
	//var startDate = new Date();
	//localStorage.setItem('auth_time_check',startDate.getTime())
}

$( document ).ready(function() {
	/*var res = window.location.pathname;

	if(res != '/finance/finance_month'){
		set_active_cache();
		reset_time_out(res);			

		document.onclick = function(e){set_active_cache(); reset_time_out(res);};
		document.onmousemove = function(e){set_active_cache(); reset_time_out(res);};
		document.onkeydown = function(e){set_active_cache(); reset_time_out(res);};
		document.onscroll = function(e){set_active_cache(); reset_time_out(res);};
	}
	*/
});	