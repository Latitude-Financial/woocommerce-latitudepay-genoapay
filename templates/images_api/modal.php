<?php
/**
 * @var WC_LatitudeFinance_Method_Latitudepay $this
 */
?>
<script>
    !function ($) {
        $("img[src*='<?php echo $this->getImagesApiUrl(); ?>snippet.svg'], img[src*='<?php echo $this->getImagesApiUrl(); ?>api/banner'], img[src*='<?php echo $this->getImagesApiUrl(); ?>LatitudePayPlusSnippet.svg']").click(function(){
            var url = $(this).attr('src').replace('snippet.svg','modal.html');
            $.get(url,function(html){
                $( "body" ).append(html);
            });
        });
    }(jQuery);
</script>
