<?php
include 'conexao.php';

$pdo = conectar();

$cpf = $nome = $sobrenome = $sexo = $dataNascimento = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['buscar'])) { // BUSCAR DADOS DO HÓSPEDE
        $cpf = $_POST["cpf"];

        $sql = "SELECT * FROM hospede WHERE cpf = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cpf]);
        $hospede = $stmt->fetch();

        if ($hospede) {
            $nome = $hospede["nome"];
            $sobrenome = $hospede["sobrenome"];
            $sexo = $hospede["sexo"];
            $dataNascimento = $hospede["dataNascimento"];
        } else {
            echo "<p>Hóspede não encontrado.</p>";
        }
    } elseif (isset($_POST['cadastrar'])) { // CADASTRAR NOVO HÓSPEDE E RESERVA
        $cpf = $_POST["cpf"];
        $nome = $_POST["nome"];
        $sobrenome = $_POST["sobrenome"];
        $sexo = $_POST["sexo"];
        $dataNascimento = $_POST["dataNascimento"];
        $paisOrigem = $_POST["paisOrigem"];
        $previsaoEstadia = $_POST["previsaoEstadia"];
        $ciasAereas = implode(", ", $_POST["ciasAereas"]);

        try {
            // **1️⃣ Verifica se o hóspede já está cadastrado**
            $sqlVerifica = "SELECT COUNT(*) FROM hospede WHERE cpf = ?";
            $stmtVerifica = $pdo->prepare($sqlVerifica);
            $stmtVerifica->execute([$cpf]);
            $existeHospede = $stmtVerifica->fetchColumn();

            // **2️⃣ Se o hóspede não estiver cadastrado, insere primeiro na tabela `hospede`**
            if ($existeHospede == 0) {
                $sqlHospede = "INSERT INTO hospede (cpf, nome, sobrenome, sexo, dataNascimento) VALUES (?, ?, ?, ?, ?)";
                $stmtHospede = $pdo->prepare($sqlHospede);
                $stmtHospede->execute([$cpf, $nome, $sobrenome, $sexo, $dataNascimento]);
                echo "<p>Hóspede cadastrado com sucesso!</p>";
            }

            // **3️⃣ Verificar se já existe uma reserva ativa**
            $sqlVerificaReserva = "SELECT COUNT(*) FROM controle WHERE hospedeCpf = ?";
            $stmtVerificaReserva = $pdo->prepare($sqlVerificaReserva);
            $stmtVerificaReserva->execute([$cpf]);
            $totalReservas = $stmtVerificaReserva->fetchColumn();

            if ($totalReservas > 0) {
                echo "<p>Erro: Este hóspede já tem uma reserva ativa e não pode fazer outra.</p>";
            } else {
                // **4️⃣ Insere a reserva na tabela `controle`**
                $sqlReserva = "INSERT INTO controle (hospedeCpf, paisOrigem, previsaoEstadia, ciasAereas) VALUES (?, ?, ?, ?)";
                $stmtReserva = $pdo->prepare($sqlReserva);
                $stmtReserva->execute([$cpf, $paisOrigem, $previsaoEstadia, $ciasAereas]);

                echo "<p>Reserva cadastrada com sucesso!</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao cadastrar: " . $e->getMessage() . "</p>";
        }
    }
}

$pdo = encerrar();
?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Tischler's Hotel</title>
</head>

<body>
    <h1>Tischler's Hotel</h1>
    <h2>Cadastro de Hóspede</h2>
    <form action="cadastro.php" method="post">
        <label>CPF:</label>
        <input type="text" name="cpf" value="<?php echo $cpf; ?>" required>
        <input type="submit" name="buscar" value="Buscar Dados"><br><br>

        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo $nome; ?>"><br><br>

        <label>Sobrenome:</label>
        <input type="text" name="sobrenome" value="<?php echo $sobrenome; ?>"><br><br>

        <label>Sexo:</label>
        <select name="sexo">
            <option value="M" <?php echo ($sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
            <option value="F" <?php echo ($sexo == 'F') ? 'selected' : ''; ?>>Feminino</option>
        </select><br><br>

        <label>Data de Nascimento:</label>
        <input type="date" name="dataNascimento" value="<?php echo $dataNascimento; ?>"><br><br>

        <h3>País de Origem</h3>
        <input type="radio" name="paisOrigem" value="Brasil"> Brasil<br>
        <input type="radio" name="paisOrigem" value="Argentina"> Argentina<br>
        <input type="radio" name="paisOrigem" value="Paraguai"> Paraguai<br>
        <input type="radio" name="paisOrigem" value="Uruguai"> Uruguai<br>
        <input type="radio" name="paisOrigem" value="Chile"> Chile<br>
        <input type="radio" name="paisOrigem" value="Peru"> Peru<br><br>

        <h3>Previsão de Dias de Estadia</h3>
        <select name="previsaoEstadia">
            <option value="3 dias">3 dias</option>
            <option value="5 dias">5 dias</option>
            <option value="1 semana">1 semana</option>
            <option value="2 semanas">2 semanas</option>
            <option value="3 semanas ou mais">3 semanas ou mais</option>
        </select><br><br>

        <h3>Companhias Aéreas Já Utilizadas</h3>
        <input type="checkbox" name="ciasAereas[]" value="GOL"> GOL<br>
        <input type="checkbox" name="ciasAereas[]" value="AZUL"> AZUL<br>
        <input type="checkbox" name="ciasAereas[]" value="TRIP"> TRIP<br>
        <input type="checkbox" name="ciasAereas[]" value="AVIANCA"> AVIANCA<br>
        <input type="checkbox" name="ciasAereas[]" value="RISSETTI"> RISSETTI<br>
        <input type="checkbox" name="ciasAereas[]" value="GLOBAL"> GLOBAL<br><br>

        <input type="submit" name="cadastrar" value="Cadastrar Reserva">
    </form>

    <form name="voltar" method="post" action="index.php">
        <input type="submit" name="voltar" value="Voltar">
    </form>
</body>

</html>