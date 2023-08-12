<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;

class EncontraNoMaisProfundo
{
    /**
     * Realiza uma busca na arvore por todos os NOS folhas
     * e retorna todos aqueles que estão no nivel mais baixo
     * @param  No    $arvore
     * @param  array $listaDeNo -> Utilizado para busca recursiva
     * @return No[]
     */
    public static function exec(No &$arvore, array $listaDeNo = []): array
    {
        if ($arvore->getFilhoDireitaNo() == null and $arvore->getFilhoEsquerdaNo() == null and $arvore->getFilhoCentroNo() == null) {
            $listaDeNo[] = empty($listaDeNo)
            ? $arvore
            : array_reduce(
                $listaDeNo,
                function (array $carry, No $newNo) {
                    $menores = array_filter($carry, function (No $NoOfList) use ($newNo) {
                        return $NoOfList->getLinhaNo() < $newNo->getLinhaNo();
                    });
                    $carry = array_diff($menores, $carry);
                    array_push($carry, $newNo);
                    return $carry;
                },
                []
            );
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
