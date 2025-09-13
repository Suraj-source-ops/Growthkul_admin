<!DOCTYPE html>
<html>

<head>
    <title>Access Denied</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Luckiest+Guy|Oswald');

        body {
            background-color: #ccad98;
            width: 98%;
            text-align: center;
            color: white;
            font-family: Oswald;
        }

        h1 {
            color: white;
            font-size: 200px;
            font-weight: 100;
            margin-top: 25vh;
            margin-bottom: 10px;
            text-decoration: underline;
            font-family: Luckiest Guy;
        }

        h2 {
            font-size: 50px;
            margin-top: -10px;
        }

        h3 {
            font-size: 30px;
        }

        h4 {
            font-size: 25px;
        }

        h4 a {
            color: #00fffa;
        }

        .back-btn {
            padding: 12px 30px;
            background: #fff;
            border-radius: 20px;
            border: none;
            color: #ccad98;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <h1>403</h1>
    <h2>Access Denied</h2>
    <h3>You dont have permission to view this page</h3>
    <a href="{{route('dashboard')}}" ><button class="back-btn">Back to home</button></a>
</body>

</html>