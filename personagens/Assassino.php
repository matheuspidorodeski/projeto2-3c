<?php

class Assassino extends Personagem
{
    const BONUS_DEFESA      = 5;
    const CUSTO_HABILIDADE  = 30;
    const DANO_VENENO       = 10;
    const TURNOS_VENENO     = 3;
    const REGEN_ENERGIA     = 12;

    protected int $hpMax      = 100;
    protected int $ataque     = 38;
    protected int $defesa     = 8;
    protected int $energiaMax = 100;
    protected int $energia    = 100;

    private int $turnosVenenoRestantes = 0;
    private ?Personagem $alvoVenenado  = null;

    public function getDescricao(): string
    {
        return "Assassino - Alto ataque. Habilidade: Lâmina Envenenada (veneno por 3 turnos).";
    }

    public function getNomeHabilidade(): string { return "Lamina Envenenada"; }
    public function getCustoHabilidade(): int   { return self::CUSTO_HABILIDADE; }

    public function usarHabilidade(Personagem $alvo): string
    {
        $this->consumirEnergia(self::CUSTO_HABILIDADE);

        $danoInicial = max(self::DANO_MINIMO, $this->ataque - $alvo->getDefesa());
        $alvo->receberDano($danoInicial);

        $this->alvoVenenado          = $alvo;
        $this->turnosVenenoRestantes = self::TURNOS_VENENO;

        return "{$this->nome} usou Lâmina Envenenada em {$alvo->getNome()}! "
             . "Causou {$danoInicial} de dano e aplicou veneno "
             . "(" . self::DANO_VENENO . " de dano por " . self::TURNOS_VENENO . " turnos)! "
             . "{$alvo->getNome()} agora tem {$alvo->getHp()} HP.";
    }

    public function processarVeneno(): ?string
    {
        if ($this->turnosVenenoRestantes <= 0 || $this->alvoVenenado === null) {
            return null;
        }

        if (!$this->alvoVenenado->estaVivo()) {
            $this->turnosVenenoRestantes = 0;
            $this->alvoVenenado = null;
            return null;
        }

        $this->alvoVenenado->receberDano(self::DANO_VENENO);
        $this->turnosVenenoRestantes--;

        $msg = "Veneno causou " . self::DANO_VENENO . " de dano em {$this->alvoVenenado->getNome()}! "
             . "{$this->alvoVenenado->getNome()} agora tem {$this->alvoVenenado->getHp()} HP. "
             . "({$this->turnosVenenoRestantes} turnos restantes)";

        if ($this->turnosVenenoRestantes <= 0) {
            $this->alvoVenenado = null;
        }

        return $msg;
    }

    public function temVenenoAtivo(): bool
    {
        return $this->turnosVenenoRestantes > 0 && $this->alvoVenenado !== null;
    }
}