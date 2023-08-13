<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraDuplaNegacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoBifurca;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoSemBifucacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNosFolha;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraProximoNoParaInsercao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\GeradorArvore;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Validadores\IsDecendente;

class GeradorAutomatico extends GeradorArvore
{
    /**
     * Esta função gera e retorna as primeiras linhas da arvores de refutacao
     * @param  Formula $formula
     * @return No|null
     */
    public function inicializar(Formula $formula): ?No
    {
        $ultimoNo = null;
        $premissas = $formula->getPremissas();
        $conclusao = $formula->getConclusao();

        if (!empty($premissas)) {
            $premissa = array_pop($premissas);

            $this->arvore = new No($this->genereteIdNo(), $premissa->getValorObjPremissa(), null, null, null, 1, null, null, false, false);
            $ultimoNo = $this->arvore;

            foreach ($premissas as $premissa) {
                $ultimoNo->setFilhoCentroNo(new No($this->genereteIdNo(), $premissa->getValorObjPremissa(), null, null, null, $ultimoNo->getLinhaNo() + 1, null, null, false, false));
                $ultimoNo = $ultimoNo->getFilhoCentroNo();
            }
        }

        $conclusao->getValorObjConclusao()->addNegacaoPredicado();

        if ($this->arvore == null) {
            $this->arvore = (new No($this->genereteIdNo(), $conclusao->getValorObjConclusao(), null, null, null, 1, null, null, false, false));
            $ultimoNo = $this->arvore;
        } else {
            $ultimoNo->setFilhoCentroNo(new No($this->genereteIdNo(), $conclusao->getValorObjConclusao(), null, null, null, $ultimoNo->getLinhaNo() + 1, null, null, false, false));
            $ultimoNo = $ultimoNo->getFilhoCentroNo();
        }

        return $this->arvore;
    }

    /**
     * Cria a arvore otimizada
     * @return ?No
     */
    public function arvoreOtimizada(): ?No
    {
        $noInsercao = EncontraProximoNoParaInsercao::exec($this->arvore);

        if ($noInsercao == null) {
            return $this->arvore;
        } else {
            $no = EncontraDuplaNegacao::exec($this->arvore, $noInsercao);
            $noBifur = EncontraNoBifurca::exec($this->arvore, $noInsercao);
            $noSemBifur = EncontraNoSemBifucacao::exec($this->arvore, $noInsercao);

            if (!is_null($no)) {
                $qntdNegado = $no->getValorNo()->getNegadoPredicado();
                $regra = $no->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao' => $no->getIdNo(),
                    'idNoInsercoes' => [$noInsercao->getIdNo()],
                    'regra'         => $regra,
                ]);
                $this->derivar($passo);
                return $this->arvoreOtimizada();
            } elseif (!is_null($noSemBifur)) {
                $qntdNegado = $noSemBifur->getValorNo()->getNegadoPredicado();
                $regra = $noSemBifur->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao' => $noSemBifur->getIdNo(),
                    'idNoInsercoes' => [$noInsercao->getIdNo()],
                    'regra'         => $regra,
                ]);
                $this->derivar($passo);
                return $this->arvoreOtimizada($this->arvore);
            } elseif (!is_null($noBifur)) {
                $qntdNegado = $noBifur->getValorNo()->getNegadoPredicado();
                $regra = $noBifur->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao' => $noBifur->getIdNo(),
                    'idNoInsercoes' => [$noInsercao->getIdNo()],
                    'regra'         => $regra,
                ]);
                $this->derivar($passo);
                return $this->arvoreOtimizada($this->arvore);
            }
            return $this->arvore;
        }
    }

    /**
     * Cria a pior arvore possivel
     * @param  No $arvore
     * @return No
     */
    public function piorArvore(): No
    {
        $listaNosFolha = EncontraNosFolha::exec($this->arvore);

        if ($listaNosFolha == null) {
            return $this->arvore;
        } else {
            $no = EncontraDuplaNegacao::exec($this->arvore, $listaNosFolha[0]);
            $noBifur = EncontraNoBifurca::exec($this->arvore, $listaNosFolha[0]);
            $noSemBifur = EncontraNoSemBifucacao::exec($this->arvore, $listaNosFolha[0]);

            if (!is_null($noBifur)) {
                for ($i = 0 ; $i < count($listaNosFolha) ; ++$i) {
                    if (!IsDecendente::exec($noBifur, $listaNosFolha[$i])) {
                        unset($listaNosFolha[$i]);
                    }
                }

                $qntdNegado = $noBifur->getValorNo()->getNegadoPredicado();
                $regra = $noBifur->getValorNo()->getTipoPredicado()->regra($qntdNegado);

                $passo = new PassoDerivacao([
                    'idNoDerivacao' => $noBifur->getIdNo(),
                    'idNoInsercoes' => array_map(fn (No $nof) => $nof->getIdNo(), $listaNosFolha),
                    'regra'         => $regra,
                ]);

                $this->derivar($passo);
                return $this->piorArvore();
            } elseif ($noSemBifur) {
                for ($i = 0 ; $i < count($listaNosFolha) ; ++$i) {
                    if (!IsDecendente::exec($noSemBifur, $listaNosFolha[$i])) {
                        unset($listaNosFolha[$i]);
                    }
                }

                $qntdNegado = $noSemBifur->getValorNo()->getNegadoPredicado();
                $regra = $noSemBifur->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao' => $noSemBifur->getIdNo(),
                    'idNoInsercoes' => array_map(fn (No $nof) => $nof->getIdNo(), $listaNosFolha),
                    'regra'         => $regra,
                ]);

                $this->derivar($passo);
                return $this->piorArvore();
            } elseif ($no) {
                for ($i = 0 ; $i < count($listaNosFolha) ; ++$i) {
                    if (!IsDecendente::exec($no, $listaNosFolha[$i])) {
                        unset($listaNosFolha[$i]);
                    }
                }

                $qntdNegado = $no->getValorNo()->getNegadoPredicado();
                $regra = $no->getValorNo()->getTipoPredicado()->regra($qntdNegado);
                $passo = new PassoDerivacao([
                    'idNoDerivacao' => $no->getIdNo(),
                    'idNoInsercoes' => array_map(fn (No $nof) => $nof->getIdNo(), $listaNosFolha),
                    'regra'         => $regra,
                ]);
                $this->derivar($passo);
                return $this->piorArvore();
            }
            return $this->arvore;
        }
    }
}
