<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment...</title>
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif;">
        <p>Redirecting to PayFast Secure Gateway...</p>
        
        <form id="payfast_form" method="post" action="https://ipg1.apps.net.pk/Ecommerce/api/Transaction/PostTransaction">
            <input type="hidden" name="CURRENCY_CODE" value="{{ $data['CURRENCY_CODE'] }}">
            <input type="hidden" name="MERCHANT_ID" value="{{ $data['MERCHANT_ID'] }}">
            <input type="hidden" name="TOKEN" value="{{ $data['TOKEN'] }}">
            <input type="hidden" name="SUCCESS_URL" value="{{ $data['SUCCESS_URL'] }}">
            <input type="hidden" name="FAILURE_URL" value="{{ $data['FAILURE_URL'] }}">
            <input type="hidden" name="CHECKOUT_URL" value="{{ $data['CHECKOUT_URL'] }}">
            <input type="hidden" name="CUSTOMER_EMAIL_ADDRESS" value="{{ $data['CUSTOMER_EMAIL_ADDRESS'] }}">
            <input type="hidden" name="CUSTOMER_MOBILE_NO" value="{{ $data['CUSTOMER_MOBILE_NO'] }}">
            <input type="hidden" name="TXNAMT" value="{{ $data['TXNAMT'] }}">
            <input type="hidden" name="BASKET_ID" value="{{ $data['BASKET_ID'] }}">
            <input type="hidden" name="ORDER_DATE" value="{{ $data['ORDER_DATE'] }}">
            <input type="hidden" name="SIGNATURE" value="{{ $data['SIGNATURE'] }}">
            <input type="hidden" name="VERSION" value="{{ $data['VERSION'] }}">
            <input type="hidden" name="TXNDESC" value="{{ $data['TXNDESC'] }}">
            <input type="hidden" name="PROCCODE" value="{{ $data['PROCCODE'] }}">
            <input type="hidden" name="TRAN_TYPE" value="ECOMM_PURCHASE">
        </form>
    </div>

    <script>
        window.onload = function() {
            document.getElementById('payfast_form').submit();
        };
    </script>
</body>
</html>