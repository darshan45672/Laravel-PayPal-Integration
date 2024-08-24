<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PayPal Laravel Integration</title>
</head>
<body>
    <h1>HAHAHH</h1> 
    <form action="{{ route('paypal') }}" method="POST">
        @csrf
        <button type="submit" >pay with paypal</button>
    </form>
</body>
</html>