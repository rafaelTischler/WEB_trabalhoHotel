<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST["cpf"];
    $excluirHospede = isset($_POST["excluir_hospede"]); // Verifica se deseja excluir o hóspede também

    $conexao = conectar();

    try {
        // Excluir todas as reservas do hóspede na tabela controle
        $sqlControle = "DELETE FROM controle WHERE hospedeCpf = ?";
        $stmtControle = $conexao->prepare($sqlControle);
        $stmtControle->execute([$cpf]);

        // Se o operador escolheu excluir o hóspede, remove-o da tabela hospede
        if ($excluirHospede) {
            $sqlHospede = "DELETE FROM hospede WHERE cpf = ?";
            $stmtHospede = $conexao->prepare($sqlHospede);
            $stmtHospede->execute([$cpf]);

            echo "<p>Reserva e hóspede excluídos com sucesso!</p>";
        } else {
            echo "<p>Reserva excluída com sucesso!</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erro ao excluir: " . $e->getMessage() . "</p>";
    }

    $conexao = encerrar();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Reserva</title>
</head>
<body>
    <h2>Excluir Reserva</h2>
    <form action="excluir.php" method="post">
        <label>CPF do Hóspede:</label>
        <input type="text" name="cpf" required><br><br>

        <label>Excluir hóspede caso não tenha mais reservas?</label>
        <input type="checkbox" name="excluir_hospede" value="sim"> Sim<br><br>

        <input type="submit" value="Excluir Reserva">
    </form>
    <br>
    <form name="voltar" method="post" action="index.php">
        <input type="submit" name="voltar" value="Voltar">
    </form>
</body>
</html>
