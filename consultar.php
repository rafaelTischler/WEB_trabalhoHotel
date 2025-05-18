<!DOCTYPE html>
<html lang="pt-br">

<head>
    <style>
        body {
            color: black;
            background-image: url("img/fundo_consultar.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .container {
            width: 100%;
      
            background-color: rgba(255, 255, 255, 0.6);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.7);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .menu-btn,
        .btn-custom {
            max-width: 250px;
            width: 100%;
            background-color: rgba(171, 131, 81, 0.8) !important;
            color: white !important;
            border: none;
        }

        .menu-btn:hover,
        .btn-custom:hover {
            background-color: rgba(235, 193, 141, 0.8) !important;
            color: black !important;
        }

        .form-label {
            font-weight: bold;
        }

        input[type="text"] {
            max-width: 300px;
            margin: 0 auto;
        }
    </style>

    <meta charset="UTF-8">
    <title>Consultar Hóspedes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
</head>

<body class="d-flex flex-column align-items-center min-vh-100 pt-5">

    <div class="form-container">
        <h2 class="text-center form-title mb-4">Consultar Hóspedes</h2>

        <!-- Formulário para listar todos -->
        <form action="consultar.php" method="post" class="mb-3">
            <button type="submit" name="listar_todos" class="btn btn-custom">Listar Todos os Hóspedes</button>

        </form>

        <!-- Formulário para consultar por CPF -->
        <form action="consultar.php" method="post" class="mb-3">
            <label for="cpf" class="form-label">Consultar reservas por CPF:</label>
            <input type="text" name="cpf" id="cpf" required class="form-control mb-2">
            <button type="submit" name="listar_cpf" class="btn btn-custom">Consultar</button>
        </form>

        <form method="post" action="index.php">
            <button type="submit" name="voltar" class="btn btn-custom">Voltar</button>
        </form>
    </div>

    <!-- Área dos resultados -->
    <div class="mt-4 mb-5">
        <?php
        include 'conexao.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['listar_todos'])) {
                echo '<div class="container mt-4 mb-5">';
                listar_todosHospedes();
                echo '</div>';
            }

            if (isset($_POST['listar_cpf'])) {
                echo '<div class="container mt-4 mb-5">';
                listar_hospedeCPF($_POST['cpf']);
                echo '</div>';
            }
        }

        function listar_todosHospedes()
        {
            $conexao = conectar();
            $sql = "SELECT * FROM hospede";
            $pstmt = $conexao->prepare($sql);
            $pstmt->execute();
            $hospedes = $pstmt->fetchAll();

            if (count($hospedes) > 0) {
                echo "<h4 class='mt-4'>Lista de Hóspedes</h4>
                      <div class='table-responsive'>
                      <table class='table mt-3'>
                            <tr>
                                <th>CPF</th>
                                <th>Nome</th>
                                <th>Sobrenome</th>
                                <th>Sexo</th>
                                <th>Data de Nascimento</th>
                            </tr>
                        </thead>
                        <tbody>";

                foreach ($hospedes as $linha) {
                    echo "<tr>
                            <td>{$linha['cpf']}</td>
                            <td>{$linha['nome']}</td>
                            <td>{$linha['sobrenome']}</td>
                            <td>{$linha['sexo']}</td>
                            <td>{$linha['dataNascimento']}</td>
                          </tr>";
                }

                echo "</tbody></table></div>";
            } else {
                echo "<div class='alert alert-warning mt-4'>Nenhum hóspede encontrado.</div>";
            }

            encerrar();
        }

        function listar_hospedeCPF($cpf)
        {
            $conexao = conectar();
            $sql = "SELECT * FROM controle WHERE hospedeCpf = ?";
            $pstmt = $conexao->prepare($sql);
            $pstmt->execute([$cpf]);
            $reservas = $pstmt->fetchAll();

            echo "<h4 class='mt-4'>Reservas do Hóspede - CPF: $cpf</span></h4>";

            if (count($reservas) > 0) {
                echo "<div class='table-responsive'>
                      <table class='table mt-3'>
                            <tr>
                                <th>País de Origem</th>
                                <th>Previsão de Estadia</th>
                                <th>Companhias Aéreas Utilizadas</th>
                            </tr>
                        </thead>
                        <tbody>";

                foreach ($reservas as $linha) {
                    echo "<tr>
                            <td>{$linha['paisOrigem']}</td>
                            <td>{$linha['previsaoEstadia']}</td>
                            <td>{$linha['ciasAereas']}</td>
                          </tr>";
                }

                echo "</tbody></table></div>";
            } else {
                echo "<div class='alert mt-3'>Este hóspede não possui reservas registradas.</div>";
            }

            encerrar();
        }
        ?>
    </div>

</body>

</html>