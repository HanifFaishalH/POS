<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Homepage</title>
</head>
<body>
    <h1>Selamat datang di halaman utama Point of Sales</h1>

    <h2>Pilih kategori</h2>

    <ul>
        <li><a href="{{ url('/food-beverage') }}">Food and Beverage</a></li>
        <li><a href="{{ url('/beauty-healthy') }}">Beauty and Healthy</a></li>
        <li><a href="{{ url('/home-care') }}">Home Care</a></li>
        <li><a href="{{ url('/baby-kid') }}">Baby and Kids</a></li>
    </ul>

    <p><a href="{{ url('/user') }}">User Information</a></p>  </p>
    <p><a href="{{ url('/sales') }}">Transactions</a></p>  </p>
</body>
</html>