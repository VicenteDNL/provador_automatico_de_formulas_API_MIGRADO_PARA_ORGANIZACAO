<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Visualizacao;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Serializa;

class No extends Serializa
{
    public mixed $arv;
    public string $str;
    public int $idNo;
    public int $linha;
    public bool $noFolha;
    public float $posX;
    public float $posY;
    public float $tmh;
    public float $posXno;
    public int | null $linhaDerivacao;
    public float $posXlinhaDerivacao;
    public bool $utilizado;
    public bool $fechado;
    public int | null $linhaContradicao;
    public string $fill;
    public int $strokeWidth;
    public string $strokeColor;
}
