
$jq(document).ready(function(){	
	// top cart
	(function($jq){
		//show subnav on hover
		$jq('.top-cart-contain').mouseenter(function() {
			$jq(this).find(".top-cart-content").stop(true, true).slideDown();
		});
		//hide submenus on exit
		$jq('.top-cart-contain').mouseleave(function() {
			$jq(this).find(".top-cart-content").stop(true, true).slideUp();
		});
	})($jq);
	// form search
	//(function($jq){
	//	$jq('.search-contain').mouseenter(function() {
	//		$jq(this).find(".search-content").stop(true, true).slideDown();
	//	});
	//	$jq('.search-contain').mouseleave(function() {
	//		$jq(this).find(".search-content").stop(true, true).slideUp();
	//	});
	//})($jq);
	
	// wide menu
	$jq('.wine_menu ul.level0').wrap('<div class="container" />');
	$jq('.wine_menu .container').css("display","none");
	(function($jq){
		//cache nav
		var nav = $jq(".wine_menu");
		//add indicator and hovers to submenu parents
		nav.find("li").each(function() {
				//show subnav on hover
				$jq(this).mouseenter(function() {
					$jq(this).find(".container").stop(true, true).fadeIn();
				});
				
				//hide submenus on exit
				$jq(this).mouseleave(function() {
					$jq(this).find(".container").stop(true, true).fadeOut();
				});
		});
	})($jq);
	


});
