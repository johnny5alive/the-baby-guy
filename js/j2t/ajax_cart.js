/**
 * J2T-DESIGN.
 *
 * @category   J2t
 * @package    J2t_Ajaxcheckout
 * @copyright  Copyright (c) 2003-2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    GPL
 */

var loadingW = 260;
var loadingH = 50;
var confirmW = 260;
var confirmH = 134;

var inCart = false;

if (window.location.toString().search('/product_compare/') != -1){
	var win = window.opener;
}
else{
	var win = window;
}

if (window.location.toString().search('/checkout/cart/') != -1){
    inCart = true;
}

function setLocation(url){
    if(!inCart && ((url.search('/add') != -1 ) || (url.search('/remove') != -1 ) || url.search('checkout/cart/add') != -1) ){
        sendcart(url, 'url');
    }else{
        window.location.href = url;
    }
}


function sendcart(url, type){
    showLoading();
    if (type == 'form'){
        url = ($('product_addtocart_form').action).replace('checkout', 'j2tajaxcheckout/index/cart');
        var myAjax = new Ajax.Request(
        url,
        {
            method: 'post',
            postBody: $('product_addtocart_form').serialize(),
            parameters : Form.serialize("product_addtocart_form"),
            onException: function (xhr, e)
            {
                alert('Exception : ' + e);
            },
            onComplete: function (xhr)
            {

                var start3 = xhr.responseText.indexOf('<div class="j2t_ajax_message">')+30;
                var end3 = xhr.responseText.indexOf("<span>j2t_ajax_auto_add</span></div>",start3);
                var return_message = xhr.responseText.substring(start3, end3);

                var start= xhr.responseText.indexOf('<div id="back-ajax-add">')+24;
                var end= xhr.responseText.indexOf("</div>",start);
                $('j2t_ajax_confirm').innerHTML = return_message + xhr.responseText.substring(start,end);
                var start2 = xhr.responseText.indexOf('<div id="cart_content">')+23;
                var end2= xhr.responseText.indexOf("</div>",start2);
                $$('.top-link-cart').each(function (el){
                    el.innerHTML = xhr.responseText.substring(start2,end2);
                });

                var start4 = xhr.responseText.indexOf('<div class="cart_side_ajax">')+28;
                var end4 = xhr.responseText.indexOf("<span>j2t_ajax_auto_add</span></div>",start4);

                $$('.mini-cart').each(function (el){
                    el.replace(xhr.responseText.substring(start4,end4));
                    //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
                });
                $$('.block-cart').each(function (el){
                    el.replace(xhr.responseText.substring(start4,end4));
                    //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
                });

                replaceDelUrls();

                if (ajax_cart_show_popup){
                    showConfirm();
                } else {
                    hideJ2tOverlay();
                }

            }

        });



    } else if (type == 'url'){

        url = url.replace('checkout', 'j2tajaxcheckout/index/cart');
        //alert(url);
        var myAjax = new Ajax.Request(
        url,
        {
            method: 'post',
            postBody: '',
            onException: function (xhr, e)
            {
                alert('Exception : ' + e);
            },
            onComplete: function (xhr)
            {
                //alert(xhr.responseText);
                var start3 = xhr.responseText.indexOf('<div class="j2t_ajax_message">')+30;
                var end3 = xhr.responseText.indexOf("<span>j2t_ajax_auto_add</span></div>",start3);
                var return_message = xhr.responseText.substring(start3, end3);


                var start= xhr.responseText.indexOf('<div id="back-ajax-add">')+24;
                var end= xhr.responseText.indexOf("</div>",start);
                $('j2t_ajax_confirm').innerHTML = return_message + xhr.responseText.substring(start,end);
                var start2 = xhr.responseText.indexOf('<div id="cart_content">')+23;
                var end2= xhr.responseText.indexOf("</div>",start2);
                $$('.top-link-cart').each(function (el){
                    el.innerHTML = xhr.responseText.substring(start2,end2);
                });


                var start4 = xhr.responseText.indexOf('<div class="cart_side_ajax">')+28;
                var end4 = xhr.responseText.indexOf("<span>j2t_ajax_auto_add</span></div>",start4);

                $$('.mini-cart').each(function (el){
                    el.replace(xhr.responseText.substring(start4,end4));
                    //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
                });

                $$('.block-cart').each(function (el){
                    el.replace(xhr.responseText.substring(start4,end4));
                    //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
                });

                replaceDelUrls();
                if (ajax_cart_show_popup){
                    showConfirm();
                } else {
                    hideJ2tOverlay();
                }
            }

        });

    }

}

function replaceDelUrls(){
    if (!inCart){
        $$('a').each(function(el){
            if(el.href.search('checkout/cart/delete') != -1){
                el.href = 'javascript:cartdelete(\'' + el.href +'\')';
            }
        });
    }
}

function replaceAddUrls(){
    $$('a').each(function(link){
        if(link.href.search('checkout/cart/add') != -1){
            link.href = 'javascript:setLocation(\''+link.href+'\'); void(0);';
        }
    });
}

function cartdelete(url){
    showLoading();
    url = url.replace('checkout', 'j2tajaxcheckout/index/cart');
    var myAjax = new Ajax.Request(
    url,
    {
        method: 'post',
        postBody: '',
        onException: function (xhr, e)
        {
            alert('Exception : ' + e);
        },
        onComplete: function (xhr)
        {


            var start2 = xhr.responseText.indexOf('<div id="cart_content">')+23;
            var end2= xhr.responseText.indexOf("</div>",start2);
            $$('.top-link-cart').each(function (el){
                el.innerHTML = xhr.responseText.substring(start2,end2);
            });

            var start4 = xhr.responseText.indexOf('<div class="cart_side_ajax">')+28;
            var end4 = xhr.responseText.indexOf("<span>j2t_ajax_auto_add</span></div>",start4);
            $$('.mini-cart').each(function (el){
                el.replace(xhr.responseText.substring(start4,end4));
                //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
            });
            $$('.block-cart').each(function (el){
                el.replace(xhr.responseText.substring(start4,end4));
                //new Effect.Opacity(el, { from: 0, to: 1, duration: 1.5 });
            });

            replaceDelUrls();

            //$('j2t_ajax_progress').hide();
            hideJ2tOverlay();
        }

    });


}

function showJ2tOverlay(){
    new Effect.Appear($('j2t-overlay'), { duration: 0.5,  to: 0.8 });
}

function hideJ2tOverlay(){
    $('j2t-overlay').hide();
    $('j2t_ajax_progress').hide();
    $('j2t_ajax_confirm').hide();
}


function j2tCenterWindow(element) {
     if($(element) != null) {

          // retrieve required dimensions
            var el = $(element);
            var elDims = el.getDimensions();
            var browserName=navigator.appName;
            if(browserName==="Microsoft Internet Explorer") {

                if(document.documentElement.clientWidth==0) {
                    //IE8 Quirks
                    //alert('In Quirks Mode!');
                    var y=(document.viewport.getScrollOffsets().top + (document.body.clientHeight - elDims.height) / 2);
                    var x=(document.viewport.getScrollOffsets().left + (document.body.clientWidth - elDims.width) / 2);
                }
                else {
                    var y=(document.viewport.getScrollOffsets().top + (document.documentElement.clientHeight - elDims.height) / 2);
                    var x=(document.viewport.getScrollOffsets().left + (document.documentElement.clientWidth - elDims.width) / 2);
                }
            }
            else {
                // calculate the center of the page using the browser andelement dimensions
                var y = Math.round(document.viewport.getScrollOffsets().top + ((window.innerHeight - $(element).getHeight()))/2);
                var x = Math.round(document.viewport.getScrollOffsets().left + ((window.innerWidth - $(element).getWidth()))/2);
            }
            // set the style of the element so it is centered
            var styles = {
                position: 'absolute',
                top: y + 'px',
                left : x + 'px'
            };
            el.setStyle(styles);




     }
}

function showLoading(){
    showJ2tOverlay();
    var progress_box = $('j2t_ajax_progress');
    progress_box.show();
    progress_box.style.width = loadingW + 'px';
    progress_box.style.height = loadingH + 'px';
    progress_box.style.position = 'absolute';

    j2tCenterWindow(progress_box);
}


function showConfirm(){
    $('j2t_ajax_progress').hide();
    var confirm_box = $('j2t_ajax_confirm');
    confirm_box.show();
    confirm_box.style.width = confirmW + 'px';
    confirm_box.style.height = confirmH + 'px';

    confirm_box.style.position = 'absolute';

    j2tCenterWindow(confirm_box);

}

document.observe("dom:loaded", function() {
    replaceDelUrls();
    replaceAddUrls();
    //Event.observe($('j2t-overlay'), 'click', hideJ2tOverlay);

    var cartInt = setInterval(function(){
        if (typeof productAddToCartForm  != 'undefined'){
            if ($('j2t-overlay')){
                Event.observe($('j2t-overlay'), 'click', hideJ2tOverlay);
            }
            productAddToCartForm.submit = function(url){
                if(this.validator && this.validator.validate()){
                    sendcart('', 'form');
                    clearInterval(cartInt);
                }

                return false;
            }
        } else {
            clearInterval(cartInt);
        }
    },500);
});
