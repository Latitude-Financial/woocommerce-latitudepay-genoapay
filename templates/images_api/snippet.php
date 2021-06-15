<?php
/**
 * @var WC_LatitudeFinance_Method_Latitudepay $this
 */
?>
<img class="lpay_snippet" src="<?php echo $this->getImagesApiUrl(); ?><?php echo $this->getSnippetPath(); ?>?amount=<?php echo $this->getAmount(); ?><?php if ($this->isFullBlock()): ?>&full_block=1<?php endif; ?><?php if($this->getPaymentTerm()): ?>?term=<?php echo $this->getPaymentTerm(); ?><?php endif; ?>"
     alt="<?php echo $this->getTitle(); ?>"/>

