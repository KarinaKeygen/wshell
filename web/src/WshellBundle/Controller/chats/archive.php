<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>pusher</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/vendor/jquery.min.js"></script>
    <script src="../js/vendor/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css"/>
    <style>
        body {
            font-family: Calibri;
            font-size: 16px;
        }

        #content {
            padding-top: 60px;
        }
    </style>
    <script>
        function register() {
            var login = $('#i1').val();
            var pass = $('#i2').val();
            var result = send('offtop', 'new', [login, pass], false);
            $('#result').empty();
            $('#result').append('<p>' + result + '</p>');
        }
    </script>
</head>
<body>

<header class="navbar navbar-inverse navbar-fixed-top bs-docs-nav" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="../" class="navbar-brand">◀ К чатам</a>
        </div>
        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
            <ul class="nav navbar-nav">
                <li>
                    <a href="index.php">Инфо</a>
                </li>
                <li class="active">
                    <a href="archive.php">Архив</a>
                </li>
                <li>
                    <a href="cupboard.php">Чулан</a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li>
                        <div class="navbar-form navbar-left">
                            <div class="form-group">
                                <input type="text" class="form-control" id="i1" placeholder="Никнейм">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="i2" placeholder="Пароль">
                            </div>
                            <button class="btn btn-default" onclick='register();'>Готово</button>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a>Ваше имя: <?php echo $_SESSION['name'] ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<div class="container" id="content">
    <div class="row">
        <div id="result"></div>
        <p class="text-center">Архив закрыт на инвентаризацию =)</p>
    </div>
</div>
</body>
</html>