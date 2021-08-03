(function($) {
    $(document).ready(function(){
        $('#mainform').on('submit', function() {
            var services = $('#woocommerce_latitudepay_lpay_services').val()
            if( services == 'LPAY'){
                return true;
            }
            var terms = $('#woocommerce_latitudepay_lpay_plus_payment_terms').val();
            if(terms == ''){
                $('#payment-terms-error').remove();
                $('#woocommerce_latitudepay_lpay_plus_payment_terms').before( '<p id="payment-terms-error" style="color:red;">At least 1 payment term is required.</p>' )
                return false;
            } else {
                $('#payment-terms-error').remove();
                return true;
            }
        });
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