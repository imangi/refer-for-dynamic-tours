/**
*
* JavaScript file that has global action in the admin menu
*
*/
(function($){

	"use strict";

	$( document ).ready(function() {

		// Stripe gateway hide charge type 3d Secure
		// On load, show hide user choices 
		showHideChargeType();

		$('#woocommerce_marketking_stripe_gateway_enable_3d_secure').change(showHideChargeType);

		function showHideChargeType(){
			let selectedValue = $('#woocommerce_marketking_stripe_gateway_enable_3d_secure').is(':checked');
			if (selectedValue){
				// hide charge type
				$('label[for="woocommerce_marketking_stripe_gateway_charge_type"]').parent().parent().css('display','none');
			} else {
				// show charge type
				$('label[for="woocommerce_marketking_stripe_gateway_charge_type"]').parent().parent().css('display','contents');
			}
		}

		updateAddToBilling();
		$('#marketking_field_billing_connection_metabox_select').change(updateAddToBilling);
		// Billing field connection, show add to billing only if default connection is none
		function updateAddToBilling(){
			let billingConnectionSelected = $('#marketking_field_billing_connection_metabox_select').val();
			if (billingConnectionSelected === 'none' || billingConnectionSelected === 'billing_vat'){
				$('.marketking_add_to_billing_container').css('display', '');
			} else {
				$('.marketking_add_to_billing_container').css('display', 'none');
			}

			// Show VAT container only if selected billing connection is VAT
			if (billingConnectionSelected === 'billing_vat'){
				$('.marketking_VAT_container').css('display', 'flex');
			} else {
				$('.marketking_VAT_container').css('display', 'none');
			}

			if (billingConnectionSelected === 'custom_mapping'){
				$('.marketking_custom_mapping_container').css('display', 'flex');
			}  else {
				$('.marketking_custom_mapping_container').css('display', 'none');
			}
		}

		// pending review backend product, add message vendor button
		// Check if the body has the class "post-type-product"
	    if ($('body').hasClass('post-type-product')) {
	        // Check if #post-status-display contains 'pending' (case-insensitive)
	        
	            // Both conditions are met, add the button

	            var urlParams = new URLSearchParams(window.location.search);
                var productid = urlParams.get('post');

	            var button = $('<a>', {
	                target: '_blank',
	                href: 'post-new.php?post_type=marketking_message&vendor='+jQuery('#marketking_set_product_author').val()+'&product='+productid,
	                class: 'button marketking_message_vendor_pending'
	            }).html('<span class="dashicons dashicons-format-chat"></span>Message vendor');

	            $('.misc-pub-section.misc-pub-post-status').after(button);
	        
	    }
	    // Check if the body has the class "post-type-marketking_message"
        if ($('body').hasClass('post-type-marketking_message')) {
            // Get the 'vendor' parameter from the URL
            var urlParams = new URLSearchParams(window.location.search);
            var vendorParam = urlParams.get('vendor');
            var productParam = urlParams.get('product');

            // If 'vendor' parameter is set
            if (vendorParam) {
                // Set the value of the select input
                $('#marketking_message_user_input').val(vendorParam);

                // Set the message in the textarea
                $('input[name="post_title"]').val(marketking.product_sub_message+productParam).trigger('input');
                $('#marketking_message_start_message').val(marketking.product_sub_message+productParam+':').trigger('input');
            }
        }

		// On load, show hide user choices 
		showHideUserChoices();

		$('.marketking_field_settings_metabox_bottom_field_type_select').change(showHideUserChoices);

		function showHideUserChoices(){
			let selectedValue = $('.marketking_field_settings_metabox_bottom_field_type_select').val();
			if (selectedValue === 'select' || selectedValue === 'checkbox'){
				$('.marketking_field_settings_metabox_bottom_user_choices').css('display','block');
			} else {
				$('.marketking_field_settings_metabox_bottom_user_choices').css('display','none');
			}
		}

		// On clicking the "Add user button in the Product Category User Visibility table"
		$("#marketking_category_add_user").on("click",function(){
			// Get username
			let username = $("#marketking_all_users_dropdown").children("option:selected").val();
			// Get content and check if username already exists
			let content = $("#marketking_category_users_textarea").val();
			let usersarray = content.split(',');
			let exists = 0;

			$.each( usersarray, function( i, val ) {
				if (val.trim() === username){
					exists = 1;
				}
			});

			if (exists === 1){
				// Show "Username already in the list" for 3 seconds
				$("#marketking_category_add_user").text(marketking.username_already_list);
				setTimeout(function(){
					$("#marketking_category_add_user").text(marketking.add_user);
				}, 2000);

			} else {
				// remove last comma and whitespace after
				content = content.replace(/,\s*$/, "");
				// if list is not empty, add comma
				if (content.length > 0){
					content = content + ', ';
				}
				// add username
				content = content + username;
				$("#marketking_category_users_textarea").val(content);
			}
		});


		

 
	});

})(jQuery);