<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Consultar Hóspedes</title>
</head>

<body>
    <h2>Consultar Hóspedes</h2>

    <form action="consultar.php" method="post">
        <input type="submit" name="listar_todos" value="Listar Todos os Hóspedes">
    </form>
    <br>
    <form action="consultar.php" method="post">
        <label>Consultar reservas por CPF:</label>
        <input type="text" name="cpf" required>
        <input type="submit" name="listar_cpf" value="Consultar">
    </form>
    <br>
    <form name="voltar" method="post" action="index.php">
        <input type="submit" name="voltar" value="Voltar">
    </form>
</body>

</html>

<?php
include 'conexao.php';

if (isset($_POST['listar_todos'])) {
    listar_todosHospedes();
}

if (isset($_POST['listar_cpf'])) {
    listar_hospedeCPF($_POST['cpf']);
}

/* Função para listar todos os hóspedes, incluindo os que não têm reservas */
function listar_todosHospedes()
{
    $conexao = conectar();
    $sql = "SELECT * FROM hospede";
    $pstmt = $conexao->prepare($sql);
    $pstmt->execute();

    echo "<h3>Lista de Hóspedes</h3>";
    echo "<table>";
    echo "<tr><th>CPF</th><th>Nome</th><th>Sobrenome</th><th>Sexo</th><th>Data de Nascimento</th></tr>";

    while ($linha = $pstmt->fetch()) {
        echo "<tr>";
        echo "<td>" . $linha["cpf"] . "</td>";
        echo "<td>" . $linha["nome"] . "</td>";
        echo "<td>" . $linha["sobrenome"] . "</td>";
        echo "<td>" . $linha["sexo"] . "</td>";
        echo "<td>" . $linha["dataNascimento"] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    $conexao = encerrar();
}

/* Função para listar todas as reservas de um hóspede pelo CPF */
function listar_hospedeCPF($cpf)
{
    $conexao = conectar();
    $sql = "SELECT * FROM controle WHERE hospedeCpf = ?";
    $pstmt = $conexao->prepare($sql);
    $pstmt->execute([$cpf]);

    echo "<h3>Reservas do Hóspede - CPF: $cpf</h3>";
    echo "<table>";
    echo "<tr><th>País de Origem</th><th>Previsão de Estadia</th><th>Companhias Aéreas Utilizadas</th></tr>";

    $reservasEncontradas = false;

    while ($linha = $pstmt->fetch()) {
        $reservasEncontradas = true;
        echo "<tr>";
        echo "<td>" . $linha["paisOrigem"] . "</td>";
        echo "<td>" . $linha["previsaoEstadia"] . "</td>";
        echo "<td>" . $linha["ciasAereas"] . "</td>";
        echo "</tr>";
    }

    if (!$reservasEncontradas) {
        echo "<tr><td colspan='3'>Este hóspede não possui reservas registradas.</td></tr>";
    }

    echo "</table>";
    $conexao = encerrar();
}
?>