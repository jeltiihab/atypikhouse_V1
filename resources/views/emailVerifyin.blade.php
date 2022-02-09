<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body>



<script>
    $(document).ready(function () {
        let accessToken = localStorage.getItem('access_token');
        $.ajax({
            type:'GET',
            url: "{!! $url  !!}",
            headers: {
                "X-CSRF-Token": "{{csrf_token()}}",
                "Authorization": accessToken
            }
        }).done(function( data ) {
            console.log(data);
            location.href = data.url ;

        });
    })

</script>

</body>
</html>
