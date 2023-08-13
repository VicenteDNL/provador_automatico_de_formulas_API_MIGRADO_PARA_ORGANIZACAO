<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PredicadoTipoEnum;

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
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if ($arvore->isUtilizado() == false and (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::DISJUNCAO, PredicadoTipoEnum::CONDICIONAL, PredicadoTipoEnum::BICONDICIONAL, PredicadoTipoEnum::CONJUNCAO]) or ($arvore->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::PREDICATIVO and $arvore->getValorNo()->getNegadoPredicado() >= 2))) {
            $descendenteSemDerivacao = true;
        }

        if (is_null($ramoDireito) and is_null($ramoEsquerdo) and is_null($ramoCentro) and $arvore->isFechado() == false and $descendenteSemDerivacao) {
            return $arvore;
        } else {
            if (!is_null($ramoCentro) and is_null($proximoNo)) {
                $proximoNo = self::exec($ramoCentro, $descendenteSemDerivacao);
            }

            if (!is_null($ramoEsquerdo) and is_null($proximoNo)) {
                $proximoNo = self::exec($ramoEsquerdo, $descendenteSemDerivacao);
            }

            if (!is_null($ramoDireito) and is_null($proximoNo)) {
                $proximoNo = self::exec($ramoDireito, $descendenteSemDerivacao);
            }
            return $proximoNo;
        }
    }
}
