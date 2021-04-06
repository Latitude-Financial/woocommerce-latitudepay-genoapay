<?php
    if (!isset($snippetPath)) {
        $snippetPath = 'snippet.svg';
    }

    if (!isset($price)) {
        $price = 0;
    }

    if (!isset($title)) {
        $title = '';
    }

    if (!isset($isLPayPlusEnabled)) {
        $isLPayPlusEnabled = '0';
    }

    if (!isset($fullBlock)) {
        $fullBlock = false;
    }
?>

<img src="https://images.latitudepayapps.com/<?php echo $snippetPath; ?>?amount=<?php echo $price; ?><?php if($fullBlock): ?>&full_block=1<?php endif; ?>"  alt="<?php echo $title; ?>"/>
<script src="https://images.latitudepayapps.com/util.js?lpay_plus=<?php echo $isLPayPlusEnabled; ?>"></script>
