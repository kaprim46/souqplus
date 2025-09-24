<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        SooqPlus - Order Status Changed
    </title>
    <style type="text/css">
        .order__mail__content {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
            background-color: #ffffff;
            color: #000000;
            border-radius: 5px;
        }

        .order__mail__content h1 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .order__mail__content p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .order__mail__content a {
            display: inline-block;
            font-size: 16px;
            margin-bottom: 20px;
            text-decoration: none;
            color: #ffffff;
            background-color: #3490dc;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btn-blue {
            display: inline-block;
            font-size: 16px;
            margin-bottom: 20px;
            text-decoration: none;
            color: #ffffff;
            background-color: #3490dc;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="order__mail__content">
        <h1>
           {{ __('Order Status Changed') }}
        </h1>

        {{ $slot }}
    </div>
    <!-- Footer -->
    <div style="text-align: center; margin-top: 20px;">
        <p>
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
