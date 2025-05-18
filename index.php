<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Tischler's Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: black;
            background-image: url("img/fundo_menu.jpg");
            background-size: cover;
        }

        .menu-container {
            max-width: 70%;
            height: 100%;
            width: 50%;
            background-color:rgba(255, 255, 255, 0.5);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center
            
        }

        .menu-btn {
            max-width: 250px;
            width: 50%;
            background-color:rgba(171, 131, 81, 0.5);
            color: white;
            border: none;
        }

        .menu-btn:hover {
            background-color:rgba(235, 193, 141, 0.5);
        }
    </style>
</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100 text-center">


    <div class="menu-container">
        <img src="img/logo.png" width="200" height="200">
        <a href="cadastro.php" class="btn menu-btn mb-2">Cadastrar</a>
        <a href="alterar.php" class="btn menu-btn mb-2">Alterar</a>
        <a href="consultar.php" class="btn menu-btn mb-2">Consultar</a>
        <a href="excluir.php" class="btn menu-btn">Excluir</a>
    </div>

</body>

</html>