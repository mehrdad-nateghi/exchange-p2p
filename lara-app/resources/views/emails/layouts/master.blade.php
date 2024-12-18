<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        /* Reset styles for email clients */
        * {
            direction: rtl !important;
            text-align: right !important;
        }

        body {
            font-family: Tahoma, sans-serif !important;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            direction: rtl !important;
            text-align: right !important;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            direction: rtl !important;
            text-align: right !important;
        }

        .email-body {
            background-color: #fff7e7;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            direction: rtl !important;
            text-align: right !important;
        }

        .button {
            display: inline-block;
            background-color: #00925d;
            color: white !important;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 10px;
            text-align: center !important;
        }

        /* Force RTL for table elements */
        table, tr, td, th {
            direction: rtl !important;
            text-align: right !important;
        }
    </style>
</head>
<body dir="rtl">
<div class="container" dir="rtl">
    <div class="email-body" dir="rtl">
        @yield('content')
    </div>
</div>
</body>
</html>
