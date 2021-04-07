<?php
/**
 * @var WC_LatitudeFinance_Method_Latitudepay $this
 */
?>
<img src="<?php echo $this->getImagesApiUrl(); ?><?php echo $this->getSnippetPath(); ?>?amount=<?php echo $this->getAmount(); ?><?php if ($this->isFullBlock()): ?>&full_block=1<?php endif; ?>"
     alt="<?php echo $this->getTitle(); ?>"/>
<script src="<?php echo $this->getImagesApiUrl(); ?>util.js?lpay_plus=<?php echo $this->isLpayPlusEnabled(); ?>"></script>
