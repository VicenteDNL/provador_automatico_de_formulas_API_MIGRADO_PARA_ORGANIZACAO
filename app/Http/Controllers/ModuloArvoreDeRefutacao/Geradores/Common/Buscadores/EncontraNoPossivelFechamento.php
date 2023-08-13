<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;

class EncontraNoPossivelFechamento
{
    /**
     * Realiza uma busca na arvore por um NO que ainda não foi fechado
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

        if (is_null($ramoDireito) and is_null($ramoEsquerdo) and is_null($ramoCentro) and $arvore->isFechamento() == false and $arvore->isFechado() == true) {
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
