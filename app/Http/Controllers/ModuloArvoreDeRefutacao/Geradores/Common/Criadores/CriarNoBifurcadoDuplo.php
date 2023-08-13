<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Criadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\RegrasResponse;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraContradicao;

class CriarNoBifurcadoDuplo
{
    /**
     * Responsavel por inserir novos NOS na esquerda e direira do NO de insercao e
     * tambem aplicar a validação de contradição caso exista
     * @param No             $noInsercao
     * @param No             $arvore
     * @param RegrasResponse $filhos
     * @param int            $linhaDerivado
     * @param Array<int>     $idsNo
     */
    public static function exec(No &$noInsercao, No &$arvore, RegrasResponse $filhos, int $linhaDerivado, array $idsNo): void
    {
        $esq1 = new No($idsNo[0], $filhos->getEsquerda()[0], null, null, null, $noInsercao->getLinhaNo() + 1, null, $linhaDerivado, false, false);
        $contradEsq1 = EncontraContradicao::exec($arvore, $noInsercao->getFilhoEsquerdaNo());

        $esq2 = new No($idsNo[2], $filhos->getEsquerda()[1], null, null, null, $noInsercao->getLinhaNo() + 2, null, $linhaDerivado, false, false);
        $contradEsq2 = EncontraContradicao::exec($arvore, $noInsercao->getFilhoEsquerdaNo()->getFilhoCentroNo());

        if (!is_null($contradEsq2)) {
            $esq2->fecharRamo($contradEsq2->getLinhaNo());
        } elseif (!is_null($contradEsq1)) {
            $esq1->fecharRamo($contradEsq1->getLinhaNo());
            $esq1->setLinhaNo($esq1->getLinhaNo() + 1);
            $esq2->setLinhaNo($esq2->getLinhaNo() - 1);
            $aux = $esq2;
            $esq2 = $esq1;
            $esq1 = $aux;
        }
        $noInsercao->setFilhoEsquerdaNo($esq1);
        $noInsercao->getFilhoEsquerdaNo()->setFilhoCentroNo($esq2);

        $dir1 = new No($idsNo[1], $filhos->getDireita()[0], null, null, null, $noInsercao->getLinhaNo() + 1, null, $linhaDerivado, false, false);
        $contradDir1 = EncontraContradicao::exec($arvore, $noInsercao->getFilhoDireitaNo());

        $dir2 = new No($idsNo[3], $filhos->getDireita()[1], null, null, null, $noInsercao->getLinhaNo() + 2, null, $linhaDerivado, false, false);
        $contradDir2 = EncontraContradicao::exec($arvore, $noInsercao->getFilhoDireitaNo()->getFilhoCentroNo());

        if (!is_null($contradDir2)) {
            $dir2->fecharRamo($contradDir2->getLinhaNo());
        } elseif (!is_null($contradDir1)) {
            $dir1->fecharRamo($contradDir1->getLinhaNo());
            $dir1->setLinhaNo($dir1->getLinhaNo() + 1);
            $dir2->setLinhaNo($dir2->getLinhaNo() - 1);
            $aux = $dir2;
            $dir2 = $dir1;
            $dir1 = $aux;
        }
        $noInsercao->setFilhoDireitaNo($dir1);
        $noInsercao->getFilhoDireitaNo()->setFilhoCentroNo($dir2);
    }
}
