<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores;

class TicarNo
{
    public function ticarNo(No $arvore, No $no)
    {
        $noTicado = $this->getNoPeloId($arvore, $no['idNo']); // o nó a ser ticado

        if (($noTicado->getValorNo()->getTipoPredicado() == 'PREMISSA' or $noTicado->getValorNo()->getTipoPredicado() == 'CONCLUSAO' or $noTicado->getValorNo()->getTipoPredicado() == 'PREDICATIVO') and $noTicado->getValorNo()->getNegadoPredicado() < 2) {
            return ['sucesso' => false, 'messagem' => 'Este argumento não pode ser ticado, pois não existe derivação'];
        } else {
            if ($noTicado->isUtilizado() == true) {
                if ($noTicado->isTicado() == true) {
                    return ['sucesso' => false, 'messagem' => 'Este nó já foi ticado'];
                } else {
                    $noTicado->ticarNo();
                    return ['sucesso' => true, 'arv' => $arvore];
                }
            } else {
                return ['sucesso' => false, 'messagem' => 'Este nó ainda não foi deriavado'];
            }
        }
    }
}
