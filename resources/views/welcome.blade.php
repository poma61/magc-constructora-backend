<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MAGC</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="{{ asset('src/css/normalize.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('src/css/style.css') }}" rel="stylesheet" type="text/css" />

    <!-- Styles -->
</head>

<body>
    <main class="container">
        <h1 class="as-box-shadow as-font-white">
            MAGC - DESARROLLADORAS
        </h1>
           <img class="img__logo" src="{{ asset('src/images/logo-empresa.jpeg') }}">
        <h3 class="as-font-white">
           BACKEND FRAMEWORK v{{ Illuminate\Foundation\Application::VERSION }} - PHP v{{ PHP_VERSION }}
        </h3>
    </main>
</body>

</html>
