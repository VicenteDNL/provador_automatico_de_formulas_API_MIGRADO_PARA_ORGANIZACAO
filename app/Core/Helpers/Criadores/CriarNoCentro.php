<?php

namespace App\Core\Helpers\Criadores;

use App\Core\Common\Models\DerivationRules\RegrasResponse;
use App\Core\Common\Models\Tree\No;
use App\Core\Helpers\Buscadores\EncontraContradicao;

class CriarNoCentro
{
    /**
     * Responsavel por inserir novos NOS no centro do NO de insercao e
     * tambem aplicar a validação de contradição caso exista
     * @param No             $noInsercao
     * @param No             $arvore
     * @param RegrasResponse $filhos
     * @param int            $linhaDerivado
     * @param int            $idNo
     */
    public static function exec(No &$noInsercao, No &$arvore, RegrasResponse $filhos, int $linhaDerivado, int $idNo): void
    {
        $noInsercao->setFilhoCentroNo(new No($idNo, $filhos->getCentro()[0], null, null, null, $noInsercao->getLinhaNo() + 1, null, $linhaDerivado, false, false));
        $contradicao = EncontraContradicao::exec($arvore, $noInsercao->getFilhoCentroNo());

        if (!is_null($contradicao)) {
            $noInsercao->getFilhoCentroNo()->fecharRamo($contradicao->getLinhaNo());
        }
    }
}
