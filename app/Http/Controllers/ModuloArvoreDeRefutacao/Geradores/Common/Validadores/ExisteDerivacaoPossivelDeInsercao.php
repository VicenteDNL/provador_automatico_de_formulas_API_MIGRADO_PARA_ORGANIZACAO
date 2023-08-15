<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Validadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PredicadoTipoEnum;

class ExisteDerivacaoPossivelDeInsercao
{
    /**
     * Realiza uma busca na arvore por um Nó que ainda não foi derivado, após o valor encontrado
     * é verificado se o Nó poder ser inserido na árvore.
     * (a busca e feita na seguinte ordem = centro -> esquerda -> direita)
     * @param  No   $arvore
     * @param  bool $descendenteSemDerivacao -> Utilizado para busca recursiva
     * @return bool
     */
    public static function exec(No &$arvore, bool $descendenteSemDerivacao = false): bool
    {
        $proximoNo = false;
        $ramoCentro = $arvore->getFilhoCentroNo();
        $ramoEsquerdo = $arvore->getFilhoEsquerdaNo();
        $ramoDireito = $arvore->getFilhoDireitaNo();

        if ($arvore->isUtilizado() == false and (in_array($arvore->getValorNo()->getTipoPredicado(), [PredicadoTipoEnum::DISJUNCAO, PredicadoTipoEnum::CONDICIONAL, PredicadoTipoEnum::BICONDICIONAL, PredicadoTipoEnum::CONJUNCAO]) or ($arvore->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::PREDICATIVO and $arvore->getValorNo()->getNegadoPredicado() >= 2))) {
            $descendenteSemDerivacao = true;
        }

        if (is_null($ramoDireito) and is_null($ramoEsquerdo) and is_null($ramoCentro) and $arvore->isFechado() == false and $descendenteSemDerivacao) {
            return true;
        } else {
            if (!is_null($ramoCentro) and !$proximoNo) {
                $proximoNo = self::exec($ramoCentro, $descendenteSemDerivacao);
            }

            if (!is_null($ramoEsquerdo) and !$proximoNo) {
                $proximoNo = self::exec($ramoEsquerdo, $descendenteSemDerivacao);
            }

            if (!is_null($ramoDireito) and !$proximoNo) {
                $proximoNo = self::exec($ramoDireito, $descendenteSemDerivacao);
            }
            return $proximoNo;
        }
    }
}
