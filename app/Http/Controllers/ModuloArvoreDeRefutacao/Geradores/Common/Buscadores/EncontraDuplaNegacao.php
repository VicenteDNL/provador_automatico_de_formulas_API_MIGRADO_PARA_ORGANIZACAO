<?php

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Validadores\IsDecendente;

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

        if ($arvore->getValorNo()->getNegadoPredicado() >= 2 and $arvore->isUtilizado() == false) {
            if (IsDecendente::exec($arvore, $noInsercao)) {
                return $arvore;
            }
        } else {
            if ($arvore->getFilhoEsquerdaNo() != null and $negacao == null) {
                $negacao = self::exec($arvore->getFilhoEsquerdaNo(), $noInsercao);
            }

            if ($arvore->getFilhoCentroNo() != null and $negacao == null) {
                $negacao = self::exec($arvore->getFilhoCentroNo(), $noInsercao);
            }

            if ($arvore->getFilhoDireitaNo() != null and $negacao == null) {
                $negacao = self::exec($arvore->getFilhoDireitaNo(), $noInsercao);
            }
            return $negacao;
        }
    }
}
