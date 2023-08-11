<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;

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
        if ($arvore->getLinhaNo() == $linha) {
            array_push($nos, $arvore);
        }

        if ($arvore->getFilhoCentroNo() != null) {
            $nos = self::exec($arvore->getFilhoCentroNo(), $linha, $nos);
        } elseif ($arvore->getFilhoEsquerdaNo() != null and $arvore->getFilhoDireitaNo() != null) {
            $nos = self::exec($arvore->getFilhoEsquerdaNo(), $linha, $nos);
            $nos = self::exec($arvore->getFilhoDireitaNo(), $linha, $nos);
        }
        return $nos;
    }
}
