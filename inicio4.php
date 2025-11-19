<?php
require_once "connection.php";

// Garante que o ID é um número inteiro, usando prepared statement para SELECT
if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = $_GET["id"];

// LÓGICA DE ATUALIZAÇÃO (UPDATE) - SEGURA CONTRA INJEÇÃO SQL
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $nome = $_POST["nome"];
    $idade = (int)$_POST["idade"];
    $imc = (float)$_POST["imc"];
    $pressao_sistolica = (int)$_POST["sistolica"]; // Corrigido

    // Prepara a declaração UPDATE
    $stmt = $conn->prepare("UPDATE paciente SET
                            nome=?, idade=?, imc=?, pressao_sistolica=?
                            WHERE id=?");
                            
    // Associa os parâmetros: s(string), i(integer), d(double), i(integer), i(integer) para o ID
    $stmt->bind_param("siddi", $nome, $idade, $imc, $pressao_sistolica, $id);

    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// LÓGICA DE CARREGAMENTO DO PACIENTE - SEGURA CONTRA INJEÇÃO SQL
// Prepara o SELECT para carregar os dados
$stmt = $conn->prepare("SELECT nome, idade, imc, pressao_sistolica FROM paciente WHERE id = ?");
$stmt->bind_param("i", $id); // Associa o ID (integer)
$stmt->execute();
$result = $stmt->get_result();
$paciente = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$paciente) {
    header("Location: index.php"); // Redireciona se o paciente não for encontrado
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Paciente <?= htmlspecialchars($paciente["nome"]) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="container">
        <h1>Editar Paciente</h1>
        
        <form method="POST" action="edit.php?id=<?= htmlspecialchars($id) ?>">
            <h2>ID do Paciente: <?= htmlspecialchars($id) ?></h2>
            
            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($paciente["nome"]) ?>" required>
            </div>
            <div>
                <label for="idade">Idade:</label>
                <input type="number" id="idade" name="idade" min="0" value="<?= htmlspecialchars($paciente["idade"]) ?>" required>
            </div>
            <div>
                <label for="imc">IMC (Índice de Massa Corporal):</label>
                <input type="number" id="imc" name="imc" step="0.1" value="<?= htmlspecialchars($paciente["imc"]) ?>" required>
            </div>
            <div>
                <label for="sistolica">Pressão Sistólica (mmHg):</label>
                <input type="number" id="sistolica" name="sistolica" min="50" value="<?= htmlspecialchars($paciente["pressao_sistolica"]) ?>" required>
            </div>
            <button type="submit">Salvar Alterações</button>
        </form>
        <p><a href="index.php">Voltar para a Lista</a></p>
    </div>

</body>
</html>
