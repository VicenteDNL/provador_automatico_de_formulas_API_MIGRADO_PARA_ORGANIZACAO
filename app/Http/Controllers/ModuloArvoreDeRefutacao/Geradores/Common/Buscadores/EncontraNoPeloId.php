<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;

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
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if ($arvore->getIdNo() == $id) {
            return $arvore;
        } else {
            if (!is_null($ramoEsquerdo) and is_null($no)) {
                $no = self::exec($ramoEsquerdo, $id);
            }

            if (!is_null($ramoCentro) and is_null($no)) {
                $no = self::exec($ramoCentro, $id);
            }

            if (!is_null($ramoDireito) and is_null($no)) {
                $no = self::exec($ramoDireito, $id);
            }
            return $no;
        }
    }
}
