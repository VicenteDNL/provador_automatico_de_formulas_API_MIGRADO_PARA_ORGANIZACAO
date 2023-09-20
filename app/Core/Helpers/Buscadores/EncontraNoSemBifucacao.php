<?php

namespace App\Core\Helpers\Buscadores;

use App\Core\Common\Models\Enums\PredicadoTipoEnum;
use App\Core\Common\Models\Tree\No;

class EncontraNoSemBifucacao
{
    /**
     *  Realiza uma busca na arvore por um NO que não possui bifurcação e ainda nao utilizado
     *  Retorna null caso não encontre (a busca é feita partindo do NO raiz até os NOS folhas)
     * @param  No      $arvore
     * @return No|null
     */
    public static function exec(No &$arvore): ?No
    {
        $noSemBifucacao = null;
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if ($arvore->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONJUNCAO and $arvore->getValorNo()->getNegadoPredicado() == 0 and $arvore->isUtilizado() == false) {
        } elseif (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::DISJUNCAO, PredicadoTipoEnum::CONDICIONAL]) and $arvore->getValorNo()->getNegadoPredicado() == 1 and $arvore->isUtilizado() == false) {
        } else {
            if (!is_null($ramoCentro) and is_null($noSemBifucacao)) {
                $noSemBifucacao = self::exec($ramoCentro);
            }

            if (!is_null($ramoEsquerdo) and is_null($noSemBifucacao)) {
                $noSemBifucacao = self::exec($ramoEsquerdo);
            }

            if (!is_null($ramoDireito) and is_null($noSemBifucacao)) {
                $noSemBifucacao = self::exec($ramoDireito);
            }
        }
        return $noSemBifucacao;
    }
}
