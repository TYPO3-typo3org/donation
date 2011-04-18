

var DonationFormHandler = Class.create({

	initialize: function() {

		$$('#bucket-selector .tab, #bucket-selector .tab *').each(function(element) {
			element.observe('click', function(event) {
				tab = event.element();

				if(!tab.hasClassName('tab')) {
					tab = $(Event.element(event)).up('.tab');
				}

				this.activateTab(tab);
			}.bind(this));
		}.bind(this));

		this.activateTab($$('#bucket-selector .tab')[0], false);
		$$('#bucket-selector .tab')[0].addClassName('active');
		this.setAccount();
	},

	activateTab: function(tab, effectsOn) {
		if (typeof effectsOn == 'undefined') {
			effectsOn = true;
		}

		if (!effectsOn) {
			$$('#bucket-selector .tab').each(function(element) {
				element.removeClassName('active');
			});
		}

		if (effectsOn) {
			Effect.BlindUp('buckets', {
				duration: 0.4,
				afterFinish: function() {
					$$('#bucket-selector .tab').each(function(element) {
						element.removeClassName('active');
					});

					tab.addClassName('active');
					this.setAccount();

					Effect.BlindDown('buckets', {
						duration: 0.4,
						afterFinish: function() {
							$('buckets').setStyle({height: '210px'});
						}
					});
				}.bind(this)
			});
		}
	},

	setAccount: function() {
		var accountId    = $$('#bucket-selector .tab.active .account-id')[0].innerHTML;
		var accountEmail = $$('#bucket-selector .tab.active .account-email-paypal')[0].innerHTML;

			// setting account for each bucket
		$('paypal-business').value     = accountEmail;
		$('paypal-sub-business').value = accountEmail;
		$('bankwire-account').value    = accountId;

			// modifying paypal custom field
		$$('#paypal-custom, #paypal-sub-custom').each(function(el) {
			var value = Form.Element.getValue(el);
			var parameters = value.split('|');

			parameters[0] = accountId;
			el.value = parameters.join('|');
		});

	}

});

document.observe('dom:loaded', function() {
	var T3oDonationFormHandler = new DonationFormHandler();
});