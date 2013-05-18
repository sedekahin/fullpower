jQuery(document).ready(function($){
	getData('post','publish');	
});
jQuery(function(){	
  var menu_ul = jQuery('.menu > li > ul'),
  menu_a  = jQuery('.menu > li > a'),
  sub_menu = jQuery('.menu > li > ul > li > a');		   
  menu_a.click(function(e){
	e.preventDefault();
	  if(!jQuery(this).hasClass('active')) {
		menu_a.removeClass('active');
		menu_ul.filter(':visible').slideUp('normal');
		jQuery(this).addClass('active').next().stop(true,true).slideDown('normal');
	  } else {
		jQuery(this).removeClass('active');
		jQuery(this).addClass('active').next().stop(true,true).slideUp('normal');
	  }
  });		   
  sub_menu.click(function(e){
	  e.preventDefault();
	  jQuery('.menu > li > ul > li > a').each(function(){
		jQuery(this).removeClass('active');
	  });
	  jQuery(this).addClass('active');
  });	
}); 
function setCounters(){
	var data = {
		action: 'counters_action'
	};
	jQuery.post(ajaxurl, data, function(response){
		var counters = response.split(',');
		jQuery('#post-all-counter').html(counters[0]);
		jQuery('#post-trash-counter').html(counters[1]);
		jQuery('#page-all-counter').html(counters[2]);
		jQuery('#page-trash-counter').html(counters[3]);
		jQuery('#post-duplicated-counter').html(counters[4]);
		jQuery('#page-duplicated-counter').html(counters[5]);	
		var duplicate = parseInt(counters[4]) + parseInt(counters[5]);
		if (duplicate)
		jQuery('.item3 a:first').html('Duplicate Content <span style="background:red;color:#fff;">'+ duplicate +'</span>');	
		else
		jQuery('.item3 a:first').html('Duplicate Content');	
		
	});	
}
function getFilters(){
	var data = {
		action: 'filters_action'		
	};
	jQuery.post(ajaxurl, data, function(ajax) {
		jQuery('.tablenav .Opt7-actions').html(jQuery.trim(ajax));
		jQuery(function(){
			jQuery('#opt7-apply-trash').click(function(){
				if (jQuery('#opt7-action-select').val()!=-1){				
					jQuery('#opt7-data-table tbody tr').each(function(index){
						jQuery(this).find('td:first').each(function(){
							if (jQuery(this).find('input').attr('checked')=='checked'){
								deletePost(jQuery(this).parent().attr('id'));
							}
						});						
					});
				}				
			});
		});
	});
}
function getTrashFilters(){
	var data = {
		action: 'trashfilters_action'
	};
	jQuery.post(ajaxurl, data, function(ajax) {
		jQuery('.tablenav .Opt7-actions').html(ajax);
		jQuery(function(){
			jQuery('#opt7-apply-restore').click(function(){
				if (jQuery('#opt7-action-select').val()!=-1){				
					jQuery('#opt7-data-table tbody tr').each(function(index){
						jQuery(this).find('td:first').each(function(){
							if (jQuery(this).find('input').attr('checked')=='checked'){
								restorePost(jQuery(this).parent().attr('id'));
							}
						});						
					});
				}				
			});
		});	
	});
}
function deletePost(value){
	var data = {
		action: 'deletePost_action',
		postID: value		
	};
	jQuery.post(ajaxurl, data, function(ajax) {
		getAjax();	
	});
}
function restorePost(value){
	var data = {
		action: 'restorePost_action',
		postID: value		
	};
	jQuery.post(ajaxurl, data, function(ajax) {
		getAjax();	
	});
}
function getData(type,status,isDuplicate){
	jQuery('#opt7-post-type-value').val(type);
	jQuery('#opt7-post-status-value').val(status);
	jQuery('#opt7-is-duplicate-value').val(isDuplicate);	
	getAjax();	
}
function loadData(value){
	jQuery('#opt7-page-value').val(value);
	getAjax();
}
function getAjax(){
	jQuery('.right-col').html('<img width="21" height="5" style="vertical-align: middle;border:none;background-color:#FFF;padding:5px;margin-left:10px;" src="../wp-content/plugins/duplicate-posts-remover/images/indicator.gif" class="spinner" alt="Indicator">');
	var data = {
		action: 'my_action',
		page: jQuery('#opt7-page-value').val(),
		limit: jQuery('#opt7-limit-value').val(),
		post_status: jQuery('#opt7-post-status-value').val(),
		post_type: jQuery('#opt7-post-type-value').val(),
		is_for_duplicate: jQuery('#opt7-is-duplicate-value').val(),
		post_search: jQuery('#opt7-post-search-value').val()						
	};
	jQuery.post(ajaxurl, data, function(ajax) {
		setCounters();
		jQuery('.right-col').html(ajax);
		jQuery(function(){
			jQuery('#opt7-per-page').change(function(){
				jQuery('#opt7-limit-value').val(jQuery(this).val());
				getAjax();
			});
		});
		jQuery(function(){
			jQuery('#opt7-remover-cb').change(function(){
				if (jQuery(this).attr('checked')=='checked'){
					jQuery('#opt7-data-table input[type="checkbox"]').each(function(){
						jQuery(this).attr('checked',jQuery('#opt7-remover-cb').attr('checked'));
					});
				}else{
					jQuery('#opt7-data-table input[type="checkbox"]').each(function(){
						jQuery(this).removeAttr('checked');
					});
				}
				
			});
		});
		jQuery(function(){
			jQuery('#opt7-posts-search').bind('keypress', function(e) {
				if(e.which == 13) { //Enter keycode
				   jQuery('#opt7-post-search-value').val(jQuery('#opt7-posts-search').val());
				   getAjax();
				}
			});	
		});		
		if (data.post_status!='trash')
			getFilters();
		else
			getTrashFilters();
	});
}