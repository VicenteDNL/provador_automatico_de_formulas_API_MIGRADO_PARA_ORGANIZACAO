<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Arvore\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Formula\PredicadoTipoEnum;

class GeradorAutomatico extends Gerador
{
    public function __construct()
    {
    }

    /**
     * Esta função gera e retorna as primeiras linhas da arvores de refutacao, a partir das premissas e conclusão
     * @param  Premissa[]  $premissas
     * @param  Conclusao[] $conclusao
     * @return No|null
     */
    public function inicializar(array $premissas, array $conclusao): ?No
    {
        $ultimoNo = null;

        if (!empty($premissas)) {
            $premissa = array_pop($premissas);
            $this->addLinha();
            $this->arvore = new No($this->genereteIdNo(), $premissa->getValorObjPremissa(), null, null, null, $this->getUltimaLinha(), null, null, false, false);
            $ultimoNo = $this->arvore;

            foreach ($premissas as $premissa) {
                $this->addLinha();
                $ultimoNo->setFilhoCentroNo(new No($this->genereteIdNo(), $premissa->getValorObjPremissa(), null, null, null, $this->getUltimaLinha(), null, null, false, false));
                $ultimoNo = $ultimoNo->getFilhoCentroNo();
            }
        }

        if (!empty($conclusao)) {
            $conclusao[0]->getValorObjConclusao()->addNegacaoPredicado();

            if ($this->arvore == null) {
                $this->arvore = (new No($this->genereteIdNo(), $conclusao[0]->getValorObjConclusao(), null, null, null, 1, null, null, false, false));
                $ultimoNo = $this->arvore;
            } else {
                $ultimoNo->setFilhoCentroNo(new No($this->genereteIdNo(), $conclusao[0]->getValorObjConclusao(), null, null, null, $this->getUltimaLinha(), null, null, false, false));
                $ultimoNo = $ultimoNo->getFilhoCentroNo();
            }
            $this->addLinha();
        }
        return $this->arvore;
    }

    /**
     * Cria a arvore otimizada
     * @param  No $arvore
     * @return No
     */
    public function arvoreOtimizada(No $arvore): No
    {
        $noInsercao = $this->proximoNoParaInsercao($arvore);

        if ($noInsercao == null) {
            return $arvore;
        } else {
            $no = $this->encontraDuplaNegacao($arvore, $noInsercao);
            $noBifur = $this->encontraNoBifuca($arvore, $noInsercao);
            $noSemBifur = $this->encontraNoSemBifucacao($arvore, $noInsercao);

            if (!is_null($no)) {
                $array_filhos = $this->regras->duplaNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNo($noInsercao, $arvore, $array_filhos, $no->getLinhaNo());
                return $this->arvoreOtimizada($arvore);
            } elseif (!is_null($noSemBifur)) {
                if ($noSemBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONJUNCAO and $noSemBifur->getValorNo()->getNegadoPredicado() == 0) {
                    $array_filhos = $this->regras->conjuncao($noSemBifur->getValorNo());
                    $noSemBifur->utilizado(true);
                    $this->criarNoSemBifucacao($noInsercao, $arvore, $array_filhos, $noSemBifur->getLinhaNo());
                } elseif ($noSemBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::DISJUNCAO and $noSemBifur->getValorNo()->getNegadoPredicado() == 1) {
                    $array_filhos = $this->regras->disjuncaoNeg($noSemBifur->getValorNo());
                    $noSemBifur->utilizado(true);
                    $this->criarNoSemBifucacao($noInsercao, $arvore, $array_filhos, $noSemBifur->getLinhaNo());
                } elseif ($noSemBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONDICIONAL and $noSemBifur->getValorNo()->getNegadoPredicado() == 1) {
                    $array_filhos = $this->regras->condicionalNeg($noSemBifur->getValorNo());
                    $noSemBifur->utilizado(true);
                    $this->criarNoSemBifucacao($noInsercao, $arvore, $array_filhos, $noSemBifur->getLinhaNo());
                }
                return $this->arvoreOtimizada($arvore);
            } elseif (!is_null($noBifur)) {
                if ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::DISJUNCAO and $noBifur->getValorNo()->getNegadoPredicado() == 0) {
                    $array_filhos = $this->regras->disjuncao($noBifur->getValorNo());
                    $noBifur->utilizado(true);
                    $this->criarNoBifurcado($noInsercao, $arvore, $array_filhos, $noBifur->getLinhaNo());
                } elseif ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONDICIONAL and $noBifur->getValorNo()->getNegadoPredicado() == 0) {
                    $array_filhos = $this->regras->condicional($noBifur->getValorNo());
                    $noBifur->utilizado(true);
                    $this->criarNoBifurcado($noInsercao, $arvore, $array_filhos, $noBifur->getLinhaNo());
                } elseif ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::BICONDICIONAL and $noBifur->getValorNo()->getNegadoPredicado() == 0) {
                    $array_filhos = $this->regras->bicondicional($noBifur->getValorNo());
                    $noBifur->utilizado(true);
                    $this->criarNoBifurcadoDuplo($noInsercao, $arvore, $array_filhos, $noBifur->getLinhaNo());
                } elseif ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONJUNCAO and $noBifur->getValorNo()->getNegadoPredicado() == 1) {
                    $array_filhos = $this->regras->conjuncaoNeg($noBifur->getValorNo());
                    $noBifur->utilizado(true);
                    $this->criarNoBifurcado($noInsercao, $arvore, $array_filhos, $noBifur->getLinhaNo());
                } elseif ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::BICONDICIONAL and $noBifur->getValorNo()->getNegadoPredicado() == 1) {
                    $array_filhos = $this->regras->bicondicionalNeg($noBifur->getValorNo());
                    $noBifur->utilizado(true);
                    $this->criarNoBifurcadoDuplo($noInsercao, $arvore, $array_filhos, $noBifur->getLinhaNo());
                }
                return $this->arvoreOtimizada($arvore);
            }
            return $arvore;
        }
    }

    /**
     * Cria a pior arvore possivel
     * @param  No $arvore
     * @return No
     */
    public function piorArvore($arvore): No
    {
        $listaNosFolha = $this->getNosFolha($arvore);

        if ($listaNosFolha == null) {
            return $arvore;
        } else {
            $no = $this->encontraDuplaNegacao($arvore, $listaNosFolha[0]);
            $noBifur = $this->encontraNoBifuca($arvore, $listaNosFolha[0]);
            $noSemBifur = $this->encontraNoSemBifucacao($arvore, $listaNosFolha[0]);

            if ($noBifur) {
                for ($i = 0 ; $i < count($listaNosFolha) ; ++$i) {
                    if (!$this->isDecendente($noBifur, $listaNosFolha[$i])) {
                        unset($listaNosFolha[$i]);
                    }
                }

                if ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::DISJUNCAO and $noBifur->getValorNo()->getNegadoPredicado() == 0) {
                    foreach ($listaNosFolha as $nosFolha) {
                        $array_filhos = $this->regras->disjuncao($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcado($nosFolha, $arvore, $array_filhos, $noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                } elseif ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONDICIONAL and $noBifur->getValorNo()->getNegadoPredicado() == 0) {
                    foreach ($listaNosFolha as $nosFolha) {
                        $array_filhos = $this->regras->condicional($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcado($nosFolha, $arvore, $array_filhos, $noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                } elseif ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::BICONDICIONAL and $noBifur->getValorNo()->getNegadoPredicado() == 0) {
                    foreach ($listaNosFolha as $nosFolha) {
                        $array_filhos = $this->regras->bicondicional($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcadoDuplo($nosFolha, $arvore, $array_filhos, $noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                    $this->addLinha();
                } elseif ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONJUNCAO and $noBifur->getValorNo()->getNegadoPredicado() == 1) {
                    foreach ($listaNosFolha as $nosFolha) {
                        $array_filhos = $this->regras->conjuncaoNeg($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcado($nosFolha, $arvore, $array_filhos, $noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                } elseif ($noBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::BICONDICIONAL and $noBifur->getValorNo()->getNegadoPredicado() == 1) {
                    foreach ($listaNosFolha as $nosFolha) {
                        $array_filhos = $this->regras->bicondicionalNeg($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcadoDuplo($nosFolha, $arvore, $array_filhos, $noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                    $this->addLinha();
                }
                return $this->piorArvore($arvore);
            } elseif ($noSemBifur) {
                for ($i = 0 ; $i < count($listaNosFolha) ; ++$i) {
                    if (!$this->isDecendente($noSemBifur, $listaNosFolha[$i])) {
                        unset($listaNosFolha[$i]);
                    }
                }

                if ($noSemBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONJUNCAO and $noSemBifur->getValorNo()->getNegadoPredicado() == 0) {
                    foreach ($listaNosFolha as $nosFolha) {
                        $array_filhos = $this->regras->conjuncao($noSemBifur->getValorNo());
                        $noSemBifur->utilizado(true);
                        $this->criarNoSemBifucacao($nosFolha, $arvore, $array_filhos, $noSemBifur->getLinhaNo());
                    }
                } elseif ($noSemBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::DISJUNCAO and $noSemBifur->getValorNo()->getNegadoPredicado() == 1) {
                    foreach ($listaNosFolha as $nosFolha) {
                        $array_filhos = $this->regras->disjuncaoNeg($noSemBifur->getValorNo());
                        $noSemBifur->utilizado(true);
                        $this->criarNoSemBifucacao($nosFolha, $arvore, $array_filhos, $noSemBifur->getLinhaNo());
                    }
                } elseif ($noSemBifur->getValorNo()->getTipoPredicado() == PredicadoTipoEnum::CONDICIONAL and $noSemBifur->getValorNo()->getNegadoPredicado() == 1) {
                    foreach ($listaNosFolha as $nosFolha) {
                        $array_filhos = $this->regras->condicionalNeg($noSemBifur->getValorNo());
                        $noSemBifur->utilizado(true);
                        $this->criarNoSemBifucacao($nosFolha, $arvore, $array_filhos, $noSemBifur->getLinhaNo());
                    }
                }
                $this->addLinha();
                $this->addLinha();
                return $this->piorArvore($arvore);
            } elseif ($no) {
                for ($i = 0 ; $i < count($listaNosFolha) ; ++$i) {
                    if (!$this->isDecendente($no, $listaNosFolha[$i])) {
                        unset($listaNosFolha[$i]);
                    }
                }

                foreach ($listaNosFolha as $nosFolha) {
                    $array_filhos = $this->regras->duplaNeg($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNo($nosFolha, $arvore, $array_filhos, $no->getLinhaNo());
                }
                $this->addLinha();
                return $this->piorArvore($arvore);
            }
            return $arvore;
        }
    }
}
