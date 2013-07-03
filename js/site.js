jQuery(document).ready( function($) {

	///////////////////
	// Franchise user table (tr.franchise_users) hidden until button (.display_user_table) pressed
	jQuery('.display_user_table').click( function() {
		var user_table_id = '#row'+jQuery(this).attr('id');		
		jQuery(user_table_id).toggle(1000);
		return false;
	});


	jQuery('.remove_user').click( function($) {
		return confirm('Are you sure you want to remove this user?');
	});
		
	jQuery('.add_user').click( function($) {
		var id = '#add_'+jQuery(this).attr('id');		
		jQuery(id).toggle(1000);
		jQuery(this).hide();
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
