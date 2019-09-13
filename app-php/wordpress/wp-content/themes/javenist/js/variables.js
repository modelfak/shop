		var javenist_brandnumber = 6,
			javenist_brandscrollnumber = 2,
			javenist_brandpause = 3000,
			javenist_brandanimate = 2000;
		var javenist_brandscroll = false;
							javenist_brandscroll = true;
					var javenist_categoriesnumber = 6,
			javenist_categoriesscrollnumber = 2,
			javenist_categoriespause = 3000,
			javenist_categoriesanimate = 2000;
		var javenist_categoriesscroll = false;
					var javenist_blogpause = 3000,
			javenist_bljavenistmate = 2000;
		var javenist_blogscroll = false;
							javenist_blogscroll = true;
					var javenist_testipause = 3000,
			javenist_testianimate = 2000;
		var javenist_testiscroll = false; 
							javenist_testiscroll = false;
					var javenist_catenumber = 6,
			javenist_catescrollnumber = 2,
			javenist_catepause = 3000,
			javenist_cateanimate = 700;
		var javenist_catescroll = false;
					var javenist_menu_number = 10; 

		var javenist_sticky_header = false;
							javenist_sticky_header = true;
			
		var javenist_item_first = 12,
			javenist_moreless_products = 4;

		jQuery(document).ready(function(){
			jQuery("#ws").focus(function(){
				if(jQuery(this).val()=="Search product..."){
					jQuery(this).val("");
				}
			});
			jQuery("#ws").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("Search product...");
				}
			});
			jQuery("#wsearchsubmit").click(function(){
				if(jQuery("#ws").val()=="Search product..." || jQuery("#ws").val()==""){
					jQuery("#ws").focus();
					return false;
				}
			});
			jQuery("#search_input").focus(function(){
				if(jQuery(this).val()=="Search..."){
					jQuery(this).val("");
				}
			});
			jQuery("#search_input").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("Search...");
				}
			});
			jQuery("#blogsearchsubmit").click(function(){
				if(jQuery("#search_input").val()=="Search..." || jQuery("#search_input").val()==""){
					jQuery("#search_input").focus();
					return false;
				}
			});
		});
		