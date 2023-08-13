<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Validadores\IsDecendente;

class EncontraDuplaNegacao
{
    /**
     * Realiza uma busca na arvore por No que posse dupla negação
     * e que esteja no mesmo ramo do No que se deseja realizar a insercao.
     * Retorna null caso nenhum No seja encontrado
     * @param  No      $arvore
     * @param  No      $noInsercao
     * @return No|null
     */
    public static function exec(No &$arvore, No &$noInsercao): ?No
    {
        $negacao = null;
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if ($arvore->getValorNo()->getNegadoPredicado() >= 2 and $arvore->isUtilizado() == false) {
            if (IsDecendente::exec($arvore, $noInsercao)) {
                return $arvore;
            }
        } else {
            if (!is_null($ramoEsquerdo) and is_null($negacao)) {
                $negacao = self::exec($ramoEsquerdo, $noInsercao);
            }

            if (!is_null($ramoCentro) and is_null($negacao)) {
                $negacao = self::exec($ramoCentro, $noInsercao);
            }

            if (!is_null($ramoDireito) and is_null($negacao)) {
                $negacao = self::exec($ramoDireito, $noInsercao);
            }
            return $negacao;
        }
    }
}
