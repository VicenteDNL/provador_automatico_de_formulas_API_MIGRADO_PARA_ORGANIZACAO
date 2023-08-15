<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\TentativaDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\TentativaInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraDuplaNegacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoBifurca;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoSemBifucacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNosFolha;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\GeradorArvore;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Validadores\ExisteDerivacaoPossivelDeInsercao;

class GeradorAutomatico extends GeradorArvore
{
    /**
     * Esta função gera e retorna as primeiras linhas da arvores de refutacao
     * @param  Formula                $formula
     * @return TentativaInicializacao
     */
    public function inicializar(Formula $formula): TentativaInicializacao
    {
        $premissas = $formula->getPremissas();
        $conclusao = $formula->getConclusao();

        foreach ($premissas as $premissa) {
            $tentativa = $this->inserirNoIncializacao($formula, $premissa->getId(), false);

            if ($tentativa->getSucesso() == false) {
                return new TentativaInicializacao([
                    'sucesso'  => false,
                    'mensagem' => $tentativa->getMensagem(),
                ]);
            }
        }

        $tentativa = $this->inserirNoIncializacao($formula, $conclusao->getId(), true);

        if ($tentativa->getSucesso() == false) {
            return new TentativaInicializacao([
                'sucesso'  => false,
                'mensagem' => $tentativa->getMensagem(),
            ]);
        }

        return new TentativaInicializacao([
            'sucesso'  => true,
            'mensagem' => 'inicializacao realizada com sucesso',
            'arvore'   => $this->arvore,
            'passos'   => [],
        ]);
    }

    /**
     * Cria a arvore otimizada
     * @return ?TentativaDerivacao
     */
    public function arvoreOtimizada(): ?TentativaDerivacao
    {
        if (is_null($this->arvore)) {
            return new TentativaDerivacao([
                'sucesso'  => false,
                'mensagem' => 'Arvore não foi inicializada',
            ]);
        }

        $existe = ExisteDerivacaoPossivelDeInsercao::exec($this->arvore);

        if ($existe) {
            $no = EncontraDuplaNegacao::exec($this->arvore);
            $noBifur = EncontraNoBifurca::exec($this->arvore);
            $noSemBifur = EncontraNoSemBifucacao::exec($this->arvore);

            if (!is_null($no)) {
                $insercoes = array_map(fn (No $n) => $n->getIdNo(), EncontraNosFolha::exec($no));
                $qntdNegado = $no->getValorNo()->getNegadoPredicado();
                $regra = $no->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao'  => $no->getIdNo(),
                    'idsNoInsercoes' => $insercoes,
                    'regra'          => $regra,
                ]);
            } elseif (!is_null($noSemBifur)) {
                $insercoes = array_map(fn (No $n) => $n->getIdNo(), EncontraNosFolha::exec($noSemBifur));
                $qntdNegado = $noSemBifur->getValorNo()->getNegadoPredicado();
                $regra = $noSemBifur->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao'  => $noSemBifur->getIdNo(),
                    'idsNoInsercoes' => $insercoes,
                    'regra'          => $regra,
                ]);
            } elseif (!is_null($noBifur)) {
                $insercoes = array_map(fn (No $n) => $n->getIdNo(), EncontraNosFolha::exec($noBifur));
                $qntdNegado = $noBifur->getValorNo()->getNegadoPredicado();
                $regra = $noBifur->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao'  => $noBifur->getIdNo(),
                    'idsNoInsercoes' => $insercoes,
                    'regra'          => $regra,
                ]);
            }

            if (isset($passo)) {
                $tentativa = $this->derivar($passo);

                if (!$tentativa->getSucesso()) {
                    return  $tentativa;
                }

                return $this->arvoreOtimizada();
            }
        }
        return  new TentativaDerivacao([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
            'arvore'   => $this->arvore,
            'passos'   => [],
        ]);
    }

    /**
     * Cria a pior arvore possivel
     * @param  No                 $arvore
     * @return TentativaDerivacao
     */
    public function piorArvore(): TentativaDerivacao
    {
        $existe = ExisteDerivacaoPossivelDeInsercao::exec($this->arvore);

        if ($existe) {
            $no = EncontraDuplaNegacao::exec($this->arvore);
            $noBifur = EncontraNoBifurca::exec($this->arvore);
            $noSemBifur = EncontraNoSemBifucacao::exec($this->arvore);

            if (!is_null($noBifur)) {
                $insercoes = array_map(fn (No $n) => $n->getIdNo(), EncontraNosFolha::exec($noBifur));
                $qntdNegado = $noBifur->getValorNo()->getNegadoPredicado();
                $regra = $noBifur->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao'  => $noBifur->getIdNo(),
                    'idsNoInsercoes' => $insercoes,
                    'regra'          => $regra,
                ]);
            } elseif (!is_null($noSemBifur)) {
                $insercoes = array_map(fn (No $n) => $n->getIdNo(), EncontraNosFolha::exec($noSemBifur));
                $qntdNegado = $noSemBifur->getValorNo()->getNegadoPredicado();
                $regra = $noSemBifur->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao'  => $noSemBifur->getIdNo(),
                    'idsNoInsercoes' => $insercoes,
                    'regra'          => $regra,
                ]);
            } elseif (!is_null($no)) {
                $insercoes = array_map(fn (No $n) => $n->getIdNo(), EncontraNosFolha::exec($no));
                $qntdNegado = $no->getValorNo()->getNegadoPredicado();
                $regra = $no->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao'  => $no->getIdNo(),
                    'idsNoInsercoes' => $insercoes,
                    'regra'          => $regra,
                ]);
            }

            if (isset($passo)) {
                $tentativa = $this->derivar($passo);

                if (!$tentativa->getSucesso()) {
                    return  $tentativa;
                }
                return $this->arvoreOtimizada();
            }
        }
        return new TentativaDerivacao([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
            'arvore'   => $this->arvore,
            'passos'   => [],
        ]);
    }
}
