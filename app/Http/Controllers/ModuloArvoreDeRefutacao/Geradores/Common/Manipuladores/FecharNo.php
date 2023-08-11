<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores;

class FecharNo
{
    public function fecharNo($arvore, $folha, $contradicao)
    {
        $noContradicao = $this->getNoPeloId($arvore, $contradicao['idNo']);
        $noFolha = $this->getNoPeloId($arvore, $folha['idNo']);
        $descendente = $this->isDecendente($noContradicao, $noFolha);

        if ($descendente == true) {
            if ($noContradicao->getValorNo()->getValorPredicado() == $noFolha->getValorNo()->getValorPredicado()) {
                $negacaoContradicao = $noContradicao->getValorNo()->getNegadoPredicado();
                $negacaoFolha = $noFolha->getValorNo()->getNegadoPredicado();

                if ($negacaoContradicao == 1 and $negacaoFolha == 0) {
                    if ($noFolha->isFechamento()) {
                        return ['sucesso' => false, 'messagem' => 'O ramo já foi fechado'];
                    }
                    $noFolha->fechamentoNo();
                    return ['sucesso' => true, 'messagem' => '', 'arv' => $arvore];
                } elseif ($negacaoContradicao == 0 and $negacaoFolha == 1) {
                    if ($noFolha->isFechamento()) {
                        return ['sucesso' => false, 'messagem' => 'O ramo já foi fechado'];
                    }
                    $noFolha->fechamentoNo();
                    return ['sucesso' => true, 'messagem' => '', 'arv' => $arvore];
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
}
