<?php if ( isset( $gateway ) && isset( $cart ) ) : ?>
	<?php
	$price          = $cart->total;
	$containerClass = 'wc-latitudefinance-' . $gateway->get_id() . '-container';
	?>
	<?php if ( $gateway->get_id() === 'genoapay' ) : ?>
		<?php
		$modalFile = __DIR__ . DIRECTORY_SEPARATOR . '../checkout/' . $gateway->get_id() . DIRECTORY_SEPARATOR . 'modal.php';

		$paymentInfo = 'Available now.';

		if ( $price >= 20 && $price <= 1500 ) {
			$weekly      = number_format( round( $price / 10, 2 ), 2 );
			$paymentInfo = "10 weekly payments of <strong>$${weekly}</strong>";
		}

		?>
		<div style="display: inline-block; padding: 5px;" class="<?php echo $containerClass; ?>">
			<a style="text-decoration: none;display: flex;" href="javascript: void(0)"
			   id="<?php echo $gateway->get_id(); ?>-popup">
				<img src="<?php echo WC_LATITUDEPAY_ASSETS . $gateway->get_id() . '.svg'; ?>"
					 style="float: left;padding-right: 5px; max-width: 110px;"/>

				<span style="font-size: 15px;padding-right: 5px;color: rgb(46, 46, 46);">
					<?php echo $paymentInfo; ?>
				</span>

				<span style="color: rgb(49, 181, 156); font-weight: bold; font-size:13px;">learn more</span>
			</a>

			<?php include $modalFile; ?>
		</div>
	<?php elseif ( $gateway->id === WC_LatitudeFinance_Method_Latitudepay::METHOD_LATITUDEPAY ) : ?>
		<div style="display: inline-block; padding: 5px;" class="<?php echo $containerClass; ?>">
			<?php
			/**
			 * @var WC_LatitudeFinance_Method_Latitudepay $gateway
			 */
			$gateway->setAmount( $price );
			echo $gateway->generate_snippet_html();
			?>
		</div>
	<?php endif; ?>
<?php endif; ?>
