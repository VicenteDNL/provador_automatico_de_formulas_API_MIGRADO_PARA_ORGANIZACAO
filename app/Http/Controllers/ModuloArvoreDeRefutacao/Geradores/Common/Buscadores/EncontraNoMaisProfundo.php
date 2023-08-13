<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;

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
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if (is_null($ramoDireito) and is_null($ramoEsquerdo) and is_null($ramoCentro)) {
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
