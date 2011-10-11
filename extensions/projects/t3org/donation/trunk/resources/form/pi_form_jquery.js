
jQuery(document).ready(function() {
    dform = $('.tx-donation-pi-form form.paypal');
    $('form.paypal div.paypal-checkboxes').prepend('<div class="b-form-row"><label>One Time Donation</label><div class="b-form-inputs"><input type="radio" name="donationtype" value="onetype"/></div></div>');

    dform.each(function(){

	$('input[name="donationtype"]',dform).change(function(){
	    if($(this).val()=='onetype'){
		$('div.additional',dform).html('');
		$('input[name="cmd"]').val('_donations');
		$('input[name="bn"]').val('PP-DonationsBF');
		$('input[name="item_name"]',dform).val('TYPO3 One-Time Donation');
		$('input[name="a3"]',dform).attr('name','amount');
	    }
	    if($(this).val()=='subscription'){
		$('div.additional',dform).html(
	'<input type="hidden" name="p3" value="1" />'
	+'<input type="hidden" name="t3" value="M" />'
	+'<input type="hidden" name="src" value="1" />'
	+'<input type="hidden" name="sra" value="1" />'
	);   
	    paypal
	    $('input[name="amount"]',dform).attr('name','a3');
	    $('input[name="cmd"]',dform).val('_xclick-subscriptions');
	    $('input[name="bn"]',dform).val('PP-SubscriptionsBF');
	    $('input[name="item_name"]',dform).val('TYPO3 Recuring Donations');
	    }

	});
    })
});


