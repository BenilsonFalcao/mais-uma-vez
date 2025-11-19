<?php
require_once "connection.php";

// Garante que o ID está presente
if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = $_GET["id"];

// LÓGICA DE EXCLUSÃO (DELETE) - SEGURA CONTRA INJEÇÃO SQL
// Prepara a declaração DELETE
$stmt = $conn->prepare("DELETE FROM paciente WHERE id = ?");

// Associa o parâmetro: i(integer) para o ID
$stmt->bind_param("i", $id);

$stmt->execute();
$stmt->close();
$conn->close();

header("Location: index.php");
exit;
?>
