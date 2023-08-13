<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PredicadoTipoEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Validadores\IsDecendente;

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
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::DISJUNCAO, PredicadoTipoEnum::CONDICIONAL, PredicadoTipoEnum::BICONDICIONAL]) and $arvore->getValorNo()->getNegadoPredicado() == 0 and $arvore->isUtilizado() == false) {
            if (IsDecendente::exec($arvore, $noInsercao)) {
                return $arvore;
            }
        } elseif (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::CONJUNCAO, PredicadoTipoEnum::BICONDICIONAL]) and $arvore->getValorNo()->getNegadoPredicado() == 1 and $arvore->isUtilizado() == false) {
            if (IsDecendente::exec($arvore, $noInsercao)) {
                return $arvore;
            }
        } else {
            if (!is_null($ramoEsquerdo) and is_null($noBifucacao)) {
                $noBifucacao = self::exec($ramoEsquerdo, $noInsercao);
            }

            if (!is_null($ramoCentro) and is_null($noBifucacao)) {
                $noBifucacao = self::exec($ramoCentro, $noInsercao);
            }

            if (!is_null($ramoDireito) and is_null($noBifucacao)) {
                $noBifucacao = self::exec($ramoDireito, $noInsercao);
            }
            return  $noBifucacao;
        }
    }
}
