<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PredicadoTipoEnum;

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
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if ($arvore->isUtilizado() == true and (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::DISJUNCAO, PredicadoTipoEnum::CONDICIONAL, PredicadoTipoEnum::BICONDICIONAL, PredicadoTipoEnum::CONJUNCAO]) or ($arvore->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::PREDICATIVO and $arvore->getValorNo()->getNegadoPredicado() >= 2)) and $arvore->isTicado() == false) {
            return $arvore;
        } else {
            if (!is_null($ramoCentro) and is_null($proximoNo)) {
                $proximoNo = self::exec($ramoCentro);
            }

            if (!is_null($ramoEsquerdo) and is_null($proximoNo)) {
                $proximoNo = self::exec($ramoEsquerdo);
            }

            if (!is_null($ramoDireito) and is_null($proximoNo)) {
                $proximoNo = self::exec($ramoDireito);
            }
            return $proximoNo;
        }
    }
}
