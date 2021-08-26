<?php if ( isset( $gateway ) && isset( $cart ) ) : ?>
	<?php
	$price          = $cart->total;
	$containerClass = 'wc-latitudefinance-' . $gateway->get_id() . '-container';
	?>
	<div style="display: inline-block; padding: 5px 0;" class="<?php echo $containerClass; ?>">
		<?php
		/**
		 * @var WC_LatitudeFinance_Method_Latitudepay $gateway
		 */
		$gateway->setAmount( $price );
		echo $gateway->generate_snippet_html();
		?>
	</div>
<?php endif; ?>
