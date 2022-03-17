<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <base href="{{$generateBase()}}">
    <title>I4NET</title>

    <!-- Styles -->
    <link rel="stylesheet" href="./public/css/app.css" type="text/css">

</head>
<body id="body">
    <main class="overflow-hidden relative">
        @include("components.header")

        @yield("content")
    </main>
    <script src="./public/js/app.js" defer></script>
</body>
</html>