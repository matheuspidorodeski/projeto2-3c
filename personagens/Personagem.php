<?php

abstract class Personagem
{
    const DANO_MINIMO   = 0;
    const BONUS_DEFESA  = 0;

    protected string $nome;
    protected int $hpMax;
    protected int $hp;
    protected int $ataque;
    protected int $defesa;
    protected int $energiaMax;
    protected int $energia;
    protected int $bonusDefesaTemp = 0;

    public function __construct(string $nome)
    {
        $this->nome    = $nome;
        $this->hp      = $this->hpMax;
        $this->energia = $this->energiaMax;
    }

    // Getters
    public function getNome(): string    { return $this->nome; }
    public function getHp(): int         { return $this->hp; }
    public function getHpMax(): int      { return $this->hpMax; }
    public function getEnergia(): int    { return $this->energia; }
    public function getEnergiaMax(): int { return $this->energiaMax; }
    public function getAtaque(): int     { return $this->ataque; }
    public function getDefesa(): int     { return $this->defesa + $this->bonusDefesaTemp; }
    public function getTipo(): string    { return get_class($this); }
    public function estaVivo(): bool     { return $this->hp > 0; }

    public function atacar(Personagem $alvo): int
    {
        $dano = max(self::DANO_MINIMO, $this->ataque - $alvo->getDefesa());
        $alvo->receberDano($dano);
        return $dano;
    }

    public function defender(): int
    {
        $this->bonusDefesaTemp = static::BONUS_DEFESA;
        return $this->bonusDefesaTemp;
    }

    public function resetarDefesaTemp(): void
    {
        $this->bonusDefesaTemp = 0;
    }

    public function receberDano(int $dano): void
    {
        $this->hp = max(0, $this->hp - $dano);
    }

    public function regenerarEnergia(int $quantidade): void
    {
        $this->energia = min($this->energiaMax, $this->energia + $quantidade);
    }

    protected function consumirEnergia(int $custo): void
    {
        if ($this->energia < $custo) {
            throw new EnergiaInsuficienteException(
                "{$this->nome} não tem energia suficiente! (precisa: {$custo}, tem: {$this->energia})"
            );
        }
        $this->energia -= $custo;
    }

    abstract public function usarHabilidade(Personagem $alvo): string;
    abstract public function getNomeHabilidade(): string;
    abstract public function getCustoHabilidade(): int;
    abstract public function getDescricao(): string;
}