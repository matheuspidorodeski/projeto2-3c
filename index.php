<?php

require_once 'excecoes/EnergiaInsuficienteException.php';
require_once 'personagens/Personagem.php';
require_once 'personagens/Guerreiro.php';
require_once 'personagens/Mago.php';
require_once 'personagens/Assassino.php';
require_once 'LogBatalha.php';

function ler(string $prompt): string
{
    echo $prompt;
    return trim(fgets(STDIN));
}

function lerInteiro(string $prompt, int $min, int $max): int
{
    while (true) {
        $entrada = ler($prompt);
        if (ctype_digit($entrada) && (int)$entrada >= $min && (int)$entrada <= $max) {
            return (int)$entrada;
        }
        echo "  Entrada invalida. Digite um numero entre {$min} e {$max}.\n";
    }
}

function barraHp(Personagem $p): string
{
    $preenchido = (int) round(($p->getHp() / $p->getHpMax()) * 20);
    $barra = str_repeat("#", $preenchido) . str_repeat("-", 20 - $preenchido);
    return "[{$barra}] {$p->getHp()}/{$p->getHpMax()} HP";
}

$tipos = [
    1 => 'Guerreiro',
    2 => 'Mago',
    3 => 'Assassino',
];

$jogadores = [];

system('clear');
echo "\n=== ARENA DE COMBATE RPG ===\n\n";
ler("Pressione Enter para comecar...");

for ($i = 0; $i < 2; $i++) {
    system('clear');
    echo "\n=== ARENA DE COMBATE RPG ===\n\n";
    echo "Jogador " . ($i + 1) . ", escolha seu personagem:\n\n";

    foreach ($tipos as $num => $tipo) {
        $dummy = new $tipo('?');
        echo "  [{$num}] {$dummy->getDescricao()}\n";
    }

    echo "\n";
    $escolha = lerInteiro("Sua escolha: ", 1, 3);
    $classe  = $tipos[$escolha];

    $nome = '';
    while ($nome === '') {
        $nome = ler("Nome do seu personagem: ");
        if ($nome === '') echo "  O nome nao pode ser vazio.\n";
    }

    $jogadores[$i] = new $classe($nome);
    echo "\nJogador " . ($i + 1) . " escolheu: {$nome} ({$classe})\n";
    ler("Pressione Enter para continuar...");

}

$log   = new LogBatalha();
$turno = 0;
$vez   = 0;

system('clear');
echo "\n=== QUE A BATALHA COMECE! ===\n\n";
echo "Jogador 1: {$jogadores[0]->getNome()} ({$jogadores[0]->getTipo()})\n";
echo "Jogador 2: {$jogadores[1]->getNome()} ({$jogadores[1]->getTipo()})\n\n";
ler("Pressione Enter para iniciar...");

while ($jogadores[0]->estaVivo() && $jogadores[1]->estaVivo()) {
    $turno++;
    $atacante = $jogadores[$vez];
    $defensor = $jogadores[1 - $vez];

    if ($atacante instanceof Assassino && $atacante->temVenenoAtivo()) {
        $msgVeneno = $atacante->processarVeneno();
        if ($msgVeneno) {
            $log->registrarEvento($turno, $msgVeneno);
            if (!$defensor->estaVivo()) break;
        }
    }

    system('clear');
    echo "\n=== Turno {$turno} - Vez do Jogador " . ($vez + 1) . " ===\n\n";
    echo "Jogador " . ($vez + 1) . " - {$atacante->getNome()} ({$atacante->getTipo()})\n";
    echo "  HP:      " . barraHp($atacante) . "\n";
    echo "  Energia: {$atacante->getEnergia()}/{$atacante->getEnergiaMax()}\n\n";
    echo "Jogador " . (2 - $vez) . " - {$defensor->getNome()} ({$defensor->getTipo()})\n";
    echo "  HP:      " . barraHp($defensor) . "\n";
    echo "  Energia: {$defensor->getEnergia()}/{$defensor->getEnergiaMax()}\n\n";
    echo "Acoes:\n";
    echo "  [1] Atacar\n";
    echo "  [2] Defender (bonus de defesa ate o proximo turno)\n";
    echo "  [3] {$atacante->getNomeHabilidade()} (custo: {$atacante->getCustoHabilidade()} de energia)\n\n";

    $resultado  = '';
    $nomeAcao   = '';

    while (true) {
        $acao = lerInteiro("Escolha [1-3]: ", 1, 3);

        try {
            if ($acao === 1) {
                $dano      = $atacante->atacar($defensor);
                $resultado = "{$atacante->getNome()} atacou {$defensor->getNome()} causando {$dano} de dano! "
                           . "{$defensor->getNome()} agora tem {$defensor->getHp()} HP.";
                $nomeAcao  = "Atacar";

            } elseif ($acao === 2) {
                $bonus     = $atacante->defender();
                $resultado = "{$atacante->getNome()} se defendeu! Defesa +{$bonus} ate o proximo turno.";
                $nomeAcao  = "Defender";

            } else {
                $resultado = $atacante->usarHabilidade($defensor);
                $nomeAcao  = $atacante->getNomeHabilidade();
            }
            break;

        } catch (EnergiaInsuficienteException $e) {
            echo "\nErro: {$e->getMessage()} - escolha outra acao.\n\n";
        }
    }

    echo "\n> {$resultado}\n";
    $log->registrar($turno, "Jogador " . ($vez + 1) . " ({$atacante->getNome()})", $nomeAcao, $resultado);

    $atacante->regenerarEnergia($atacante::REGEN_ENERGIA);
    $defensor->resetarDefesaTemp();

    ler("\nPressione Enter para continuar...");

    $vez = 1 - $vez;
}

system('clear');
$vencedor = $jogadores[0]->estaVivo() ? $jogadores[0] : $jogadores[1];
$labelVencedor = $jogadores[0]->estaVivo() ? "Jogador 1" : "Jogador 2";

echo "\n=== FIM DE JOGO ===\n\n";
echo "Vencedor: {$labelVencedor} - {$vencedor->getNome()} ({$vencedor->getTipo()})\n";
echo "HP restante: {$vencedor->getHp()} / {$vencedor->getHpMax()}\n";
echo "Turnos disputados: {$turno}\n\n";

$verLog = ler("Deseja ver o log da batalha? [s/N]: ");
if (strtolower($verLog) === 's') {
    $log->exibir();
}

echo "\nObrigado por jogar!\n\n";