<?php

namespace App\Core\Helpers\Buscadores;

use App\Core\Common\Models\Tree\No;

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
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if (is_null($ramoDireito) and is_null($ramoEsquerdo) and is_null($ramoCentro) and ($arvore->isFechado() == true && $arvore->isFechamento() == false)) {
            $nos[] = $arvore;
            return  $nos;
        } else {
            if (!is_null($ramoCentro)) {
                $nos = self::exec($ramoCentro, $nos);
            }

            if (!is_null($ramoEsquerdo)) {
                $nos = self::exec($ramoEsquerdo, $nos);
            }

            if (!is_null($ramoDireito)) {
                $nos = self::exec($ramoDireito, $nos);
            }
            return $nos;
        }
    }
}
