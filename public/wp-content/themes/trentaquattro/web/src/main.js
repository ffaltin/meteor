#include "../lib/jquery/jquery-1.10.2.js"
#include "../lib/scrollto.js"
#include "../lib/owl/owl.carousel.js"
// #include "../../lib/FormStone/Scripts.js"

$.fn.hasAttr = function(name) {  
   return this.attr(name) !== undefined;
};

var app = {
	init: function() {
		var $windowHeight = $(window).height() * 0.85;
		$("#restaurant > header").css({minHeight:  $windowHeight > 650? $windowHeight : 650 });

		$('.page').each(function(){
			var $that = $(this),
			$header = $that.children("header"),
			$vCenter = $header.children(".vcenter");
			$vCenter.css({
				paddingTop: ($header.height() - $vCenter.height()) / 2
			});
		});

		$(".main-nav.prevent").on("click", "a", function(e){
			e.preventDefault();
			$(window).scrollTo("#"+$(this).parent().attr("data-page"), 500);
		});

		/* Create simple tabulator system */
		var $tabulatorHeader = $(document.createElement("ul"));
		$tabulatorHeader.addClass("tabulator-header");

		$('.has-tabulator').find("*[data-tab]").each(function(i){
			var $li = $(document.createElement("li")),
			$a = $(document.createElement("a"));
			$a.html($(this).attr("data-tab"));
			$a.attr("href", "#" + $(this).attr("data-tab-id"));
			if (i === 0) $a.addClass("current");
			$li.append($a)
			$tabulatorHeader.append($li);
		});

		$tabulatorHeader.on("click", "a" , function(e){
			e.preventDefault();
			$('.has-tabulator').find(".tabulator-content").fadeOut(250);
			$("*[data-tab-id="+$(this).attr("href").replace("#","")+"]").fadeIn(250);
			$tabulatorHeader.find("a").removeClass("current");
			$(this).addClass("current");
		});

		$('.has-tabulator').prepend($tabulatorHeader);
		$('.has-tabulator').find(".tabulator-content").hide();
		$('.has-tabulator').find(".tabulator-content.active").show();

		/* Add Carousel */
		$(".owl-carousel").each(function(){
			$(this).owlCarousel({
				navigation : false, 
				slideSpeed : 300,
				paginationSpeed : 400,
				singleItem:true,
				autoHeight: true
			});
		})
	}
};

jQuery(function($) {
	$(document).ready(app.init());
});
