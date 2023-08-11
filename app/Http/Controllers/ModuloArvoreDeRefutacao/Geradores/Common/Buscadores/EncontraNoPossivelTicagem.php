<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PredicadoTipoEnum;

class EncontraNoPossivelTicagem
{
    /**
     * Realiza uma busca na arvore por um NO que ainda não foi ticado
     * pelo usuario e retorna o primeiro NO encontrado
     * (a busca é feita percorrendo do centro -> esquerda -> direita)
     * @param  No      $arvore
     * @return No|null
     */
    public static function exec(No &$arvore): ?No
    {
        $proximoNo = null;

        if ($arvore->isUtilizado() == true and (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::DISJUNCAO, PredicadoTipoEnum::CONDICIONAL, PredicadoTipoEnum::BICONDICIONAL, PredicadoTipoEnum::CONJUNCAO]) or ($arvore->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::PREDICATIVO and $arvore->getValorNo()->getNegadoPredicado() >= 2)) and $arvore->isTicado() == false) {
            return $arvore;
        } else {
            if ($arvore->getFilhoCentroNo() != null and $proximoNo == null) {
                $proximoNo = self::exec($arvore->getFilhoCentroNo());
            }

            if ($arvore->getFilhoEsquerdaNo() != null and $proximoNo == null) {
                $proximoNo = self::exec($arvore->getFilhoEsquerdaNo());
            }

            if ($arvore->getFilhoDireitaNo() != null and $proximoNo == null) {
                $proximoNo = self::exec($arvore->getFilhoDireitaNo());
            }
            return $proximoNo;
        }
    }
}
