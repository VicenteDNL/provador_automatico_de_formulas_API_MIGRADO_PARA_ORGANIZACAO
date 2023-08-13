<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;

class EncontraNosFolha
{
    /**
     * Realiza uma busca na arvore por todos os NOS folhas
     * que ainda estão aberto e os retorna
     * @param  No    $arvore
     * @param  array $listaDeNo -> Utilizado para busca recursiva
     * @return No[]
     */
    public static function exec(No &$arvore, array $listaDeNo = []): array
    {
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if (is_null($ramoDireito) and is_null($ramoEsquerdo) and is_null($ramoCentro) and $arvore->isFechado() == false) {
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
