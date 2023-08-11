<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores;

class FecharTodosNos
{
    public function fecharTodosNos($arvore, $listaNo)
    {
        foreach ($listaNo as $no) {
            $noFolha = $this->getNoPeloId($arvore, $no['nofechado']['idNo']); // o nó a ser fechado
            $noContradicao = $this->getNoPeloId($arvore, $no['noContradicao']['idNo']);

            $descendente = $this->isDecendente($noContradicao, $noFolha);

            if ($descendente == true) {
                if ($noContradicao->getValorNo()->getValorPredicado() == $noFolha->getValorNo()->getValorPredicado()) {
                    $negacaoContradicao = $noContradicao->getValorNo()->getNegadoPredicado();
                    $negacaoFolha = $noFolha->getValorNo()->getNegadoPredicado();

                    if ($negacaoContradicao == 1 and $negacaoFolha == 0) {
                        $noFolha->fechamentoNo();
                    } elseif ($negacaoContradicao == 0 and $negacaoFolha == 1) {
                        $noFolha->fechamentoNo();
                    } else {
                        return ['sucesso' => false, 'messagem' => 'Os argumentos iguais mas não contraditórios'];
                    }
                } else {
                    return ['sucesso' => false, 'messagem' => 'Os argumentos não são iguais'];
                }
            } else {
                return ['sucesso' => false, 'messagem' => 'O nó não pertence ao mesmo ramo'];
            }
        }
        return ['sucesso' => true, 'messagem' => '', 'arv' => $arvore];
    }
}
