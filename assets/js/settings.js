(function($) {
    $(document).ready(function(){
        $('#woocommerce_latitudepay_lpay_services').change(function(){
            if($(this).val() == 'LPAY'){
                $('#woocommerce_latitudepay_lpay_plus_payment_terms').parents('tr').hide();
            } else {
                $('#woocommerce_latitudepay_lpay_plus_payment_terms').parents('tr').show();
            }
        })
        if($('#woocommerce_latitudepay_lpay_services').val() == 'LPAY'){
            $('#woocommerce_latitudepay_lpay_plus_payment_terms').parents('tr').hide();
        } else {
            $('#woocommerce_latitudepay_lpay_plus_payment_terms').parents('tr').show();
        }
    })
})(jQuery)