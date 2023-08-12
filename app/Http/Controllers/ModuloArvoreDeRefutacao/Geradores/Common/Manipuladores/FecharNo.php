<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\TentativaFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores\EncontraNoPeloId;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Validadores\IsDecendente;

class FecharNo
{
    /**
     *
     * @param No              $arvore
     * @param PassoFechamento $passo
     */
    public static function exec(No &$arvore, PassoFechamento $passo): TentativaFechamento
    {
        $noContradicao = EncontraNoPeloId::exec($arvore, $passo->getIdNoContraditorio());
        $noFolha = EncontraNoPeloId::exec($arvore, $passo->getIdNoFolha());

        if (IsDecendente::exec($noContradicao, $noFolha)) {
            if ($noContradicao->getValorNo()->getValorPredicado() == $noFolha->getValorNo()->getValorPredicado()) {
                $negacaoContradicao = $noContradicao->getValorNo()->getNegadoPredicado();
                $negacaoFolha = $noFolha->getValorNo()->getNegadoPredicado();

                if ($negacaoContradicao == 1 and $negacaoFolha == 0) {
                    if ($noFolha->isFechamento()) {
                        return new TentativaFechamento([
                            'sucesso'  => false,
                            'messagem' => 'O ramo já foi fechado',
                        ]);
                    }
                    $noFolha->fechamentoNo();
                    return new TentativaFechamento(
                        [
                            'sucesso'  => true,
                            'messagem' => 'Fechado com sucesso']
                    );
                } elseif ($negacaoContradicao == 0 and $negacaoFolha == 1) {
                    if ($noFolha->isFechamento()) {
                        return new TentativaFechamento([
                            'sucesso'  => false,
                            'messagem' => 'O ramo já foi fechado',
                        ]);
                    }
                    $noFolha->fechamentoNo();
                    return new TentativaFechamento([
                        'sucesso'  => true,
                        'messagem' => 'Fechado com sucesso',
                    ]);
                } else {
                    return new TentativaFechamento([
                        'sucesso'  => false,
                        'messagem' => 'Os argumentos iguais mas não contraditórios',
                    ]);
                }
            } else {
                return new TentativaFechamento([
                    'sucesso'  => false,
                    'messagem' => 'Os argumentos não são iguais',
                ]);
            }
        } else {
            return new TentativaFechamento([
                'sucesso'  => false,
                'messagem' => 'O nó não pertence ao mesmo ramo',
            ]);
        }
    }
}
