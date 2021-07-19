<div class="g-infomodal-container" id="g-infomodal-container" style="display: none;">
	<div class="g-infomodal-content">
		<img id="g-infomodal-close" class="g-infomodal-close" src="<?php echo WC_LATITUDEPAY_ASSETS . 'genoapay/close_btn_gen_green.svg'; ?>">
		<div class="g-infomodal-inner">
			<div class="g-modal-header">
				<img class="g-infomodal-logo" src="<?php echo WC_LATITUDEPAY_ASSETS . 'genoapay/genoapay_logo_white.svg'; ?>">
				<span>Pay over 10 weeks.<br>No interest, no fees.</span>
			</div>
			<div class="g-infomodal-body">
				<div class="g-infomodal-card-group">
					<div class="g-infomodal-card">
						<div class="g-infomodal-card-content">
							<img src="<?php echo WC_LATITUDEPAY_ASSETS . 'genoapay/shopping_trolly_icon.svg'; ?>">
						</div>
						<div class="g-infomodal-card-footer">
							<div class="g-infomodal-card-title"><span>Checkout with </span><span>Genoapay</span></div>
						</div>
					</div>
					<div class="g-infomodal-card">
						<div class="g-infomodal-card-content">
							<img src="<?php echo WC_LATITUDEPAY_ASSETS . 'genoapay/thin_tick_icon.svg'; ?>">
						</div>
						<div class="g-infomodal-card-footer">
							<div class="g-infomodal-card-title"><span>Credit approval </span><span>in seconds</span></div>
						</div>
					</div>
					<div class="g-infomodal-card">
						<div class="g-infomodal-card-content">
							<img src="<?php echo WC_LATITUDEPAY_ASSETS . 'genoapay/get_it_now_icon.svg'; ?>">
						</div>
						<div class="g-infomodal-card-footer">
							<div class="g-infomodal-card-title"><span>Get it now, </span><span>pay over 10 weeks</span></div>
						</div>
					</div>
				</div>
				<p>That's it! We manage automatic weekly payments until you're paid off. Full purchase details can be viewed anytime online.</p>
				<hr>
				<p>You will need</p>
				<ul class="g-infomodal-list">
					<li>To be over 18 years old</li>
					<li>Visa/Mastercard payment</li>
					<li>NZ drivers licence or passport</li>
					<li>First instalment paid today</li>
				</ul>
				<div class="g-infomodal-terms">Learn more about <a href="https://www.genoapay.com/how-it-works/" target="_blank">how it works</a>. Credit criteria applies. Weekly payments will be automatically deducted. Failed instalments incur a $10 charge. See our <a href="https://www.genoapay.com/terms-and-conditions/" target="_blank">Terms & Conditions</a> for more information.</div>
			</div>
		</div>
	</div>
</div>

<script>
	// Pure JS just in case if the merchant website is not using jQuery
	;(function() {
		var popupTrigger = document.getElementById('genoapay-popup'),
			popup        = document.getElementById('g-infomodal-container'),
			closeBtn     = document.getElementById('g-infomodal-close');

		function openPopup(element) {
		  element.style.display = 'block';
		}

		function closePopup(element) {
			element.style.display = 'none';
		}

		popupTrigger.addEventListener('click', function(event) {
			// prevent default
			event.preventDefault();
			event.stopImmediatePropagation();
			document.body.appendChild(popup);
			// popup the genoapay HTML
			openPopup(popup);
		});

		closeBtn.addEventListener('click', function() {
			closePopup(popup);
		});
	})();
</script>
