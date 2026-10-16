<?php
require_once 'conexao.php';
$mensagem = "";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = $conexao->prepare("SELECT * FROM Treino WHERE id_treino = :id");
    $sql->execute([':id' => $id]);
    $treino = $sql->fetch(PDO::FETCH_ASSOC);
    
    if (!$treino) { die("Erro: Registro não encontrado!"); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
    $id = $_POST['id'];
    $sqlDel = $conexao->prepare("DELETE FROM Treino WHERE id_treino = :id");
    $sqlDel->execute([':id' => $id]);
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'editar') {
    $id = $_POST['id'];
    $divisao = $_POST['divisao'];
    $texto = $_POST['texto'];
    $data = $_POST['data'];

    if (empty($id) || empty($divisao) || empty($texto) || empty($data)) {
        $mensagem = "Preencha todos os campos!";
    } else {
        $sqlUp = $conexao->prepare("UPDATE Treino SET divisao_treino = :divisao, detalhe_exercicios = :texto, data_validade = :data WHERE id_treino = :id");
        $sqlUp->execute([':divisao' => $divisao, ':texto' => $texto, ':data' => $data, ':id' => $id]);
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <h1>Editar Treino</h1>

    <?php if (!empty($mensagem)): ?>
        <p style="color: red; font-weight: bold;"><?= $mensagem ?></p>
    <?php endif; ?>

    <div class="box">
        <form action="editar.php?id=<?= $treino['id_treino'] ?? '' ?>" method="POST">
            <input type="hidden" name="id" value="<?= $treino['id_treino'] ?? '' ?>">

            <label>Divisão:</label>
            <input type="text" name="divisao" maxlength="1" value="<?= $treino['divisao_treino'] ?? '' ?>">

            <label>Exercícios:</label>
            <textarea name="texto" rows="5"><?= $treino['detalhe_exercicios'] ?? '' ?></textarea>

            <label>Validade:</label>
            <input type="date" name="data" value="<?= $treino['data_validade'] ?? '' ?>">

            <div style="margin-top: 25px; display: flex; gap: 10px;">
                <button type="submit" name="acao" value="editar" class="btn">Atualizar</button>
                
                <button type="submit" name="acao" value="excluir" class="btn" style="background-color: #dc3545;" onclick="return confirm('Tem certeza que deseja apagar esta ficha de treino?');">
                    Excluir Treino
                </button>
                
                <a href="index.php" class="btn" style="background:#6c757d;">Voltar</a>
            </div>
        </form>
    </div>

</body>
</html>