<!DOCTYPE html>
<html lang="ru">
<head>

    <meta charset="utf-8">
    <title>@yield('pageTitle', 'Кабинет') - Школа ЦХМ</title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="apple-touch-icon" href="/build/img/favicon/adfinity-logo-152.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/build/img/favicon/adfinity-logo-152.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/build/img/favicon/adfinity-logo-152.png">
    <link rel="apple-touch-startup-image" href="/build/img/favicon/adfinity-logo-152.png">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
<div id="appVue"></div>
</body>
</html>
