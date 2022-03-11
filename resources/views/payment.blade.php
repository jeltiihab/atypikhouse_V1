

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
    <script   src='https://www.paypal.com/sdk/js?client-id={{env('PAYPAL_CLIENT_ID')}}&currency=EUR&commit=true'></script>
</head>
<body>



<div  class="col-lg-7  col-12" id="pay">

</div>

<script>
    confirmUrl = "";
    orderID = "";
    paypal.Buttons({
        style: {
            layout:  'vertical',
        },
        createOrder:
            function() {

                return fetch("{!! URL::temporarySignedRoute('order.create',now()->addMinute(10))  !!}", {
                    method: 'post',
                    body: JSON.stringify( {
                        arrival : "2022-2-7",
                        departure: "2022-2-15",
                        propertyId: 14,
                        hosting_capacity:2,
                        "orderID":orderID
                    }),
                    headers: {
                        'content-type': 'application/json',
                        'X-CSRF-TOKEN': '{{csrf_token()}}',
                        'Accept' : 'application/json',
                        'Authorization' : "Bearer 23|wnYegAhYzFty7v15k5goVeDZ7DwttMU7U6W5QxjY"
                    }
                }).then(function(res) {
                    return res.json();
                }).then(function(data) {
                    console.log(data);
                    confirmUrl=data.confirmUrl;
                    return orderID = data.orderID // Use the same key name for order ID on the client and server
                });
            },
        onApprove:
            function (data) {

                return fetch(confirmUrl,{
                    headers: {
                        'content-type': 'application/json',
                        'X-CSRF-TOKEN': '{{csrf_token()}}',
                        'Authorization' : "Bearer 23|wnYegAhYzFty7v15k5goVeDZ7DwttMU7U6W5QxjY"
                    }
                }).then(function (res) {
                    return res.json()
                }).then(function (data) {
                    window.location.href="";
                })


            }
    }).render('#pay');
</script>

</body>
</html>
