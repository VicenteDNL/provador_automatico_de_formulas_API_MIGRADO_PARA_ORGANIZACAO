<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PredicadoTipoEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Validadores\IsDecendente;

class EncontraNoSemBifucacao
{
    /**
     *  Realiza uma busca na arvore por um NO que não possui bifurcação e ainda nao utilizado
     *  e que e esteja no mesmo ramo do No que se deseja realizar a insercao.
     *  Retorna null caso não encontre (a busca é feita partindo do NO raiz até os NOS folhas)
     * @param  No      $arvore
     * @param  No      $noInsercao
     * @return No|null
     */
    public static function exec(No &$arvore, No &$noInsercao): ?No
    {
        $noSemBifucacao = null;

        if ($arvore->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONJUNCAO and $arvore->getValorNo()->getNegadoPredicado() == 0 and $arvore->isUtilizado() == false) {
            if (IsDecendente::exec($arvore, $noInsercao)) {
                return $arvore;
            }
        } elseif (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::DISJUNCAO, PredicadoTipoEnum::CONDICIONAL]) and $arvore->getValorNo()->getNegadoPredicado() == 1 and $arvore->isUtilizado() == false) {
            if (IsDecendente::exec($arvore, $noInsercao)) {
                return $arvore;
            }
        } else {
            if ($arvore->getFilhoEsquerdaNo() != null and $noSemBifucacao == null) {
                $noSemBifucacao = self::exec($arvore->getFilhoEsquerdaNo(), $noInsercao);
            }

            if ($arvore->getFilhoCentroNo() != null and $noSemBifucacao == null) {
                $noSemBifucacao = self::exec($arvore->getFilhoCentroNo(), $noInsercao);
            }

            if ($arvore->getFilhoDireitaNo() != null and $noSemBifucacao == null) {
                $noSemBifucacao = self::exec($arvore->getFilhoDireitaNo(), $noInsercao);
            }
            return $noSemBifucacao;
        }
    }
}
