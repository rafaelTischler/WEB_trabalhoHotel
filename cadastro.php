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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100 text-center">
    
    <h1 class="mb-3">Tischler's Hotel</h1>
    <h2 class="mb-4">Cadastro de Hóspede</h2>

    <div class="menu-container">
        <form action="cadastro.php" method="post">
            <label>CPF:</label>
            <input type="text" name="cpf" value="<?php echo $cpf; ?>" required class="form-control mb-3">
            <input type="submit" name="buscar" value="Buscar Dados" class="btn menu-btn mb-4">

            <label>Nome:</label>
            <input type="text" name="nome" value="<?php echo $nome; ?>" class="form-control mb-3">

            <label>Sobrenome:</label>
            <input type="text" name="sobrenome" value="<?php echo $sobrenome; ?>" class="form-control mb-3">

            <label>Sexo:</label>
            <select name="sexo" class="form-select mb-3">
                <option value="M" <?php echo ($sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
                <option value="F" <?php echo ($sexo == 'F') ? 'selected' : ''; ?>>Feminino</option>
            </select>

            <label>Data de Nascimento:</label>
            <input type="date" name="dataNascimento" value="<?php echo $dataNascimento; ?>" class="form-control mb-3">

            <h3>País de Origem</h3>
            <div class="mb-3">
                <input type="radio" name="paisOrigem" value="Brasil"> Brasil
                <input type="radio" name="paisOrigem" value="Argentina"> Argentina
                <input type="radio" name="paisOrigem" value="Paraguai"> Paraguai
                <input type="radio" name="paisOrigem" value="Uruguai"> Uruguai
                <input type="radio" name="paisOrigem" value="Chile"> Chile
                <input type="radio" name="paisOrigem" value="Peru"> Peru
            </div>

            <h3>Previsão de Dias de Estadia</h3>
            <select name="previsaoEstadia" class="form-select mb-3">
                <option value="3 dias">3 dias</option>
                <option value="5 dias">5 dias</option>
                <option value="1 semana">1 semana</option>
                <option value="2 semanas">2 semanas</option>
                <option value="3 semanas ou mais">3 semanas ou mais</option>
            </select>

            <h3>Companhias Aéreas Já Utilizadas</h3>
            <div class="mb-3">
                <input type="checkbox" name="ciasAereas[]" value="GOL"> GOL
                <input type="checkbox" name="ciasAereas[]" value="AZUL"> AZUL
                <input type="checkbox" name="ciasAereas[]" value="TRIP"> TRIP
                <input type="checkbox" name="ciasAereas[]" value="AVIANCA"> AVIANCA
                <input type="checkbox" name="ciasAereas[]" value="RISSETTI"> RISSETTI
                <input type="checkbox" name="ciasAereas[]" value="GLOBAL"> GLOBAL
            </div>

            <input type="submit" name="cadastrar" value="Cadastrar Reserva" class="btn menu-btn">

        </form>

        <form name="voltar" method="post" action="index.php">
            <input type="submit" name="voltar" value="Voltar" class="btn menu-btn mt-3">
        </form>
    </div>

</body>

</html>
