<?php
include 'conexao.php';

$mensagem = "";

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
            $mensagem = "Hóspede não encontrado";
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
            }

            // **3️⃣ Verificar se já existe uma reserva ativa**
            $sqlVerificaReserva = "SELECT COUNT(*) FROM controle WHERE hospedeCpf = ?";
            $stmtVerificaReserva = $pdo->prepare($sqlVerificaReserva);
            $stmtVerificaReserva->execute([$cpf]);
            $totalReservas = $stmtVerificaReserva->fetchColumn();

            if ($totalReservas > 0) {
                $mensagem = "Este hóspede já tem uma reserva ativa e não pode fazer outra";
            } else {
                // **4️⃣ Insere a reserva na tabela `controle`**
                $sqlReserva = "INSERT INTO controle (hospedeCpf, paisOrigem, previsaoEstadia, ciasAereas) VALUES (?, ?, ?, ?)";
                $stmtReserva = $pdo->prepare($sqlReserva);
                $stmtReserva->execute([$cpf, $paisOrigem, $previsaoEstadia, $ciasAereas]);

                $mensagem = "Reserva cadastrada com sucesso!";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar: " . $e->getMessage() . "";
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
    <style>
        body {
            background-image: url("img/fundo_cadastro.jpg");

        }

        .menu-container {
            text-align: start;
        }
    </style>

</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100 text-center" style="color: white;">
    <div class="menu-container">
        <h3 class="mb-4">Cadastro de Hóspede</h3>
        <form action="cadastro.php" method="post">
            <div class="row mb-3 align-items-end">
                <div class="col-md-8">
                    <label>CPF:</label>
                    <input type="text" name="cpf" value="<?php echo $cpf; ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <input type="submit" name="buscar" value="Buscar Dados" class="btn menu-btn w-100">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Nome:</label>
                    <input type="text" name="nome" value="<?php echo $nome; ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Sobrenome:</label>
                    <input type="text" name="sobrenome" value="<?php echo $sobrenome; ?>" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Sexo:</label>
                    <select name="sexo" class="form-select">
                        <option value="M" <?php echo ($sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="F" <?php echo ($sexo == 'F') ? 'selected' : ''; ?>>Feminino</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Data de Nascimento:</label>
                    <input type="date" name="dataNascimento" value="<?php echo $dataNascimento; ?>" class="form-control">
                </div>
            </div>

            <!-- O restante do formulário continua igual -->

            <h5>País de Origem</h5>
            <div class="mb-3">
                <label class="me-2"><input type="radio" name="paisOrigem" value="Brasil"> Brasil</label>
                <label class="me-2"><input type="radio" name="paisOrigem" value="Argentina"> Argentina</label>
                <label class="me-2"><input type="radio" name="paisOrigem" value="Paraguai"> Paraguai</label>
                <label class="me-2"><input type="radio" name="paisOrigem" value="Uruguai"> Uruguai</label>
                <label class="me-2"><input type="radio" name="paisOrigem" value="Chile"> Chile</label>
                <label class="me-2"><input type="radio" name="paisOrigem" value="Peru"> Peru</label>
            </div>

            <h5>Previsão de Dias de Estadia</h5>
            <div class="mb-3">
                <select name="previsaoEstadia" class="form-select">
                    <option value="3 dias">3 dias</option>
                    <option value="5 dias">5 dias</option>
                    <option value="1 semana">1 semana</option>
                    <option value="2 semanas">2 semanas</option>
                    <option value="3 semanas ou mais">3 semanas ou mais</option>
                </select>
            </div>

            <h5>Companhias Aéreas Já Utilizadas</h5>
            <div class="mb-4">
                <label class="me-2"><input type="checkbox" name="ciasAereas[]" value="GOL"> GOL</label>
                <label class="me-2"><input type="checkbox" name="ciasAereas[]" value="AZUL"> AZUL</label>
                <label class="me-2"><input type="checkbox" name="ciasAereas[]" value="TRIP"> TRIP</label>
                <label class="me-2"><input type="checkbox" name="ciasAereas[]" value="AVIANCA"> AVIANCA</label>
                <label class="me-2"><input type="checkbox" name="ciasAereas[]" value="RISSETTI"> RISSETTI</label>
                <label class="me-2"><input type="checkbox" name="ciasAereas[]" value="GLOBAL"> GLOBAL</label>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <input type="submit" name="cadastrar" value="Cadastrar Reserva" class="btn menu-btn w-100">
                </div>
                <div class="col-md-6">
                    <a href="index.php" class="btn menu-btn w-100">Voltar</a>
                </div>
            </div>
        </form>

    </div>

</body>

<?php if (!empty($mensagem)) : ?>
    <div class="mensagem-feedback">
        <?php echo htmlspecialchars($mensagem); ?>
    </div>
<?php endif; ?>

</html>