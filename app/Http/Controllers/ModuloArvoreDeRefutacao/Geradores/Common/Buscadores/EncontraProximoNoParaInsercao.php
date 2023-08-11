<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PredicadoTipoEnum;

class EncontraProximoNoParaInsercao
{
    /**
     * Realiza uma busca na arvore por um Nó que ainda não foi derivado, após o valor encontrado
     * é verificado se o Nó poder ser inserido na árvore. Em caso de sucesso retorna o primeiro Nó
     * folha encontrao  ou null em casos que nenhum nó da arvore satisfaça a regra
     * (a busca e feita na seguinte ordem = centro -> esquerda -> direita)
     * @param  No      $arvore
     * @param  bool    $descendenteSemDerivacao -> Utilizado para busca recursiva
     * @return No|null
     */
    public static function exec(No &$arvore, bool $descendenteSemDerivacao = false): ?No
    {
        $proximoNo = null;

        if ($arvore->isUtilizado() == false and (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::DISJUNCAO, PredicadoTipoEnum::CONDICIONAL, PredicadoTipoEnum::BICONDICIONAL, PredicadoTipoEnum::CONJUNCAO]) or ($arvore->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::PREDICATIVO and $arvore->getValorNo()->getNegadoPredicado() >= 2))) {
            $descendenteSemDerivacao = true;
        }

        if ($arvore->getFilhoDireitaNo() == null and $arvore->getFilhoEsquerdaNo() == null and $arvore->getFilhoCentroNo() == null and $arvore->isFechado() == false and $descendenteSemDerivacao == true) {
            return $arvore;
        } else {
            if ($arvore->getFilhoCentroNo() != null and $proximoNo == null) {
                $proximoNo = self::exec($arvore->getFilhoCentroNo(), $descendenteSemDerivacao);
            }

            if ($arvore->getFilhoEsquerdaNo() != null and $proximoNo == null) {
                $proximoNo = self::exec($arvore->getFilhoEsquerdaNo(), $descendenteSemDerivacao);
            }

            if ($arvore->getFilhoDireitaNo() != null and $proximoNo == null) {
                $proximoNo = self::exec($arvore->getFilhoDireitaNo(), $descendenteSemDerivacao);
            }
            return $proximoNo;
        }
    }
}
