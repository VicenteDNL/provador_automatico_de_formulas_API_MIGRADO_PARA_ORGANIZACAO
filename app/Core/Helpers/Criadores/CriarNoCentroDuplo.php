<?php

namespace App\Core\Helpers\Criadores;

use App\Core\Common\Models\DerivationRules\RegrasResponse;
use App\Core\Common\Models\Tree\No;
use App\Core\Helpers\Buscadores\EncontraContradicao;

class CriarNoCentroDuplo
{
    /**
     * Responsavel por inserir novos NOS no centro do NO de insercao e
     * tambem aplicar a validação de contradição caso exista
     * @param No             $noInsercao
     * @param No             $arvore
     * @param RegrasResponse $filhos
     * @param int            $linhaDerivado
     * @param Array<int>     $idsNo
     */
    public static function exec(No &$noInsercao, No &$arvore, RegrasResponse $filhos, int $linhaDerivado, array $idsNo): void
    {
        $primeiroNo = new No($idsNo[0], $filhos->getCentro()[0], null, null, null, $noInsercao->getLinhaNo() + 1, null, $linhaDerivado, false, false);
        $segundoNo = new No($idsNo[1], $filhos->getCentro()[1], null, null, null, $noInsercao->getLinhaNo() + 1, null, $linhaDerivado, false, false);

        $noInsercao->setFilhoCentroNo($primeiroNo);
        $noInsercao->getFilhoCentroNo()->setFilhoCentroNo($segundoNo);

        $contradicaoPrim = EncontraContradicao::exec($arvore, $noInsercao->getFilhoCentroNo());

        $contradicaoSeg = EncontraContradicao::exec($arvore, $noInsercao->getFilhoCentroNo()->getFilhoCentroNo());

        if (!is_null($contradicaoPrim) and is_null($contradicaoSeg)) {
            $noInsercao->getFilhoCentroNo()->removeFilhoCentroNo();
            $noInsercao->removeFilhoCentroNo();

            $noInsercao->setFilhoCentroNo($segundoNo);
            $noInsercao->getFilhoCentroNo()->setFilhoCentroNo($primeiroNo);

            $primeiroNo->setLinhaNo($noInsercao->getLinhaNo() + 2);
            $segundoNo->setLinhaNo($noInsercao->getLinhaNo() + 1);
            $noInsercao->getFilhoCentroNo()->getFilhoCentroNo()->fecharRamo($contradicaoPrim->getLinhaNo());
        } elseif ((!is_null($contradicaoPrim) and is_null($contradicaoSeg)) or (is_null($contradicaoPrim) and !is_null($contradicaoSeg))) {
            $primeiroNo->setLinhaNo($noInsercao->getLinhaNo() + 1);
            $segundoNo->setLinhaNo($noInsercao->getLinhaNo() + 2);
            $noInsercao->getFilhoCentroNo()->getFilhoCentroNo()->fecharRamo($contradicaoSeg->getLinhaNo());
        } else {
            $primeiroNo->setLinhaNo($noInsercao->getLinhaNo() + 1);
            $segundoNo->setLinhaNo($noInsercao->getLinhaNo() + 2);
        }
    }
}
