<!DOCTYPE html>
<html lang="pt-br">

<head>
    <style>
        body {
            background-image: url("img/fundo_consultar.jpg");
        }
    </style>

    <meta charset="UTF-8">
    <title>Consultar Hóspedes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100 text-center">

    <div class="menu-container">
        <h2 class="text-center form-title mb-4">Consultar Hóspedes</h2>

        <!-- Formulário para listar todos -->
        <form action="consultar.php" method="post" class="mb-3">
            <button type="submit" name="listar_todos" class="btn menu-btn w-100">Listar Todos os Hóspedes</button>

        </form>

        <!-- Formulário para consultar por CPF -->
        <form action="consultar.php" method="post" class="mb-3">
            <label for="cpf" class="form-label">Consultar reservas por CPF:</label>
            <input type="text" name="cpf" id="cpf" required class="form-control mb-2">

            <div class="row">
                <div class="col-md-6">
                    <button type="submit" name="listar_cpf" class="btn menu-btn w-100">Consultar</button>
                </div>
                <div class="col-md-6">
                    <a href="index.php" class="btn menu-btn w-100">Voltar</a>
                </div>
            </div>
        </form>


        <!-- Área dos resultados -->

        <?php
        include 'conexao.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['listar_todos'])) {
                listar_todosHospedes();
            }

            if (isset($_POST['listar_cpf'])) {
                listar_hospedeCPF($_POST['cpf']);
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
                echo "<h4 class='text-white'>Lista de Hóspedes</h4>
      <div class='table-responsive'>
      <table class='table tabela-transparente'>
        <thead>
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
                echo "<div class='alert alert-warning '>Nenhum hóspede encontrado.</div>";
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


            if (count($reservas) > 0) {
                echo "<h4 class='text-white'>Reservas do Hóspede - CPF: $cpf</h4>
      <div class='table-responsive'>
      <table class='table tabela-transparente'>
        <thead>
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
                echo "<div class='alert '>Este hóspede não possui reservas registradas.</div>";
            }

            encerrar();
        }
        ?>

    </div>
</body>

</html>