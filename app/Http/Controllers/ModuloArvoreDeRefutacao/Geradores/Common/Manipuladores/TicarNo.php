<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PredicadoTipoEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\TentativaTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores\EncontraNoPeloId;

class TicarNo
{
    /**
     * @param No           $arvore
     * @param PassoTicagem $passo
     */
    public static function exec(No &$arvore, PassoTicagem $passo): TentativaTicagem
    {
        $noTicado = EncontraNoPeloId::exec($arvore, $passo->getIdNo());

        if (($noTicado->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::PREDICATIVO) and $noTicado->getValorNo()->getNegadoPredicado() < 2) {
            return new TentativaTicagem([
                'sucesso'  => false,
                'messagem' => 'Este argumento não pode ser ticado, pois não existe derivação',
            ]);
        } else {
            if ($noTicado->isUtilizado() == true) {
                if ($noTicado->isTicado() == true) {
                    return new TentativaTicagem([
                        'sucesso'  => false,
                        'messagem' => 'Este nó já foi ticado',
                    ]);
                } else {
                    $noTicado->ticarNo();
                    return new TentativaTicagem([
                        'sucesso'  => true,
                        'messagem' => 'Ticado com sucesso',
                    ]);
                }
            } else {
                return new TentativaTicagem([
                    'sucesso'  => false,
                    'messagem' => 'Este nó ainda não foi deriavado',
                ]);
            }
        }
    }
}
