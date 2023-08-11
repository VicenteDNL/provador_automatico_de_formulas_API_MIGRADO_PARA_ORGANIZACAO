<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;

class EncontraTodosNosFolha
{
    /**
     * Realiza uma busca na arvore por todos os NOS folhas
     * @param  No    $arvore
     * @param  array $listaDeNo -> Utilizado para busca recursiva
     * @return No[]
     */
    public static function exec(No &$arvore, array $listaDeNo = []): array
    {
        if ($arvore->getFilhoDireitaNo() == null and $arvore->getFilhoEsquerdaNo() == null and $arvore->getFilhoCentroNo() == null) {
            $listaDeNo[] = $arvore;
            return  $listaDeNo;
        } else {
            if ($arvore->getFilhoCentroNo() != null) {
                $listaDeNo = self::exec($arvore->getFilhoCentroNo(), $listaDeNo);
            }

            if ($arvore->getFilhoEsquerdaNo() != null) {
                $listaDeNo = self::exec($arvore->getFilhoEsquerdaNo(), $listaDeNo);
            }

            if ($arvore->getFilhoDireitaNo() != null) {
                $listaDeNo = self::exec($arvore->getFilhoDireitaNo(), $listaDeNo);
            }
            return $listaDeNo;
        }
    }
}
