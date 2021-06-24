<?php
/**
 * @var WC_LatitudeFinance_Method_Latitudepay $this
 */
?>

<?php
    $paymentTerm = $this->getPaymentTerm();
    $paymentTerm = ($paymentTerm && is_array($paymentTerm) && !empty($paymentTerm)) ?
        '&term=' . implode(',', $paymentTerm) :
        '';
    $snippetUrl = $this->getImagesApiUrl() . $this->getSnippetPath();
    $fullBlock = $this->isFullBlock() ? '&full_block=1' : '';
?>
<img class="lpay_snippet" src="<?php echo $snippetUrl ?>?amount=<?php echo $this->getAmount(); ?><?php echo $fullBlock; ?><?php echo $paymentTerm; ?>"
     alt="<?php echo $this->getTitle(); ?>"/>

