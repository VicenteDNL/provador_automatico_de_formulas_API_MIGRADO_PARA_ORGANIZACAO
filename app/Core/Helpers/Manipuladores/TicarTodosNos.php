<?php

namespace App\Core\Helpers\Manipuladores;

use App\Core\Common\Models\Attempts\TentativaTicagem;
use App\Core\Common\Models\Enums\PredicadoTipoEnum;
use App\Core\Common\Models\Tree\No;
use App\Core\Helpers\Buscadores\EncontraNoPeloId;

class TicarTodosNos
{
    /**
     *
     * @param No             $arvore
     * @param PassoTicagem[] $passos
     */
    public static function exec(No &$arvore, array $passos): TentativaTicagem
    {
        foreach ($passos as $no) {
            $noTicado = EncontraNoPeloId::exec($arvore, $no->getIdNo());

            if ($noTicado->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::PREDICATIVO and $noTicado->getValorNo()->getNegadoPredicado() < 2) {
                return new TentativaTicagem([
                    'sucesso'  => false,
                    'messagem' => "Não existe derivação para o argumento'" . $noTicado->getStringNo() . "' da linha'" . $noTicado->getLinhaNo() . "'",
                ]);
            } else {
                if ($noTicado->isUtilizado() == true) {
                    if ($noTicado->isTicado() == true) {
                        return new TentativaTicagem([
                            'sucesso'  => false,
                            'messagem' => "O nó '" . $noTicado->getStringNo() . "' da linha '" . $noTicado->getLinhaNo() . "' já foi ticado",
                        ]);
                    } else {
                        $noTicado->ticarNo();
                    }
                } else {
                    return new TentativaTicagem([
                        'sucesso'  => false,
                        'messagem' => "O nó '" . $noTicado->getStringNo() . "' da linha '" . $noTicado->getLinhaNo() . ' ainda não foi deriavado',
                    ]);
                }
            }
        }
        return new TentativaTicagem([
            'sucesso'  => true,
            'messagem' => 'Ticados com sucesso',
            'arvore'   => $arvore,
            'passos'   => $passos,
        ]);
    }
}
