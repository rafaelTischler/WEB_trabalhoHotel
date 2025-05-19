<?php
include 'conexao.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST["cpf"];
    $excluirHospede = isset($_POST["excluir_hospede"]); // Verifica se deseja excluir o hóspede também

    $conexao = conectar();

    try {
        $sqlVerifica = "SELECT COUNT(*) AS total FROM controle WHERE hospedeCpf = ?";
        $stmtVerifica = $conexao->prepare($sqlVerifica);
        $stmtVerifica->execute([$cpf]);
        $resultado = $stmtVerifica->fetch(PDO::FETCH_ASSOC);
        $quantReservas = $resultado["total"];

        if ($quantReservas > 0) {
            $sqlControle = "DELETE FROM controle WHERE hospedeCpf = ?";
            $stmtControle = $conexao->prepare($sqlControle);
            $stmtControle->execute([$cpf]);
            $mensagem = "Reserva excluída com sucesso!";
        } else {
            $mensagem = "Esse hóspede não tem reservas!";
        }

        if ($excluirHospede && $quantReservas == 0) {
            $sqlHospede = "DELETE FROM hospede WHERE cpf = ?";
            $stmtHospede = $conexao->prepare($sqlHospede);
            $stmtHospede->execute([$cpf]);
            $mensagem = "Reserva e hóspede excluídos com sucesso!";
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao excluir: " . $e->getMessage();
    }

    $conexao = encerrar();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Excluir Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
    <style>
        body {
            background-image: url("img/fundo_excluir.jpg");
        }
    </style>
</head>

<body class="d-flex flex-column justify-content-center align-items-center min-vh-100">

    <div class="form-container">
        <h2 class="text-center form-title mb-4">Excluir Reserva</h2>

        <form action="excluir.php" method="post">
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF do Hóspede</label>
                <input type="text" name="cpf" id="cpf" required class="form-control">
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="excluir_hospede" value="sim" class="form-check-input" id="excluirHospede">
                <label class="form-check-label" for="excluirHospede">
                    Excluir hóspede caso não tenha mais reservas?
                </label>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-custom">Excluir Reserva</button>
            </div>
        </form>

        <form method="post" action="index.php" class="text-center mt-3">
            <button type="submit" name="voltar" class="btn btn-custom">Voltar</button>
        </form>
    </div>

    <?php if (!empty($mensagem)) : ?>
        <div class="mensagem-feedback">
            <?= $mensagem ?>
        </div>
    <?php endif; ?>

</body>

</html>