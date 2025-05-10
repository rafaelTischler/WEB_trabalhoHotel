<?php
include 'conexao.php';

$pdo = conectar();

$cpf = $nome = $sobrenome = $sexo = $dataNascimento = $paisOrigem = $previsaoEstadia = $ciasAereas = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['buscar'])) { // BUSCAR DADOS DO HÓSPEDE E RESERVA
        $cpf = $_POST["cpf"];

        $sqlHospede = "SELECT * FROM hospede WHERE cpf = ?";
        $stmtHospede = $pdo->prepare($sqlHospede);
        $stmtHospede->execute([$cpf]);
        $hospede = $stmtHospede->fetch();

        $sqlReserva = "SELECT * FROM controle WHERE hospedeCpf = ?";
        $stmtReserva = $pdo->prepare($sqlReserva);
        $stmtReserva->execute([$cpf]);
        $reserva = $stmtReserva->fetch();

        if ($hospede) {
            $nome = $hospede["nome"];
            $sobrenome = $hospede["sobrenome"];
            $sexo = $hospede["sexo"];
            $dataNascimento = $hospede["dataNascimento"];
        } else {
            echo "<p>Hóspede não encontrado.</p>";
        }

        if ($reserva) {
            $paisOrigem = $reserva["paisOrigem"];
            $previsaoEstadia = $reserva["previsaoEstadia"];
            $ciasAereas = $reserva["ciasAereas"];
        }
    } elseif (isset($_POST['alterar'])) { // ALTERAR DADOS DO HÓSPEDE E RESERVA
        $cpf = $_POST["cpf"];
        $nome = $_POST["nome"];
        $sobrenome = $_POST["sobrenome"];
        $dataNascimento = $_POST["dataNascimento"];
        $paisOrigem = $_POST["paisOrigem"];
        $previsaoEstadia = $_POST["previsaoEstadia"];
        $ciasAereas = implode(", ", $_POST["ciasAereas"]);

        try {
            $sqlHospede = "UPDATE hospede SET nome = ?, sobrenome = ?, dataNascimento = ? WHERE cpf = ?";
            $stmtHospede = $pdo->prepare($sqlHospede);
            $stmtHospede->execute([$nome, $sobrenome, $dataNascimento, $cpf]);

            $sqlReserva = "UPDATE controle SET paisOrigem = ?, previsaoEstadia = ?, ciasAereas = ? WHERE hospedeCpf = ?";
            $stmtReserva = $pdo->prepare($sqlReserva);
            $stmtReserva->execute([$paisOrigem, $previsaoEstadia, $ciasAereas, $cpf]);

            echo "<p>Dados alterados com sucesso!</p>";
        } catch (PDOException $e) {
            echo "<p>Erro ao alterar dados: " . $e->getMessage() . "</p>";
        }
    }
}

$pdo = encerrar();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Dados</title>
</head>
<body>
    <h1>Alterar Dados do Hóspede e da Reserva</h1>
    <form action="alterar.php" method="post">
        <label>CPF:</label>
        <input type="text" name="cpf" value="<?php echo $cpf; ?>" required>
        <input type="submit" name="buscar" value="Buscar Dados"><br><br>

        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo $nome; ?>"><br><br>

        <label>Sobrenome:</label>
        <input type="text" name="sobrenome" value="<?php echo $sobrenome; ?>"><br><br>

        <label>Data de Nascimento:</label>
        <input type="date" name="dataNascimento" value="<?php echo $dataNascimento; ?>"><br><br>

        <h3>País de Origem</h3>
        <input type="radio" name="paisOrigem" value="Brasil" <?php echo ($paisOrigem == "Brasil") ? "checked" : ""; ?>> Brasil<br>
        <input type="radio" name="paisOrigem" value="Argentina" <?php echo ($paisOrigem == "Argentina") ? "checked" : ""; ?>> Argentina<br>
        <input type="radio" name="paisOrigem" value="Paraguai" <?php echo ($paisOrigem == "Paraguai") ? "checked" : ""; ?>> Paraguai<br>
        <input type="radio" name="paisOrigem" value="Uruguai" <?php echo ($paisOrigem == "Uruguai") ? "checked" : ""; ?>> Uruguai<br>
        <input type="radio" name="paisOrigem" value="Chile" <?php echo ($paisOrigem == "Chile") ? "checked" : ""; ?>> Chile<br>
        <input type="radio" name="paisOrigem" value="Peru" <?php echo ($paisOrigem == "Peru") ? "checked" : ""; ?>> Peru<br><br>

        <h3>Previsão de Dias de Estadia</h3>
        <select name="previsaoEstadia">
            <option value="3 dias" <?php echo ($previsaoEstadia == "3 dias") ? "selected" : ""; ?>>3 dias</option>
            <option value="5 dias" <?php echo ($previsaoEstadia == "5 dias") ? "selected" : ""; ?>>5 dias</option>
            <option value="1 semana" <?php echo ($previsaoEstadia == "1 semana") ? "selected" : ""; ?>>1 semana</option>
            <option value="2 semanas" <?php echo ($previsaoEstadia == "2 semanas") ? "selected" : ""; ?>>2 semanas</option>
            <option value="3 semanas ou mais" <?php echo ($previsaoEstadia == "3 semanas ou mais") ? "selected" : ""; ?>>3 semanas ou mais</option>
        </select><br><br>

        <h3>Companhias Aéreas Já Utilizadas</h3>
        <input type="checkbox" name="ciasAereas[]" value="GOL" <?php echo (strpos($ciasAereas, "GOL") !== false) ? "checked" : ""; ?>> GOL<br>
        <input type="checkbox" name="ciasAereas[]" value="AZUL" <?php echo (strpos($ciasAereas, "AZUL") !== false) ? "checked" : ""; ?>> AZUL<br>
        <input type="checkbox" name="ciasAereas[]" value="TRIP" <?php echo (strpos($ciasAereas, "TRIP") !== false) ? "checked" : ""; ?>> TRIP<br>
        <input type="checkbox" name="ciasAereas[]" value="AVIANCA" <?php echo (strpos($ciasAereas, "AVIANCA") !== false) ? "checked" : ""; ?>> AVIANCA<br>
        <input type="checkbox" name="ciasAereas[]" value="RISSETTI" <?php echo (strpos($ciasAereas, "RISSETTI") !== false) ? "checked" : ""; ?>> RISSETTI<br>
        <input type="checkbox" name="ciasAereas[]" value="GLOBAL" <?php echo (strpos($ciasAereas, "GLOBAL") !== false) ? "checked" : ""; ?>> GLOBAL<br><br>

        <input type="submit" name="alterar" value="Alterar Dados">
    </form>

    <form name="voltar" method="post" action="index.php">
        <input type="submit" name="voltar" value="Voltar">
    </form>
</body>
</html>
