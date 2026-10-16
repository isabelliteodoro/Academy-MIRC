<?php 
require_once 'conexao.php';
$mensagem = "";

if (isset($_GET['excluir']) && !empty($_GET['excluir'])) {
    $id_excluir = $_GET['excluir'];
    try {
        $sqlTreino = $conexao->prepare("DELETE FROM Treino WHERE id_aluno = :id");
        $sqlTreino->execute([':id' => $id_excluir]);

        $sqlMatricula = $conexao->prepare("DELETE FROM Matricula WHERE id_aluno = :id");
        $sqlMatricula->execute([':id' => $id_excluir]);

        $sqlAluno = $conexao->prepare("DELETE FROM Aluno WHERE id_aluno = :id");
        $sqlAluno->execute([':id' => $id_excluir]);

        $mensagem = "Sucesso: Aluno e histórico removidos!";
    } catch (Exception $e) {
        $mensagem = "Erro ao excluir: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'alterar_nome') {
    $id_aluno = $_POST['id_aluno'];
    $novo_nome = $_POST['novo_nome'];

    if (empty($id_aluno) || empty($novo_nome)) {
        $mensagem = "Erro: Selecione o aluno e digite o novo nome!";
    } else {
        $sql = $conexao->prepare("UPDATE Aluno SET nome = :nome WHERE id_aluno = :id");
        $sql->execute([':nome' => $novo_nome, ':id' => $id_aluno]);
        $mensagem = "Sucesso: Nome atualizado!";
    }
}

$buscaAlunos = $conexao->query("SELECT * FROM Aluno ORDER BY nome ASC");
$listaAlunos = $buscaAlunos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Alunos - ACADEMY MIRC</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <h1>ACADEMY MIRC</h1>
    
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="index.php" class="btn">Treinos</a>
        <a href="matriculas.php" class="btn">Matrículas</a>
        <a href="planos.php" class="btn">Planos</a>
        <a href="alunos.php" class="btn" style="background-color: #ff0000;">Alunos</a>
    </div>

    <?php if (!empty($mensagem)): ?>
        <p style="font-weight: bold; color: #ff0000; text-align: center;"><?= $mensagem ?></p>
    <?php endif; ?>

    <div class="box">
        <h2>Alterar Nome do Aluno</h2>
        <form action="alunos.php" method="POST">
            <input type="hidden" name="acao" value="alterar_nome">

            <label>Selecione o Aluno:</label>
            <select name="id_aluno">
                <option value="">-- Selecione o Aluno --</option>
                <?php foreach ($listaAlunos as $a): ?>
                    <option value="<?= $a['id_aluno'] ?>"><?= $a['nome'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Novo Nome:</label>
            <input type="text" name="novo_nome" placeholder="Digite o nome corrigido">

            <button type="submit" class="btn" style="margin-top:15px;">Atualizar Nome</button>
        </form>
    </div>

    <div class="box">
        <h2>Alunos Cadastrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Data Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaAlunos as $a): ?>
                    <tr>
                        <td><?= $a['id_aluno'] ?></td>
                        <td><?= $a['nome'] ?></td>
                        <td><?= $a['cpf'] ?></td>
                        <td><?= $a['data_cadastro'] ?></td>
                        <td>
                            <a href="alunos.php?excluir=<?= $a['id_aluno'] ?>" 
                               class="btn" 
                               style="background-color: #dc3545; padding: 5px 10px;" 
                               onclick="return confirm('Tem certeza que deseja remover este aluno e todo o seu histórico do sistema?');">
                                Excluir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
