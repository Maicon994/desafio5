<?php
$opcoes = [
    1 => ['nome' => 'Pedra', 'emoji' => '✊'],
    2 => ['nome' => 'Papel', 'emoji' => '✋'],
    3 => ['nome' => 'Tesoura', 'emoji' => '✌️']
];

$jogada_usuario = null;
$jogada_computador = null;
$resultado = "";
$classe_resultado = "";

function determinarVencedor($usuario, $computador) {
    if ($usuario == $computador) {
        return "empate";
    }
    
    switch ($usuario) {
        case 1: // Pedra
            return ($computador == 3) ? "vitoria" : "derrota";
        case 2: // Papel
            return ($computador == 1) ? "vitoria" : "derrota";
        case 3: // Tesoura
            return ($computador == 2) ? "vitoria" : "derrota";
        default:
            return "invalido";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['escolha'])) {
    $jogada_usuario = filter_var($_POST['escolha'], FILTER_VALIDATE_INT);
    
    if ($jogada_usuario >= 1 && $jogada_usuario <= 3) {
        $jogada_computador = rand(1, 3);
        $status = determinarVencedor($jogada_usuario, $jogada_computador);
        
        if ($status == "empate") {
            $resultado = "Empatou!Ambos escolheram a mesma coisa.";
            $classe_resultado = "empate";
        } elseif ($status == "vitoria") {
            $resultado = "Ganhou!Parabéns, você derrotou a máquina!";
            $classe_resultado = "vitoria";
        } else {
            $resultado = "Perdeu! O computador levou a melhor.";
            $classe_resultado = "derrota";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Jogo do Jo-Ken-Pô (Pedra, Papel, Tesoura)</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f0f2f5; margin: 0; padding: 20px; }
        .game-container { max-width: 600px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .options-wrapper { display: flex; justify-content: center; gap: 20px; margin: 25px 0; }
        .btn-option { background: white; border: 2px solid #ddd; border-radius: 8px; padding: 15px; font-size: 2.5em; cursor: pointer; transition: transform 0.2s, border-color 0.2s; width: 100px; }
        .btn-option:hover { transform: scale(1.1); border-color: #007bff; }
        .label-option { display: block; font-size: 0.4em; font-family: Arial, sans-serif; margin-top: 5px; color: #555; }
        
        .arena { display: flex; justify-content: space-around; align-items: center; margin: 20px 0; background: #f8f9fa; padding: 15px; border-radius: 8px; }
        .player-box { font-size: 1.2em; font-weight: bold; }
        .big-emoji { font-size: 3em; margin: 10px 0; }
        
        .box-resultado { padding: 15px; border-radius: 8px; font-size: 1.3em; font-weight: bold; margin: 20px 0; }
        .vitoria { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .derrota { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .empate { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        
        .btn-reset { display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; }
        .gif-container img { max-width: 150px; border-radius: 8px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="game-container">
    <h2>🎮 Jogo do Jo-Ken-Pô</h2>
    <p>Escolha sua jogada abaixo para desafiar o computador:</p>

    <form method="post">
        <div class="options-wrapper">
            <button type="submit" name="escolha" value="1" class="btn-option">
                ✊ <span class="label-option">Pedra</span>
            </button>
            <button type="submit" name="escolha" value="2" class="btn-option">
                ✋ <span class="label-option">Papel</span>
            </button>
            <button type="submit" name="escolha" value="3" class="btn-option">
                ✌️ <span class="label-option">Tesoura</span>
            </button>
        </div>
    </form>

    <?php if ($jogada_usuario !== null && $jogada_computador !== null): ?>
        <div class="arena">
            <div class="player-box">
                <div>Você</div>
                <div class="big-emoji"><?= $opcoes[$jogada_usuario]['emoji'] ?></div>
                <div><?= $opcoes[$jogada_usuario]['nome'] ?></div>
            </div>
            
            <div style="font-size: 1.5em; font-weight: bold; color: #aaa;">VS</div>
            
            <div class="player-box">
                <div>Computador</div>
                <div class="big-emoji"><?= $opcoes[$jogada_computador]['emoji'] ?></div>
                <div><?= $opcoes[$jogada_computador]['nome'] ?></div>
            </div>
        </div>

        <div class="box-resultado <?= $classe_resultado ?>">
            <?= $resultado ?>
            
            <div class="gif-container">
                <?php if ($classe_resultado == 'vitoria'): ?>
                    <img src="https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExejF3ZzEwcXZlc2xpdHZuYnl0OW9rMzE3Mzk4Zno3bzB2NWFtMHh3ZiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/xPlrAYjaiChdC/giphy.gif" alt="Ganhou">
                <?php elseif ($classe_resultado == 'derrota'): ?>
                    <img src="https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExOGh4dmttdHYzNHZ4cmN2NjUzM3lvZzJvOHZ3eGxpN2RnaXl4ZW5yaSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/cGis3G6pdGfweqkTG5/giphy.gif" alt="Perdeu">
                <?php else: ?>
                    <img src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExMGl2NGJncG56ZG9leDVtb3FqMHNzYWt0emxiNmloOHU4MnE1ZG8ydiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/1NtnqiTqQXmUM/giphy.gif" alt="Empatou">
                <?php endif; ?>
            </div>
        </div>

        <a href="" class="btn-reset">🔄 Jogar Novamente</a>
    <?php endif; ?>
</div>

</body>
</html>