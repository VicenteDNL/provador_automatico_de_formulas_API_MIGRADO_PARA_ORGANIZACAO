<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;

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

        if ($arvore->getFilhoDireitaNo() == null and $arvore->getFilhoEsquerdaNo() == null and $arvore->getFilhoCentroNo() == null and $arvore->isFechamento() == false and $arvore->isFechado() == true) {
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
