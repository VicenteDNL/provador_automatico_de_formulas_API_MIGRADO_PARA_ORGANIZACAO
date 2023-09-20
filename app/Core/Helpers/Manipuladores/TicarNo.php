<?php

namespace App\Core\Helpers\Manipuladores;

use App\Core\Common\Models\Attempts\TentativaTicagem;
use App\Core\Common\Models\Enums\PredicadoTipoEnum;
use App\Core\Common\Models\Steps\PassoTicagem;
use App\Core\Common\Models\Tree\No;
use App\Core\Helpers\Buscadores\EncontraNoPeloId;

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
                'mensagem' => 'Este argumento não pode ser ticado, pois não existe derivação',
            ]);
        } else {
            if ($noTicado->isUtilizado() == true) {
                if ($noTicado->isTicado() == true) {
                    return new TentativaTicagem([
                        'sucesso'  => false,
                        'mensagem' => 'Este nó já foi ticado',
                    ]);
                } else {
                    $noTicado->ticarNo();
                    return new TentativaTicagem([
                        'sucesso'   => true,
                        'mensagem'  => 'Ticado com sucesso',
                        'passos'    => [$passo],
                        'arvore'    => $arvore,
                    ]);
                }
            } else {
                return new TentativaTicagem([
                    'sucesso'  => false,
                    'mensagem' => 'Este nó ainda não foi deriavado',
                ]);
            }
        }
    }
}
