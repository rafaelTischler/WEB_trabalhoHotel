<?php
include 'conexao.php';

$mensagem = "";
$pdo = conectar();
$cpf = $nome = $sobrenome = $sexo = $dataNascimento = $paisOrigem = $previsaoEstadia = $ciasAereas = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['buscar'])) {
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
            $mensagem = "Hóspede não encontrado";
        }
        if ($reserva) {
            $paisOrigem = $reserva["paisOrigem"];
            $previsaoEstadia = $reserva["previsaoEstadia"];
            $ciasAereas = $reserva["ciasAereas"];
        }
    } elseif (isset($_POST['alterar'])) {
        $cpf = $_POST["cpf"];
        $nome = $_POST["nome"];
        $sobrenome = $_POST["sobrenome"];
        $dataNascimento = $_POST["dataNascimento"];
        $paisOrigem = $_POST["paisOrigem"];
        $previsaoEstadia = $_POST["previsaoEstadia"];
        $ciasAereas = isset($_POST["ciasAereas"]) ? implode(", ", $_POST["ciasAereas"]) : "";
        try {
            $sqlHospede = "UPDATE hospede SET nome = ?, sobrenome = ?, dataNascimento = ? WHERE cpf = ?";
            $stmtHospede = $pdo->prepare($sqlHospede);
            $stmtHospede->execute([$nome, $sobrenome, $dataNascimento, $cpf]);
            $sqlReserva = "UPDATE controle SET paisOrigem = ?, previsaoEstadia = ?, ciasAereas = ? WHERE hospedeCpf = ?";
            $stmtReserva = $pdo->prepare($sqlReserva);
            $stmtReserva->execute([$paisOrigem, $previsaoEstadia, $ciasAereas, $cpf]);
            $mensagem = "Dados alterados com sucesso!";
        } catch (PDOException $e) {
            $mensagem = "Erro ao alterar dados: " . $e->getMessage() . "";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
    <style>
        body {
            background-image: url("img/fundo_excluir.jpg");
        }
        .form-container{
            text-align: start;
        }
    </style>
</head>

<body class="d-flex flex-column justify-content-center align-items-center vh-100 text-center">
    <div class="form-container">
        <h2 class="text-center form-title mb-4 mt-4">Alterar Dados</h2>

        <form action="alterar.php" method="post">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="cpf" class="form-label">CPF:</label>
                    <input type="text" name="cpf" id="cpf" class="form-control" value="<?php echo $cpf; ?>" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" name="buscar" class="btn btn-custom w-100">Buscar Dados</button>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nome:</label>
                    <input type="text" name="nome" class="form-control" value="<?php echo $nome; ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sobrenome:</label>
                    <input type="text" name="sobrenome" class="form-control" value="<?php echo $sobrenome; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Data de Nascimento:</label>
                    <input type="date" name="dataNascimento" class="form-control" value="<?php echo $dataNascimento; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="mb-2">País de Origem</h5>
                    <?php
                    $paises = ["Brasil", "Argentina", "Paraguai", "Uruguai", "Chile", "Peru"];
                    foreach ($paises as $pais) {
                        $checked = ($paisOrigem == $pais) ? "checked" : "";
                        echo "<div class='form-check'>
                                <input class='form-check-input' type='radio' name='paisOrigem' value='$pais' $checked>
                                <label class='form-check-label'>$pais</label>
                              </div>";
                    }
                    ?>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-2">Companhias Aéreas Já Utilizadas</h5>
                    <?php
                    $cias = ["GOL", "AZUL", "TRIP", "AVIANCA", "RISSETTI", "GLOBAL"];
                    foreach ($cias as $cia) {
                        $checked = (strpos($ciasAereas, $cia) !== false) ? "checked" : "";
                        echo "<div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='ciasAereas[]' value='$cia' $checked>
                                <label class='form-check-label'>$cia</label>
                              </div>";
                    }
                    ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Previsão de Dias de Estadia</label>
                <select name="previsaoEstadia" class="form-select">
                    <option value="3 dias" <?php echo ($previsaoEstadia == "3 dias") ? "selected" : ""; ?>>3 dias</option>
                    <option value="5 dias" <?php echo ($previsaoEstadia == "5 dias") ? "selected" : ""; ?>>5 dias</option>
                    <option value="1 semana" <?php echo ($previsaoEstadia == "1 semana") ? "selected" : ""; ?>>1 semana</option>
                    <option value="2 semanas" <?php echo ($previsaoEstadia == "2 semanas") ? "selected" : ""; ?>>2 semanas</option>
                    <option value="3 semanas ou mais" <?php echo ($previsaoEstadia == "3 semanas ou mais") ? "selected" : ""; ?>>3 semanas ou mais</option>
                </select>
            </div>

            <div class="text-center mb-3">
                <button type="submit" name="alterar" class="btn btn-custom">Alterar Dados</button>
            </div>
        </form>

        <form method="post" action="index.php" class="text-center">
            <button type="submit" name="voltar" class="btn btn-custom">Voltar</button>
        </form>
    </div>
</body>
<?php if (!empty($mensagem)) : ?>
    <div class="mensagem-feedback">
        <?php echo htmlspecialchars($mensagem); ?>
    </div>
<?php endif; ?>

</html>