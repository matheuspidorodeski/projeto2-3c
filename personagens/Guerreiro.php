<?php

class Guerreiro extends Personagem
{
    const BONUS_DEFESA      = 15;
    const CUSTO_HABILIDADE  = 25;
    const DANO_HABILIDADE   = 45;
    const REGEN_ENERGIA     = 10;

    protected int $hpMax      = 140;
    protected int $ataque     = 28;
    protected int $defesa     = 12;
    protected int $energiaMax = 80;
    protected int $energia    = 80;

    public function getDescricao(): string
    {
        return "Guerreiro - Alto HP e boa defesa. Habilidade: Golpe Devastador (dano fixo elevado).";
    }

    public function getNomeHabilidade(): string { return "Golpe Devastador"; }
    public function getCustoHabilidade(): int   { return self::CUSTO_HABILIDADE; }

    public function usarHabilidade(Personagem $alvo): string
    {
        $this->consumirEnergia(self::CUSTO_HABILIDADE);
        $dano = max(self::DANO_MINIMO, self::DANO_HABILIDADE - $alvo->getDefesa());
        $alvo->receberDano($dano);
        return "{$this->nome} usou Golpe Devastador em {$alvo->getNome()} causando {$dano} de dano! "
             . "{$alvo->getNome()} agora tem {$alvo->getHp()} HP.";
    }
}