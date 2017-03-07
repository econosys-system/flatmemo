// jQuery.target_blank.js
// copyright (c) econosys system    http://flatsystems.net/econosys_system/
// Version 1.00

jQuery(document).ready( function () {
	$("a[href^='http']:not([href*='" + location.hostname + "'])").attr('target', '_blank');
})
