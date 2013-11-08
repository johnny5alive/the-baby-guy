<?php
    header('Content-type: text/css; charset: UTF-8');
    header('Cache-Control: must-revalidate');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
    $url = $_REQUEST['url'];
?>
.pager .view-mode a.grid,
.pager .view-mode strong.grid,
.pager .view-mode a.grid:hover,
.pager .view-mode a.list,
.pager .view-mode strong.list,
.pager .view-mode a.list:hover,
.pager .sort-by select,
.pager .limiter select
{
	-moz-box-shadow: 0 0 3px #DDDDDD;
	-webkit-box-shadow: 0 0 3px #DDDDDD;
	box-shadow: 0 0 3px #DDDDDD;
}


button.btn-cart span,
.products-grid .actions .link-wishlist,
.products-grid .actions .link-compare,
.products-grid .actions .product-detail a,
.products-grid .item-inner:hover .actions,
.add-to-cart input.qty-decrease,
.add-to-cart input.qty-increase,
.ma-thumbnail-container .flex-direction-nav a,
#back-top,
.product-prev,
.product-next
{
	-webkit-transition: 0.5s;
	-moz-transition: 0.5s;
	transition: 0.5s;
}
.ma-banner7-container .flex-control-paging li a
{
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border-radius: 8px;
	behavior: url(<?php echo $url; ?>css/css3.htc);
}
#back-top
{
	-webkit-border-radius: 25px;
	-moz-border-radius: 25px;
	border-radius: 25px;
	behavior: url(<?php echo $url; ?>css/css3.htc);
}