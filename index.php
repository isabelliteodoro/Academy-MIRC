<?php 
require_once 'conexao.php';
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $aluno = $_POST['aluno'];
    $divisao = $_POST['divisao'];
    $texto = $_POST['texto'];
    $data = $_POST['data'];

    if (empty($aluno) || empty($divisao) || empty($texto) || empty($data)) {
        $mensagem = "Erro: Preencha todos os campos!";
    } else {
        $sql = $conexao->prepare("INSERT INTO Treino (id_aluno, divisao_treino, detalhe_exercicios, data_validade) VALUES (:aluno, :divisao, :texto, :data)");
        $sql->execute([':aluno' => $aluno, ':divisao' => $divisao, ':texto' => $texto, ':data' => $data]);
        $mensagem = "Sucesso: Cadastrado!";
    }
}

$buscaAlunos = $conexao->query("SELECT id_aluno, nome FROM Aluno");
$listaAlunos = $buscaAlunos->fetchAll(PDO::FETCH_ASSOC);

$buscaTreinos = $conexao->query("SELECT t.id_treino, a.nome, t.divisao_treino, t.detalhe_exercicios, t.data_validade FROM Treino t INNER JOIN Aluno a ON t.id_aluno = a.id_aluno ORDER BY t.id_treino DESC");
$listaTreinos = $buscaTreinos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ACADEMY MIRC</title>
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

    <div class="box">
        <h2>Novo Treino</h2>
        <form action="index.php" method="POST">
            <input type="hidden" name="acao" value="cadastrar">

            <label>Aluno:</label>
            <select name="aluno">
                <option value="">-- Selecione --</option>
                <?php foreach ($listaAlunos as $a): ?>
                    <option value="<?= $a['id_aluno'] ?>"><?= $a['nome'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Divisão:</label>
            <input type="text" name="divisao" maxlength="1">

            <label>Exercícios:</label>
            <textarea name="texto" rows="3"></textarea>

            <label>Validade:</label>
            <input type="date" name="data">

            <button type="submit" class="btn" style="margin-top:10px;">Salvar</button>
        </form>
    </div>

    <div class="box">
        <h2>Controle de Rotinas e Exercícios</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Aluno</th>
                    <th>Divisão</th>
                    <th>Exercícios</th>
                    <th>Validade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaTreinos as $t): ?>
                    <tr>
                        <td><?= $t['id_treino'] ?></td>
                        <td><?= $t['nome'] ?></td>
                        <td><?= $t['divisao_treino'] ?></td>
                        <td><?= $t['detalhe_exercicios'] ?></td>
                        <td><?= $t['data_validade'] ?></td>
                        <td><a href="editar.php?id=<?= $t['id_treino'] ?>" class="btn btn-edit">Editar</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
