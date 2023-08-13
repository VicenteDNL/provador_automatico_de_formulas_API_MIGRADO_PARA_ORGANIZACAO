<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Manipuladores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\TentativaFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoPeloId;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Validadores\IsDecendente;

class FecharTodosNos
{
    /**
     * @param No                $arvore
     * @param PassoFechamento[] $passos
     */
    public static function exec(No &$arvore, array $passos): TentativaFechamento
    {
        foreach ($passos as $no) {
            $noFolha = EncontraNoPeloId::exec($arvore, $no->getIdNoFolha()); // o nó a ser fechado
            $noContradicao = EncontraNoPeloId::exec($arvore, $no->getIdNoContraditorio());

            if (IsDecendente::exec($noContradicao, $noFolha)) {
                if ($noContradicao->getValorNo()->getValorPredicado() == $noFolha->getValorNo()->getValorPredicado()) {
                    $negacaoContradicao = $noContradicao->getValorNo()->getNegadoPredicado();
                    $negacaoFolha = $noFolha->getValorNo()->getNegadoPredicado();

                    if ($negacaoContradicao == 1 and $negacaoFolha == 0) {
                        $noFolha->fechamentoNo();
                    } elseif ($negacaoContradicao == 0 and $negacaoFolha == 1) {
                        $noFolha->fechamentoNo();
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
        return new TentativaFechamento([
            'sucesso'  => true,
            'messagem' => 'Fechados com sucesso',
        ]);
    }
}
