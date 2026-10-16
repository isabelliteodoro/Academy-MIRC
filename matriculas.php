<?php 
require_once 'conexao.php';
$mensagem = "";
$matricula_editar = null;

if (isset($_GET['editar_id']) && !empty($_GET['editar_id'])) {
    $id_edit = $_GET['editar_id'];
    $sqlBusca = $conexao->prepare("SELECT m.*, a.nome FROM Matricula m INNER JOIN Aluno a ON m.id_aluno = a.id_aluno WHERE m.id_matricula = :id");
    $sqlBusca->execute([':id' => $id_edit]);
    $matricula_editar = $sqlBusca->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_edicao') {
    $id_mat = $_POST['id_matricula'];
    $status = $_POST['status_pagamento'];

    if (!empty($id_mat) && !empty($status)) {
        $sqlUp = $conexao->prepare("UPDATE Matricula SET status_pagamento = :status WHERE id_matricula = :id");
        $sqlUp->execute([':status' => $status, ':id' => $id_mat]);
        $mensagem = "Sucesso: Pagamento atualizado!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome_aluno = $_POST['nome_aluno'];
    $data = $_POST['data_inicio'];
    $status = $_POST['status_pagamento'];

    if (empty($nome_aluno) || empty($data) || empty($status)) {
        $mensagem = "Erro: Preencha todos os campos!";
    } else {
        try {
            $cpf_ficticio = substr(str_shuffle("0123456789"), 0, 11);
            $data_hoje = date('Y-m-d');
            
            $sqlAluno = $conexao->prepare("INSERT INTO Aluno (nome, cpf, data_cadastro, status_acesso) VALUES (:nome, :cpf, :data_cad, 'Liberado')");
            $sqlAluno->execute([':nome' => $nome_aluno, ':cpf' => $cpf_ficticio, ':data_cad' => $data_hoje]);
            
            $id_aluno_novo = $conexao->lastInsertId();

            $sqlMatricula = $conexao->prepare("INSERT INTO Matricula (id_aluno, id_plano, data_inicio, status_pagamento) VALUES (:aluno, 4, :data, :status)");
            $sqlMatricula->execute([':aluno' => $id_aluno_novo, ':data' => $data, ':status' => $status]);

            $mensagem = "Sucesso: Aluno cadastrado e matriculado!";
        } catch (Exception $e) {
            $mensagem = "Erro: " . $e->getMessage();
        }
    }
}

$buscaMatriculas = $conexao->query("SELECT m.id_matricula, a.nome, m.data_inicio, m.status_pagamento FROM Matricula m INNER JOIN Aluno a ON m.id_aluno = a.id_aluno ORDER BY m.id_matricula DESC");
$listaMatriculas = $buscaMatriculas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Matrículas - ACADEMY MIRC</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <h1>ACADEMY MIRC</h1>

    <div style="text-align: center; margin-bottom: 20px;">
        <a href="index.php" class="btn">Treinos</a>
        <a href="matriculas.php" class="btn" style="background-color: #ff0000;">Matrículas</a>
    </div>

    <?php if (!empty($mensagem)): ?>
        <p style="font-weight: bold; color: #ff0000; text-align: center;"><?= $mensagem ?></p>
    <?php endif; ?>

    <?php if ($matricula_editar): ?>
        <div class="box" style="border-color: #ff0707c6;">
            <h2>Editar Situação de Pagamento</h2>
            <form action="matriculas.php" method="POST">
                <input type="hidden" name="acao" value="salvar_edicao">
                <input type="hidden" name="id_matricula" value="<?= $matricula_editar['id_matricula'] ?>">

                <label>Aluno:</label>
                <input type="text" value="<?= $matricula_editar['nome'] ?>" disabled>

                <label>Situação do Pagamento:</label>
                <select name="status_pagamento">
                    <option value="Em dia" <?= $matricula_editar['status_pagamento'] === 'Em dia' ? 'selected' : '' ?>>Em dia</option>
                    <option value="Inadimplente" <?= $matricula_editar['status_pagamento'] === 'Inadimplente' ? 'selected' : '' ?>>Inadimplente</option>
                </select>

                <div style="margin-top: 15px;">
                    <button type="submit" class="btn" style="background-color: #e40707b3; color: black;">Atualizar</button>
                    <a href="matriculas.php" class="btn" style="background-color: #6c757d;">Cancelar</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="box">
            <h2>Nova Matrícula Contratual</h2>
            <form action="matriculas.php" method="POST">
                <input type="hidden" name="acao" value="cadastrar">

                <label>Nome do Novo Aluno:</label>
                <input type="text" name="nome_aluno" placeholder="Digite o nome completo">

                <label>Data de Início:</label>
                <input type="date" name="data_inicio">

                <label>Situação do Pagamento:</label>
                <select name="status_pagamento">
                    <option value="Em dia">Em dia</option>
                    <option value="Inadimplente">Inadimplente</option>
                </select>

                <button type="submit" class="btn" style="margin-top:15px;">Efetivar Matrícula</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="box">
        <h2>Contratos de Matrícula</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Aluno</th>
                    <th>Início</th>
                    <th>Situação Financeira</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaMatriculas as $m): ?>
                    <tr>
                        <td><?= $m['id_matricula'] ?></td>
                        <td><?= $m['nome'] ?></td>
                        <td><?= $m['data_inicio'] ?></td>
                        <td style="font-weight: bold; color: <?= $m['status_pagamento'] === 'Inadimplente' ? '#ff0000' : '#00ff00' ?>;">
                            <?= $m['status_pagamento'] ?>
                        </td>
                        <td>
                            <a href="matriculas.php?editar_id=<?= $m['id_matricula'] ?>" class="btn" style="background-color: #ffc107; color: black; padding: 5px 10px;">
                                Editar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
