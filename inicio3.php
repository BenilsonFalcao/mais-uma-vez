<?php
require_once "connection.php";

// LÓGICA DE CRIAÇÃO (CREATE) - SEGURA CONTRA INJEÇÃO SQL
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Coleta dos dados do formulário
    $nome = $_POST["nome"];
    $idade = (int)$_POST["idade"]; // Converte para inteiro
    $imc = (float)$_POST["imc"];   // Converte para float
    $pressao_sistolica = (int)$_POST["sistolica"]; // Corrigido para "sistolica" do HTML

    // Prepara a declaração SQL com placeholders (?)
    $stmt = $conn->prepare("INSERT INTO paciente (nome, idade, imc, pressao_sistolica) 
                            VALUES (?, ?, ?, ?)");
    
    // Associa os parâmetros: s(string), i(integer), d(double), i(integer)
    $stmt->bind_param("sidi", $nome, $idade, $imc, $pressao_sistolica);

    // Executa e fecha a declaração
    $stmt->execute();
    $stmt->close();

    // Redireciona para evitar reenvio do formulário (PRG Pattern)
    header("Location: index.php");
    exit;
}

// LÓGICA DE LEITURA (READ)
$sql = "SELECT id, nome, idade, imc, pressao_sistolica FROM paciente ORDER BY nome";
$result = $conn->query($sql);

$pacientes = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pacientes[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏥 Monitoramento de Pacientes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="container">
        <h1>Sistema de Monitoramento de Pacientes</h1>
        
        <form method="POST" action="index.php">
            <h2>Registro de Dados</h2>
            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div>
                <label for="idade">Idade:</label>
                <input type="number" id="idade" name="idade" min="0" required>
            </div>
            <div>
                <label for="imc">IMC (Índice de Massa Corporal):</label>
                <input type="number" id="imc" name="imc" step="0.1" required>
            </div>
            <div>
                <label for="sistolica">Pressão Sistólica (mmHg):</label>
                <input type="number" id="sistolica" name="sistolica" min="50" required>
            </div>
            <button type="submit">Adicionar Paciente</button>
        </form>

        <hr>

        <h2>Pacientes Monitorados</h2>

        <table id="patientTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Idade</th>
                    <th>IMC</th>
                    <th>Pressão Sistólica</th>
                    <th>Ações</th> </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p["id"]) ?></td>
                        <td><?= htmlspecialchars($p["nome"]) ?></td>
                        <td><?= htmlspecialchars($p["idade"]) ?></td>
                        <td><?= htmlspecialchars($p["imc"]) ?></td>
                        <td><?= htmlspecialchars($p["pressao_sistolica"]) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $p["id"] ?>">Editar</a> |
                            <a href="delete.php?id=<?= $p["id"] ?>" 
                               onclick="return confirm('Tem certeza que deseja deletar este paciente?');">Deletar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
