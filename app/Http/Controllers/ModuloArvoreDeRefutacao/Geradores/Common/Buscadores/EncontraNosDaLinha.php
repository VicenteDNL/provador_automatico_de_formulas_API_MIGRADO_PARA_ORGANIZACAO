<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;

class EncontraNosDaLinha
{
    /**
     * Realiza uma busca na árvore e retorna uma lista de todos os nos que pertencem a linha informada
     * @param  No   $arvore
     * @param  int  $linha
     * @param  No[] $nos    ->Utilizado para busca recursiva
     * @return No[]
     */
    public static function exec(No &$arvore, int $linha, array $nos = []): array
    {
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if ($arvore->getLinhaNo() == $linha) {
            array_push($nos, $arvore);
        }

        if (!is_null($ramoCentro)) {
            $nos = self::exec($ramoCentro, $linha, $nos);
        } elseif (!is_null($ramoEsquerdo) and !is_null($ramoDireito)) {
            $nos = self::exec($ramoEsquerdo, $linha, $nos);
            $nos = self::exec($ramoDireito, $linha, $nos);
        }
        return $nos;
    }
}
