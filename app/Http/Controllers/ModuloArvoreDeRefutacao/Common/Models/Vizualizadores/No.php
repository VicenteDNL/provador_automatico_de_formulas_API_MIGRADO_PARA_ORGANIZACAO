<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No as ProcessadoresNo;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class No extends Serializa
{
    protected ProcessadoresNo $arv;
    protected string $str;
    protected int $idNo;
    protected int $linha;
    protected bool $noFolha;
    protected float $posX;
    protected float $posY;
    protected float $tmh;
    protected float $posXno;
    protected ?int $linhaDerivacao;
    protected float $posXlinhaDerivacao;
    protected bool $utilizado;
    protected bool $fechado;
    protected ?int $linhaContradicao;
    protected string $fill;
    protected int $strokeWidth;
    protected string $strokeColor;

    /**
     * @return ProcessadoresNo
     */
    public function getArv(): ProcessadoresNo
    {
        return $this->arv;
    }

    /**
     * @param  ProcessadoresNo $arv
     * @return void
     */
    public function setArv(ProcessadoresNo $arv): void
    {
        $this->arv = $arv;
    }

    /**
     * @return string
     */
    public function getStr(): string
    {
        return $this->str;
    }

    /**
     * @param  string $str
     * @return void
     */
    public function setStr(string $str): void
    {
        $this->str = $str;
    }

    /**
     * @return int
     */
    public function getIdNo(): int
    {
        return $this->idNo;
    }

    /**
     * @param  int  $idNo
     * @return void
     */
    public function setIdNo(int $idNo): void
    {
        $this->idNo = $idNo;
    }

    /**
     * @return int
     */
    public function getLinha(): int
    {
        return $this->linha;
    }

    /**
     * @param  int  $linha
     * @return void
     */
    public function setLinha(int $linha): void
    {
        $this->linha = $linha;
    }

    /**
     * @return bool
     */
    public function getNoFolha(): bool
    {
        return $this->noFolha;
    }

    /**
     * @param  bool $noFolha
     * @return void
     */
    public function setNoFolha(bool $noFolha): void
    {
        $this->noFolha = $noFolha;
    }

    /**
     * @return float
     */
    public function getPosX(): float
    {
        return $this->posX;
    }

    /**
     * @param  float $posX
     * @return void
     */
    public function setPosX(float $posX): void
    {
        $this->posX = $posX;
    }

    /**
     * @return float
     */
    public function getPosY(): float
    {
        return $this->posY;
    }

    /**
     * @param  float $posY
     * @return void
     */
    public function setPosY(float $posY): void
    {
        $this->posY = $posY;
    }

    /**
     * @return float
     */
    public function getTmh(): float
    {
        return $this->tmh;
    }

    /**
     * @param  float $tmh
     * @return void
     */
    public function setTmh(float $tmh): void
    {
        $this->tmh = $tmh;
    }

    /**
     * @return float
     */
    public function getPosXno(): float
    {
        return $this->posXno;
    }

    /**
     * @param  float $posXno
     * @return void
     */
    public function setPosXno(float $posXno): void
    {
        $this->posXno = $posXno;
    }

    /**
     * @return int|null
     */
    public function getLinhaDerivacao(): ?int
    {
        return $this->linhaDerivacao;
    }

    /**
     * @param  int  $linhaDerivacao
     * @return void
     */
    public function setLinhaDerivacao(int $linhaDerivacao): void
    {
        $this->linhaDerivacao = $linhaDerivacao;
    }

    /**
     * @return float
     */
    public function getPosXlinhaDerivacao(): float
    {
        return $this->posXlinhaDerivacao;
    }

    /**
     * @param  float $posXlinhaDerivacao
     * @return void
     */
    public function setPosXlinhaDerivacao(float $posXlinhaDerivacao): void
    {
        $this->posXlinhaDerivacao = $posXlinhaDerivacao;
    }

    /**
     * @return bool
     */
    public function getUtilizado(): bool
    {
        return $this->utilizado;
    }

    /**
     * @param  bool $utilizado
     * @return void
     */
    public function setUtilizado(bool $utilizado): void
    {
        $this->utilizado = $utilizado;
    }

    /**
     * @return bool
     */
    public function getFechado(): bool
    {
        return $this->fechado;
    }

    /**
     * @param  bool $fechado
     * @return void
     */
    public function setFechado(bool $fechado): void
    {
        $this->fechado = $fechado;
    }

    /**
     * @return int|null
     */
    public function getLinhaContradicao(): ?int
    {
        return $this->linhaContradicao;
    }

    /**
     * @param  int  $linhaContradicao
     * @return void
     */
    public function setLinhaContradicao(int $linhaContradicao): void
    {
        $this->linhaContradicao = $linhaContradicao;
    }

    /**
     * @return string
     */
    public function getFill(): string
    {
        return $this->fill;
    }

    /**
     * @param  string $fill
     * @return void
     */
    public function setFill(string $fill): void
    {
        $this->fill = $fill;
    }

    /**
     * @return int
     */
    public function getStrokeWidth(): int
    {
        return $this->strokeWidth;
    }

    /**
     * @param  int  $strokeWidth
     * @return void
     */
    public function setStrokeWidth(int $strokeWidth): void
    {
        $this->strokeWidth = $strokeWidth;
    }

    /**
     * @return string
     */
    public function getStrokeColor(): string
    {
        return $this->strokeColor;
    }

    /**
     * @param  string $strokeColor
     * @return void
     */
    public function setStrokeColor(string $strokeColor): void
    {
        $this->strokeColor = $strokeColor;
    }
}
