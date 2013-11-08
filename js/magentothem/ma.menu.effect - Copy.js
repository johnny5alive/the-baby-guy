$jq.fn.effect = function(options) {
	options = $jq.extend({ 
		animation: "slide",  
		mm_timeout: 200
	}, options);
	var $effect_object = this; 
	$effect_object.find("li.parent").each(function(){  
		var $mm_item = $jq(this).children('div'); 
		$mm_item.hide();  
		var $timer = 0; 
		$jq(this).bind('mouseenter', function(e){ 
			var mm_item_obj = $jq(this).children('div'); 
			clearTimeout($timer);
			$timer = setTimeout(function(){  
				switch(options.animation) {
					case "show":
						mm_item_obj.show().addClass("shown-sub");
						break;
					case "slide":
						mm_item_obj.height("auto");
						mm_item_obj.slideDown('fast').addClass("shown-sub");
						break;
					case "fade":
						mm_item_obj.fadeTo('fast', 1).addClass("shown-sub");
						break; 
				}
			}, options.mm_timeout);	
		}); 
		$jq(this).bind('mouseleave', function(e){ 
			clearTimeout($timer); 
			var mm_item_obj = $jq(this).children('div'); 
			switch(options.animation) {
				case "show":
					  mm_item_obj.hide(); 
					  break;
				case "slide":  
					  mm_item_obj.slideUp( 'fast',  function() { 
					  });
					  break;
				case "fade":
					  mm_item_obj.fadeOut( 'fast', function() { 
					  });
					  break;  
              break;
			} 
		}); 
	}); 
	this.show();
};

$jq.fn.effect1 = function(options) {
	options = $jq.extend({ 
		animation: "slide",  
		mm_timeout: 200
	}, options);
	var $effect1_object = this; 
	$effect1_object.find("li.parent").each(function(){  
		var $mm_item = $jq(this).children('ul'); 
		$mm_item.hide();  
		var $timer = 0; 
		$jq(this).bind('mouseenter', function(e){ 
			var mm_item_obj = $jq(this).children('ul'); 
			clearTimeout($timer);
			$timer = setTimeout(function(){  
				switch(options.animation) {
					case "show":
						mm_item_obj.show().addClass("shown-sub");
						break;
					case "slide":
						mm_item_obj.height("auto");
						mm_item_obj.slideDown('fast').addClass("shown-sub");
						break;
					case "fade":
						mm_item_obj.fadeTo('fast', 1).addClass("shown-sub");
						break; 
				}
			}, options.mm_timeout);	
		}); 
		$jq(this).bind('mouseleave', function(e){ 
			clearTimeout($timer); 
			var mm_item_obj = $jq(this).children('ul'); 
			switch(options.animation) {
				case "show":
					  mm_item_obj.hide(); 
					  break;
				case "slide":  
					  mm_item_obj.slideUp( 'fast',  function() { 
					  });
					  break;
				case "fade":
					  mm_item_obj.fadeOut( 'fast', function() { 
					  });
					  break;  
              break;
			} 
		}); 
	}); 
	this.show();
};
$jq(document).ready(function(){
	$jq('.wine_menu ul.level0').wrap('<div class="container" />');
    $jq(function(){ 
        $jq(".fish_menu").effect1({
            'animation':'slide', 
            'mm_timeout': 200
        });
        $jq(".wine_menu").effect({
            'animation':'fade', 
            'mm_timeout': 200
        }); 
    });     
});

//----------- top cart  ------------

$jq.fn.effect2 = function(options) {
	options = $jq.extend({ 
		animation: "slide",  
		mm_timeout: 200
	}, options);
	var $effect2_object = this; 
	$effect2_object.find(".top-cart-contain").each(function(){  
		var $mm_item = $jq(this).children('.top-cart-content'); 
		$mm_item.hide();  
		var $timer = 0; 
		$jq(this).bind('mouseenter', function(e){ 
			var mm_item_obj = $jq(this).children('.top-cart-content'); 
			clearTimeout($timer);
			$timer = setTimeout(function(){  
				switch(options.animation) {
					case "slide":
						mm_item_obj.height("auto");
						mm_item_obj.slideDown('fast').addClass("shown-cart");
						break;
					case "fade":
						mm_item_obj.fadeTo('fast', 1).addClass("shown-cart");
						break; 
				}
			}, options.mm_timeout);	
		}); 
		$jq(this).bind('mouseleave', function(e){ 
			clearTimeout($timer); 
			var mm_item_obj = $jq(this).children('.top-cart-content'); 
			switch(options.animation) {
				case "show":
					  mm_item_obj.hide(); 
					  break;
				case "slide":  
					  mm_item_obj.slideUp( 'fast',  function() { 
					  });
					  break;
				case "fade":
					  mm_item_obj.fadeOut( 'fast', function() { 
					  });
					  break;  
              break;
			} 
		}); 
	}); 
	this.show();
};

$jq(document).ready(function(){
    $jq(function(){ 
        $jq(".top-cart-wrap").effect2({
            'animation':'slide', 
            'mm_timeout': 200
        }); 
    });     
});
