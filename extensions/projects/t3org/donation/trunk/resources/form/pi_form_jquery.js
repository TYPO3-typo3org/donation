
jQuery(document).ready(function() {
    dform = $('.tx-donation-pi-form form.paypal');
    dform.each(function(){

	$('input[name="donationtype"]',dform).change(function(){
	    if($(this).val()=='onetype'){
		$('div.additional',dform).html('');
		$('input[name="cmd"]').val('_donations');
		$('input[name="bn"]').val('PP-DonationsBF');
		$('input[name="item_name"]',dform).val('TYPO3 One-Time Donation');

	    }
	    if($(this).val()=='subscription'){
		$('div.additional',dform).html(
	'<input type="hidden" name="p3" value="1" />'
	+'<input type="hidden" name="t3" value="M" />'
	+'<input type="hidden" name="src" value="1" />'
	+'<input type="hidden" name="sra" value="1" />'
	);   
	    $('input[name="cmd"]',dform).val('_xclick-subscriptions');
	    $('input[name="bn"]',dform).val('PP-SubscriptionsBF');
	    $('input[name="item_name"]',dform).val('TYPO3 Recuring Donations');
	    }

	});
    })
});


