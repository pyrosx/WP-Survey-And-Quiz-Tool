jQuery(document).ready( function($) {

	///////////////////
	// Franchise user table (tr.franchise_users) hidden until button (.display_user_table) pressed
	jQuery('.display_user_table').click( function($) {
		var user_table_id = '#row'+jQuery(this).attr('id');
		if (jQuery(this).attr('value') == "+") {
			jQuery(this).attr('value',"-");
		} else {
			jQuery(this).attr('value',"+");
		}
		
		
		var store_id = jQuery(this).attr('id').substring(6);
		var table_store_id = '#table'+jQuery(this).attr('id');

		if (jQuery(table_store_id).html() == "") {
			
			// nothing in table - load it!
			jQuery(table_store_id).load(l10n.plugin_url+"Page/Dashboard/Lookup.php?id_store="+store_id, {noncache: new Date().getTime()}, function($) {
				jQuery(user_table_id).toggle();		
	
				// Need to specifially define these scripts so that they work for the new content
				jQuery(table_store_id+' .remove_user').click( function($) {
					return confirm('Are you sure you want to remove this user?');
				});
				jQuery(table_store_id+' .remove_store').click( function($) {
					return confirm('Are you sure you want to remove this store?');
				});
					
				jQuery(table_store_id+' .add_user').click( function($) {
					var id = '#add_'+jQuery(this).attr('id');	
					jQuery(id).toggle();
					jQuery(this).hide();
					return false;
				});
				
				jQuery(table_store_id+' .add_user_form').submit( function($) {
				
					name_input = jQuery(this).find('input[name=new_name]').val();
				
					if (name_input.indexOf(" ") == -1) { 
						// name contains no spaces
						return confirm('Are you sure this is the correct full name?\r\nThe name entered contains no spaces\r\nThis name will be printed on the Certificate');
					
					}
				});
			});
		} else {
			jQuery(table_store_id).toggle();
		}
		
		return false;
	});


	jQuery('.remove_user').click( function($) {
		return confirm('Are you sure you want to remove this user?');
	});
	jQuery('.remove_store').click( function($) {
		return confirm('Are you sure you want to remove this store?');
	});
		
	jQuery('.add_user').click( function($) {
		var id = '#add_'+jQuery(this).attr('id');	
		jQuery(id).toggle();
		jQuery(this).hide();
		return false;
	});
	
	jQuery('.add_user_form').submit( function($) {
	
		name_input = jQuery(this).find('input[name=new_name]').val();
	
		if (name_input.indexOf(" ") == -1) { 
			// name contains no spaces
			return confirm('Are you sure this is the correct full name?\r\nThe name entered contains no spaces\r\nThis name will be printed on the Certificate');
		
		}
	});


	////////////////////////
	// Bulk Email
	
	jQuery('.toradio').click( function() {
		if (jQuery(this).attr("id") == "toall" || jQuery(this).attr("id") == "tofranchisees" ) {
			// Standard reminders not appropriate for ALL options
			
			jQuery('#bodystandard').attr("disabled", true);
			jQuery('#bodystandardlabel').addClass("disabled");
			jQuery('#bodycustom').click();
			
		} else {
			// reenable options removed above...
			jQuery('#bodystandard').attr("disabled", false);
			jQuery('#bodystandardlabel').removeClass("disabled");				
		}
	});
	// custom section, disabled when standard is selected
	jQuery('#bodystandard').click( function() {
		jQuery('#customsection').addClass("disabled");
		jQuery('.custominput').addClass("disabled");
		jQuery('.custominput').removeAttr("required");
	});
	// and reenabled when custom is selected
	jQuery('#bodycustom').click( function() {
		jQuery('#customsection').removeClass("disabled");
		jQuery('.custominput').removeClass("disabled");
		jQuery('.custominput').attr("required","true");
	});
	// no need to repeat the disabled stuff anywhere else... just manually "click"	
	jQuery('#bodystandard').click();

	// send button - confirm
	jQuery('#bulkemailsend').click( function() {
		return confirm ("Are you sure? "+$('input[name=toradio]:checked').attr("jqcount")+" emails will be sent immediately, and this cannot be undone");
	});
	
	// fail detail panel show
	jQuery('#failDetailsLink').click( function() {
		jQuery('#failDetails').toggle();
		return false;
	});

	jQuery('.wpsqt-show-answer').click( function() {
		jQuery(this).siblings('.wpsqt-answer-explanation').show();
		jQuery(this).hide();
		return false;
	});
	jQuery('.wpsqt-show-toggle').click( function() {
		jQuery(this).siblings('.wpsqt-toggle-block').show();
		jQuery(this).siblings('.wpsqt-hide-toggle').show();
		jQuery(this).hide();
		return false;
	});
	jQuery('.wpsqt-hide-toggle').click( function() {
		jQuery(this).siblings('.wpsqt-toggle-block').hide();
		jQuery(this).siblings('.wpsqt-show-toggle').show();
		jQuery(this).hide();
		return false;
	});
	jQuery('.wpst_question input, .wpst_question textarea').click( function() {
		var explanationText = jQuery(this).parents('.wpst_question').children('.wpsqt-answer-explanation:hidden');

		if (explanationText.length != 0) {
			jQuery(explanationText).siblings('.wpsqt-show-answer').show();
		}
	});
});
