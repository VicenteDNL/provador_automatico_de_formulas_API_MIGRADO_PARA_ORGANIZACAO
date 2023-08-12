<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;

class EncontraNosFolhasAberto
{
    /**
     * Esta funçao recebe com parametro a arvore atual, e retorna uma
     * array com a referencia de todos os nós folhas que não foram fechados pelo usuario
     * @param  No   $arvore
     * @param  No[] $nos
     * @return No[]
     * */
    public static function exec(No &$arvore, array $nos = []): array
    {
        if ($arvore->getFilhoDireitaNo() == null and $arvore->getFilhoEsquerdaNo() == null and $arvore->getFilhoCentroNo() == null and ($arvore->isFechado() == true && $arvore->isFechamento() == false)) {
            $nos[] = $arvore;
            return  $nos;
        } else {
            if ($arvore->getFilhoCentroNo() != null) {
                $nos = self::exec($arvore->getFilhoCentroNo(), $nos);
            }

            if ($arvore->getFilhoEsquerdaNo() != null) {
                $nos = self::exec($arvore->getFilhoEsquerdaNo(), $nos);
            }

            if ($arvore->getFilhoDireitaNo() != null) {
                $nos = self::exec($arvore->getFilhoDireitaNo(), $nos);
            }
            return $nos;
        }
    }
}
