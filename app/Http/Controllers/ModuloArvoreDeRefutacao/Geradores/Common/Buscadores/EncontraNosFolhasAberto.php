<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

class EncontraNosFolhasAberto
{
    /*esta funçao recebe com parametro a arvore atual, e retorna uma array com a referencia de todos os nós folhas que não foram fechados pelo usuario*/
    public function getNosFolhasAberto($arvore, $ListaDeNo = null)
    {
        if ($arvore->getFilhoDireitaNo() == null and $arvore->getFilhoEsquerdaNo() == null and $arvore->getFilhoCentroNo() == null and ($arvore->isFechado() == true && $arvore->isFechamento() == false)) {
            $ListaDeNo[] = $arvore;
            return  $ListaDeNo;
        } else {
            if ($arvore->getFilhoCentroNo() != null) {
                $ListaDeNo = $this->getNosFolhasAberto($arvore->getFilhoCentroNo(), $ListaDeNo);
            }

            if ($arvore->getFilhoEsquerdaNo() != null) {
                $ListaDeNo = $this->getNosFolhasAberto($arvore->getFilhoEsquerdaNo(), $ListaDeNo);
            }

            if ($arvore->getFilhoDireitaNo() != null) {
                $ListaDeNo = $this->getNosFolhasAberto($arvore->getFilhoDireitaNo(), $ListaDeNo);
            }
            return $ListaDeNo;
        }
    }
}
