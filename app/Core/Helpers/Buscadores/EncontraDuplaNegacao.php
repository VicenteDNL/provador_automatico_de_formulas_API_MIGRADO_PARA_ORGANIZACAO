<?php

namespace App\Core\Helpers\Buscadores;

use App\Core\Common\Models\Tree\No;

class EncontraDuplaNegacao
{
    /**
     * Realiza uma busca na arvore por No que posse dupla negação
     * Retorna null caso nenhum No seja encontrado
     * @param  No      $arvore
     * @return No|null
     */
    public static function exec(No &$arvore): ?No
    {
        $negacao = null;
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if ($arvore->getValorNo()->getNegadoPredicado() >= 2 and $arvore->isUtilizado() == false) {
            return $arvore;
        } else {
            if (!is_null($ramoEsquerdo) and is_null($negacao)) {
                $negacao = self::exec($ramoEsquerdo);
            }

            if (!is_null($ramoCentro) and is_null($negacao)) {
                $negacao = self::exec($ramoCentro);
            }

            if (!is_null($ramoDireito) and is_null($negacao)) {
                $negacao = self::exec($ramoDireito);
            }
            return $negacao;
        }
    }
}
