<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Manipuladores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PredicadoTipoEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\TentativaTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoPeloId;

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
