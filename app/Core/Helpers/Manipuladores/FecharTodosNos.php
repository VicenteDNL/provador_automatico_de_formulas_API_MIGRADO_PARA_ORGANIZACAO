<?php

namespace App\Core\Helpers\Manipuladores;

use App\Core\Common\Models\Attempts\TentativaFechamento;
use App\Core\Common\Models\Steps\PassoFechamento;
use App\Core\Common\Models\Tree\No;
use App\Core\Helpers\Buscadores\EncontraNoPeloId;
use App\Core\Helpers\Validadores\IsDecendente;

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
                            'mensagem' => 'Os argumentos iguais mas não contraditórios',
                        ]);
                    }
                } else {
                    return new TentativaFechamento([
                        'sucesso'  => false,
                        'mensagem' => 'Os argumentos não são iguais',
                    ]);
                }
            } else {
                return new TentativaFechamento([
                    'sucesso'  => false,
                    'mensagem' => 'O nó não pertence ao mesmo ramo',
                ]);
            }
        }
        return new TentativaFechamento([
            'sucesso'  => true,
            'mensagem' => 'Fechados com sucesso',
            'arvore'   => $arvore,
            'passos'   => $passos,
        ]);
    }
}
