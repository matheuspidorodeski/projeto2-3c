<?php

class Mago extends Personagem
{
    const BONUS_DEFESA      = 8;
    const CUSTO_HABILIDADE  = 35;
    const DANO_HABILIDADE   = 55;
    const REGEN_ENERGIA     = 15;

    protected int $hpMax      = 85;
    protected int $ataque     = 32;
    protected int $defesa     = 6;
    protected int $energiaMax = 120;
    protected int $energia    = 120;

    public function getDescricao(): string
    {
        return "Mago - Alto ataque e muita energia. Habilidade: Bola de Fogo (ignora 50% da defesa).";
    }

    public function getNomeHabilidade(): string { return "Bola de Fogo"; }
    public function getCustoHabilidade(): int   { return self::CUSTO_HABILIDADE; }

    public function usarHabilidade(Personagem $alvo): string
    {
        $this->consumirEnergia(self::CUSTO_HABILIDADE);
        $defesaEfetiva = (int) floor($alvo->getDefesa() * 0.5);
        $dano = max(self::DANO_MINIMO, self::DANO_HABILIDADE - $defesaEfetiva);
        $alvo->receberDano($dano);
        return "{$this->nome} lançou Bola de Fogo em {$alvo->getNome()} causando {$dano} de dano "
             . "(ignorou metade da defesa)! {$alvo->getNome()} agora tem {$alvo->getHp()} HP.";
    }
}