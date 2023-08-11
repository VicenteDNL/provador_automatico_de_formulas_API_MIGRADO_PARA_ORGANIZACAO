<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores;

class TicarTodosNos
{
    public function ticarTodosNos($arvore, $listaNo)
    {
        foreach ($listaNo as $no) {
            $noTicado = $this->getNoPeloId($arvore, $no['idNo']); // o nó a ser ticado

            if (($noTicado->getValorNo()->getTipoPredicado() == 'PREMISSA' or $noTicado->getValorNo()->getTipoPredicado() == 'CONCLUSAO' or $noTicado->getValorNo()->getTipoPredicado() == 'PREDICATIVO') and $noTicado->getValorNo()->getNegadoPredicado() < 2) {
                return ['sucesso' => false, 'messagem' => "Não existe derivação para o argumento'" . $noTicado->getStringNo() . "' da linha'" . $noTicado->getLinhaNo() . "'"];
            } else {
                if ($noTicado->isUtilizado() == true) {
                    if ($noTicado->isTicado() == true) {
                        return ['sucesso' => false, 'messagem' => "O nó '" . $noTicado->getStringNo() . "' da linha '" . $noTicado->getLinhaNo() . "' já foi ticado"];
                    } else {
                        $noTicado->ticarNo();
                    }
                } else {
                    return ['sucesso' => false, 'messagem' => "O nó '" . $noTicado->getStringNo() . "' da linha '" . $noTicado->getLinhaNo() . ' ainda não foi deriavado'];
                }
            }
        }
        return ['sucesso' => true, 'arv' => $arvore];
    }
}
