<?php

namespace App\Core\Helpers\Buscadores;

use App\Core\Common\Models\Tree\No;

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
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if (is_null($ramoDireito) and is_null($ramoEsquerdo) and is_null($ramoCentro)) {
            $listaDeNo[] = $arvore;
            return  $listaDeNo;
        } else {
            if (!is_null($ramoCentro)) {
                $listaDeNo = self::exec($ramoCentro, $listaDeNo);
            }

            if (!is_null($ramoEsquerdo)) {
                $listaDeNo = self::exec($ramoEsquerdo, $listaDeNo);
            }

            if (!is_null($ramoDireito)) {
                $listaDeNo = self::exec($ramoDireito, $listaDeNo);
            }
            return $listaDeNo;
        }
    }
}
