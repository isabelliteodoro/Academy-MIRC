<?php 
require_once 'conexao.php';
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome = $_POST['nome_plano'];
    $valor = $_POST['valor_mensal'];
    $duracao = $_POST['duracao_meses'];
    $descricao = $_POST['descricao_beneficios'];

    if (empty($nome) || empty($valor) || empty($duracao)) {
        $mensagem = "Erro: Preencha os campos obrigatórios!";
    } else {
        $sql = $conexao->prepare("INSERT INTO Plano (nome_plano, valor_mensal, duracao_meses, descricao_beneficios) VALUES (:nome, :valor, :duracao, :descricao)");
        $sql->execute([':nome' => $nome, ':valor' => $valor, ':duracao' => $duracao, ':descricao' => $descricao]);
        $mensagem = "Sucesso: Plano cadastrado!";
    }
}

$buscaPlanos = $conexao->query("SELECT * FROM Plano ORDER BY id_plano DESC");
$listaPlanos = $buscaPlanos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Planos - ACADEMY MIRC</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <h1>ACADEMY MIRC</h1>
    
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="index.php" class="btn">Treinos</a>
        <a href="matriculas.php" class="btn">Matrículas</a>
        <a href="planos.php" class="btn">Planos</a>
        <a href="alunos.php" class="btn">Alunos</a>
    </div>


    <?php if (!empty($mensagem)): ?>
        <p style="font-weight: bold; color: #ff0000; text-align: center;"><?= $mensagem ?></p>
    <?php endif; ?>

    <div class="box">
        <h2>Novo Plano Comercial</h2>
        <form action="planos.php" method="POST">
            <input type="hidden" name="acao" value="cadastrar">

            <label>Nome do Plano:</label>
            <input type="text" name="nome_plano" placeholder="Ex: Plano Anual">

            <label>Valor Mensal (R$):</label>
            <input type="number" step="0.01" name="valor_mensal" placeholder="Ex: 79.90">

            <label>Duração (Meses):</label>
            <input type="number" name="duracao_meses" placeholder="Ex: 12">

            <label>Descrição dos Benefícios:</label>
            <textarea name="descricao_beneficios" rows="2"></textarea>

            <button type="submit" class="btn" style="margin-top:15px;">Salvar Plano</button>
        </form>
    </div>

    <div class="box">
        <h2>Planos Cadastrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Valor</th>
                    <th>Duração</th>
                    <th>Benefícios</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaPlanos as $p): ?>
                    <tr>
                        <td><?= $p['id_plano'] ?></td>
                        <td><?= $p['nome_plano'] ?></td>
                        <td>R$ <?= number_format($p['valor_mensal'], 2, ',', '.') ?></td>
                        <td><?= $p['duracao_meses'] ?> meses</td>
                        <td><?= $p['descricao_beneficios'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
