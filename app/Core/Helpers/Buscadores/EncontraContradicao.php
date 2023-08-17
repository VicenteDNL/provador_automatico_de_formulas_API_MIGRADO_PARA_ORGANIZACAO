<?php

namespace App\Core\Helpers\Buscadores;

use App\Core\Common\Models\Tree\No;
use App\Core\Helpers\Validadores\IsDecendente;

class EncontraContradicao
{
    /**
     * Recebe uma arvore e um NO, e verifica se existe uma
     * contradicao para o NO na arvore, se verdadeiro retorna o NO contraditorio
     * @param  No      $arvore
     * @param  No      $no
     * @return No|null
     */
    public static function exec(No $arvore, No $no): ?No
    {
        $contradicao = null;
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if ($arvore->getValorNo()->getValorPredicado() == $no->getValorNo()->getValorPredicado()) {
            $negacaoNo = $no->getValorNo()->getNegadoPredicado();

            if ($negacaoNo == 1 and $arvore->getValorNo()->getNegadoPredicado() == 0 and IsDecendente::exec($arvore, $no)) {
                return $arvore;
            } elseif ($negacaoNo == 0 and $arvore->getValorNo()->getNegadoPredicado() == 1 and IsDecendente::exec($arvore, $no)) {
                return $arvore;
            } else {
                if (!is_null($ramoCentro) and is_null($contradicao)) {
                    $contradicao = self::exec($ramoCentro, $no);
                }

                if (!is_null($ramoEsquerdo) and is_null($contradicao)) {
                    $contradicao = self::exec($ramoEsquerdo, $no);
                }

                if (!is_null($ramoDireito) and is_null($contradicao)) {
                    $contradicao = self::exec($ramoDireito, $no);
                }
                return $contradicao;
            }
        } else {
            if (!is_null($ramoCentro) and is_null($contradicao)) {
                $contradicao = self::exec($ramoCentro, $no);
            }

            if (!is_null($ramoEsquerdo) and is_null($contradicao)) {
                $contradicao = self::exec($ramoEsquerdo, $no);
            }

            if (!is_null($ramoDireito) and is_null($contradicao)) {
                $contradicao = self::exec($ramoDireito, $no);
            }
            return $contradicao;
        }
    }
}
