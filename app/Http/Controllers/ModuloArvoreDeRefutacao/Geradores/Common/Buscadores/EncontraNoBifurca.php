<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PredicadoTipoEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Validadores\IsDecendente;

class EncontraNoBifurca
{
    /**
     *  Realiza uma busca na arvore por um NO que possui bifurcação e ainda nao utilizado
     *  e que e esteja no mesmo ramo do No que se deseja realizar a insercao.
     *  Retorna null caso não encontre (a busca é feita partindo do NO raiz até os NOS folhas)
     * @param  No      $arvore
     * @param  No      $noInsercao
     * @return No|null
     */
    public static function exec(No &$arvore, No &$noInsercao): ?No
    {
        $noBifucacao = null;

        if (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::DISJUNCAO, PredicadoTipoEnum::CONDICIONAL, PredicadoTipoEnum::BICONDICIONAL]) and $arvore->getValorNo()->getNegadoPredicado() == 0 and $arvore->isUtilizado() == false) {
            if (IsDecendente::exec($arvore, $noInsercao)) {
                return $arvore;
            }
        } elseif (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::CONJUNCAO, PredicadoTipoEnum::BICONDICIONAL]) and $arvore->getValorNo()->getNegadoPredicado() == 1 and $arvore->isUtilizado() == false) {
            if (IsDecendente::exec($arvore, $noInsercao)) {
                return $arvore;
            }
        } else {
            if ($arvore->getFilhoEsquerdaNo() != null and $noBifucacao == null) {
                $noBifucacao = self::exec($arvore->getFilhoEsquerdaNo(), $noInsercao);
            }

            if ($arvore->getFilhoCentroNo() != null and $noBifucacao == null) {
                $noBifucacao = self::exec($arvore->getFilhoCentroNo(), $noInsercao);
            }

            if ($arvore->getFilhoDireitaNo() != null and $noBifucacao == null) {
                $noBifucacao = self::exec($arvore->getFilhoDireitaNo(), $noInsercao);
            }
            return  $noBifucacao;
        }
    }
}
