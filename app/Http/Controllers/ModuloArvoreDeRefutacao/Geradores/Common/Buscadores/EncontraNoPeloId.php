<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;

class EncontraNoPeloId
{
    /**
     * Realiza uma busca na árvore e retorna o NO que possui o id correspondente
     * @param  No      $arvore
     * @param  int     $id
     * @return No|null
     */
    public static function exec(No &$arvore, int $id): ?No
    {
        $no = null;

        if ($arvore->getIdNo() == $id) {
            return $arvore;
        } else {
            if ($arvore->getFilhoEsquerdaNo() != null and $no == null) {
                ;
                $no = self::exec($arvore->getFilhoEsquerdaNo(), $id);
            }

            if ($arvore->getFilhoCentroNo() != null and $no == null) {
                $no = self::exec($arvore->getFilhoCentroNo(), $id);
            }

            if ($arvore->getFilhoDireitaNo() != null and $no == null) {
                $no = self::exec($arvore->getFilhoDireitaNo(), $id);
            }
            return $no;
        }
    }
}
