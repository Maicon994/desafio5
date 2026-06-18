<?php
$campanha = $premio = $valor = $data_sorteio = "";
$quantidade = 50;
$bilhetes = [];
$imagem_path = null;
$erros = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campanha = trim($_POST['campanha'] ?? '');
    $premio = trim($_POST['premio'] ?? '');
    $data_sorteio = trim($_POST['data_sorteio'] ?? '');
    $quantidade = filter_var($_POST['quantidade'] ?? 0, FILTER_VALIDATE_INT);
    
    $valor_str = str_replace(',', '.', trim($_POST['valor'] ?? ''));
    $valor_valido = filter_var($valor_str, FILTER_VALIDATE_FLOAT);

    // Validações
    if (empty($campanha)) $erros[] = "O nome da campanha é obrigatório.";
    if (empty($premio)) $erros[] = "O nome do prêmio é obrigatório.";
    if (empty($data_sorteio)) $erros[] = "A data do sorteio é obrigatória.";
    
    if ($quantidade === false || $quantidade < 50 || $quantidade > 500) {
        $erros[] = "A quantidade de bilhetes deve ser um número entre 50 e 500.";
    }
    
    if ($valor_valido === false || $valor_valido <= 0) {
        $erros[] = "O valor do bilhete deve ser maior que zero.";
    } else {
        $valor = $valor_valido;
    }

    // Upload Opcional da Imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($extensao, $extensoes_permitidas)) {
            // Cria um nome único e simula um upload local temporário em string base64 para o arquivo único funcionar direto
            $conteudo_img = file_get_contents($_FILES['imagem']['tmp_name']);
            $imagem_path = 'data:image/' . $extensao . ';base64,' . base64_encode($conteudo_img);
        } else {
            $erros[] = "Formato de imagem inválido. Use JPG, PNG ou GIF.";
        }
    }

    // Se não houver erros, gera a numeração dos bilhetes
    if (count($erros) == 0) {
        for ($i = 1; $i <= $quantidade; $i++) {
            $bilhetes[] = str_pad($i, 3, "0", STR_PAD_LEFT);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerador de Rifas Profissional</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0,1); }
        .erro { color: red; background: #ffe8e8; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .campo { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"], input[type="date"], input[type="file"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .btn-print { background: #007bff; margin-bottom: 20px; }
        
        /* Estilização dos Bilhetes de Rifa */
        .rifa-wrapper { display: flex; flex-direction: column; gap: 15px; margin-top: 30px; }
        .bilhete { display: flex; border: 2px dashed #333; background: #fff; border-radius: 4px; overflow: hidden; page-break-inside: avoid; }
        .canhoto { width: 30%; border-right: 2px dashed #333; padding: 10px; background: #f4f4f4; display: flex; flex-direction: column; justify-content: space-between; }
        .corpo-rifa { width: 70%; padding: 10px; display: flex; justify-content: space-between; position: relative; }
        .info-rifa { flex-grow: 1; padding-right: 10px; }
        .img-rifa { width: 80px; height: 80px; object-fit: cover; border: 1px solid #ccc; border-radius: 4px; }
        .numero-destaque { font-size: 1.5em; color: red; font-weight: bold; text-align: right; }
        
        /* Regras de Impressão */
        @media print {
            body { background: white; margin: 0; }
            .no-print { display: none !important; }
            .container { box-shadow: none; padding: 0; max-width: 100%; }
            .bilhete { border: 2px dashed #000; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="no-print">
        <h2>🎟️ Gerador Automático de Rifas</h2>
        <?php if (count($erros) > 0): ?>
            <div class="erro">
                <ul><?php foreach ($erros as $erro) { echo "<li>$erro</li>"; } ?></ul>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="campo">
                <label>Título da Rifa / Nome da Campanha:</label>
                <input type="text" name="campanha" value="<?= htmlspecialchars($campanha) ?>" placeholder="Ex: Rifa Beneficente do Terceirão">
            </div>
            <div class="campo">
                <label>Prêmio(s):</label>
                <input type="text" name="premio" value="<?= htmlspecialchars($premio) ?>" placeholder="Ex: PlayStation 5 ou R$ 4.000 no PIX">
            </div>
            <div class="campo">
                <label>Quantidade (50 a 500):</label>
                <input type="number" name="quantidade" value="<?= htmlspecialchars($quantidade) ?>" min="50" max="500">
            </div>
            <div class="campo">
                <label>Valor por Bilhete (R$):</label>
                <input type="text" name="valor" value="<?= htmlspecialchars($_POST['valor'] ?? '') ?>" placeholder="Ex: 10,00">
            </div>
            <div class="campo">
                <label>Data do Sorteio:</label>
                <input type="date" name="data_sorteio" value="<?= htmlspecialchars($data_sorteio) ?>">
            </div>
            <div class="campo">
                <label>Imagem do Prêmio (Opcional):</label>
                <input type="file" name="imagem" accept="image/*">
            </div>
            <button type="submit" class="btn">Gerar Bilhetes</button>
        </form>
    </div>

    <?php if (count($bilhetes) > 0 && count($erros) == 0): ?>
        <hr class="no-print" style="margin: 30px 0;">
        <div class="no-print" style="text-align: right;">
            <button onclick="window.print();" class="btn btn-print">🖨️ Imprimir Bilhetes</button>
        </div>

        <div class="rifa-wrapper">
            <?php foreach ($bilhetes as $num): ?>
                <div class="bilhete">
                    <div class="canhoto">
                        <div>
                            <small>Nº <strong><?= $num ?></strong></small><br>
                            <span style="font-size: 0.8em; font-weight: bold;"><?= htmlspecialchars($campanha) ?></span>
                        </div>
                        <div style="font-size: 0.7em; margin-top: 5px;">
                            Nome: _______________________<br>
                            Fone: _______________________
                        </div>
                    </div>
                    
                    <div class="corpo-rifa">
                        <div class="info-rifa">
                            <h3 style="margin: 0 0 5px 0; font-size: 1.1em; color: #333;"><?= htmlspecialchars($campanha) ?></h3>
                            <p style="margin: 3px 0; font-size: 0.9em;"><strong>Prêmio:</strong> <?= htmlspecialchars($premio) ?></p>
                            <p style="margin: 3px 0; font-size: 0.8em;"><strong>Sorteio em:</strong> <?= date('d/m/Y', strtotime($data_sorteio)) ?></p>
                            <p style="margin: 3px 0; font-size: 0.9em; color: green;"><strong>Valor:</strong> R$ <?= number_format($valor, 2, ',', '.') ?></p>
                        </div>
                        
                        <?php if ($imagem_path): ?>
                            <img src="<?= $imagem_path ?>" class="img-rifa" alt="Prêmio">
                        <?php endif; ?>
                        
                        <div class="numero-destaque">
                            Nº <?= $num ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>