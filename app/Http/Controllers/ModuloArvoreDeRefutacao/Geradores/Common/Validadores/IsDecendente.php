<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Validadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;

class IsDecendente
{
    /**
     *  Verificar se o no raiz da arvore está no mesmo ramo no de interesse,
     * @param  No   $arvore
     * @param  No   $no
     * @return bool
     */
    public static function exec(No $arvore, No $no): bool
    {
        $noDescendente = false;

        if ($arvore->getValorNo() === $no->getValorNo()) {
            return true;
        } else {
            if ($arvore->getFilhoCentroNo() != null and $noDescendente == false) {
                $noDescendente = self::exec($arvore->getFilhoCentroNo(), $no);
            }

            if ($arvore->getFilhoEsquerdaNo() != null and $noDescendente == false) {
                $noDescendente = self::exec($arvore->getFilhoEsquerdaNo(), $no);
            }

            if ($arvore->getFilhoDireitaNo() != null and $noDescendente == false) {
                $noDescendente = self::exec($arvore->getFilhoDireitaNo(), $no);
            }
            return $noDescendente;
        }
    }
}
