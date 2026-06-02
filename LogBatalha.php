<?php

class LogBatalha
{
    private array $entradas = [];

    public function registrar(int $turno, string $jogador, string $acao, string $resultado): void
    {
        $this->entradas[] = [
            'turno'     => $turno,
            'jogador'   => $jogador,
            'acao'      => $acao,
            'resultado' => $resultado,
        ];
    }

    public function registrarEvento(int $turno, string $evento): void
    {
        $this->entradas[] = [
            'turno'     => $turno,
            'jogador'   => null,
            'acao'      => null,
            'resultado' => $evento,
        ];
    }

    public function exibir(): void
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "           LOG COMPLETO DA BATALHA\n";
        echo str_repeat("=", 60) . "\n";

        $turnoAtual = 0;
        foreach ($this->entradas as $entrada) {
            if ($entrada['turno'] !== $turnoAtual) {
                $turnoAtual = $entrada['turno'];
                echo "\n-- Turno {$turnoAtual} " . str_repeat("-", 40) . "\n";
            }

            if ($entrada['jogador'] === null) {
                echo "  [EVENTO] {$entrada['resultado']}\n";
            } else {
                echo "  {$entrada['jogador']} usou: {$entrada['acao']}\n";
                echo "  Resultado: {$entrada['resultado']}\n";
            }
        }

        echo "\n" . str_repeat("=", 60) . "\n";
    }
}