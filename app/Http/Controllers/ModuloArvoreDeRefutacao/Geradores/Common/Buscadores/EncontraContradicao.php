<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Validadores\IsDecendente;

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

        if ($arvore->getValorNo()->getValorPredicado() == $no->getValorNo()->getValorPredicado()) {
            $negacaoNo = $no->getValorNo()->getNegadoPredicado();

            if ($negacaoNo == 1 and $arvore->getValorNo()->getNegadoPredicado() == 0) {
                if (IsDecendente::exec($arvore, $no)) {
                    return $arvore;
                }
            } elseif ($negacaoNo == 0 and $arvore->getValorNo()->getNegadoPredicado() == 1) {
                if (IsDecendente::exec($arvore, $no)) {
                    return $arvore;
                }
            } else {
                if ($arvore->getFilhoCentroNo() != null and $contradicao == null) {
                    $contradicao = self::exec($arvore->getFilhoCentroNo(), $no);
                }

                if ($arvore->getFilhoEsquerdaNo() != null and $contradicao == null) {
                    $contradicao = self::exec($arvore->getFilhoEsquerdaNo(), $no);
                }

                if ($arvore->getFilhoDireitaNo() != null and $contradicao == null) {
                    $contradicao = self::exec($arvore->getFilhoDireitaNo(), $no);
                }
                return $contradicao;
            }
        } else {
            if ($arvore->getFilhoCentroNo() != null and $contradicao == null) {
                $contradicao = self::exec($arvore->getFilhoCentroNo(), $no);
            }

            if ($arvore->getFilhoEsquerdaNo() != null and $contradicao == null) {
                $contradicao = self::exec($arvore->getFilhoEsquerdaNo(), $no);
            }

            if ($arvore->getFilhoDireitaNo() != null and $contradicao == null) {
                $contradicao = self::exec($arvore->getFilhoDireitaNo(), $no);
            }
            return $contradicao;
        }
    }
}
