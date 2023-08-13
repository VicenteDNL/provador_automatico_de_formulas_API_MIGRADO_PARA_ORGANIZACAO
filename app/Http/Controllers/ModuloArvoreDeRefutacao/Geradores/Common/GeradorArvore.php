<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PredicadoTipoEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\RegrasEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\TentativaDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoPeloId;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNosFolha;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraProximoNoParaInsercao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Criadores\CriarNoBifurcado;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Criadores\CriarNoBifurcadoDuplo;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Criadores\CriarNoCentro;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Criadores\CriarNoCentroDuplo;

class GeradorArvore
{
    protected ?No $arvore;
    protected AplicadorRegras $regras;
    private int $idNo;

    public function __construct()
    {
        $this->regras = new AplicadorRegras();
        $this->idNo = 0;
    }

    /**
     * @return No|null
     */
    public function getArvore(): ?No
    {
        return $this->arvore;
    }

    /**
     * @param No             $noDerivacao
     * @param No[]           $listaNoInsercao
     * @param RegrasEnum     $regra
     * @param PassoDerivacao $novoPasso
     * @param PassoDerivacao $passo
     */
    public function derivar(PassoDerivacao $passo): TentativaDerivacao
    {
        $tentativa = $this->validarDerivacao($passo);

        if (!$tentativa->getSucesso()) {
            return  $tentativa;
        }

        $listaNoInsercao = array_map(fn ($id) => EncontraNoPeloId::exec($this->arvore, $id), $passo->getIdNoInsercoes());
        $noDerivacao = EncontraNoPeloId::exec($this->arvore, $passo->getIdNoDerivacao());

        switch($passo->getRegra()) {
            case RegrasEnum::DUPLANEGACAO:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->duplaNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoCentro::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), $this->genereteIdNo());
                }
                break;
            case RegrasEnum::CONJUNCAO:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->conjuncao($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoCentroDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::DISJUNCAONEGADA:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->disjuncaoNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoCentroDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::CONDICIONALNEGADA:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->condicionalNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoCentroDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::DISJUNCAO:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->disjuncao($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcado::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::CONDICIONAL:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->condicional($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcado::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::BICONDICIONAL:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->bicondicional($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcadoDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo(), $this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::CONJUNCAONEGADA:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->conjuncaoNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcado::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
            case RegrasEnum::BICONDICIONALNEGADA:
                foreach ($listaNoInsercao as $nosFolha) {
                    $array_filhos = $this->regras->bicondicionalNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    CriarNoBifurcadoDuplo::exec($nosFolha, $this->arvore, $array_filhos, $noDerivacao->getLinhaNo(), [$this->genereteIdNo(), $this->genereteIdNo(), $this->genereteIdNo(), $this->genereteIdNo()]);
                }
                break;
        }
        return new TentativaDerivacao([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
            'arvore'   => $this->arvore,
            'passos'   => [$passo],
        ]);
    }

    /**
     * @param  Formula                      $formula
     * @param  int                          $idInsersao
     * @param  bool                         $negacao
     * @param  ?No                          $ultimoNo
     * @return Array<string,bool|No|string>
     */
    protected function inserirNoIncializacao(Formula $formula, int $idInsersao, bool $negacao, $ultimoNo = null): array
    {
        $identificador = str_split($idInsersao, strrpos($idInsersao, '_'));

        if ($identificador[0] == 'premissa' && $negacao == false) {
            $premissa = $formula->getPremissas();
            $premissa = $premissa[substr($identificador[1], 1)];

            if ($this->arvore == null) {
                $this->arvore = new No($this->genereteIdNo(), $premissa->getValorObjPremissa(), null, null, null, 1, null, null, false, false);
                $ultimoNo = $this->arvore;
            } else {
                $ultimoNo->setFilhoCentroNo(new No($this->genereteIdNo(), $premissa->getValorObjPremissa(), null, null, null, $ultimoNo->getLinhaNo() + 1, null, null, false, false));
                $ultimoNo = $ultimoNo->getFilhoCentroNo();
            }

            return ['sucesso' => true, 'ultimoNo' => $ultimoNo];
        } elseif ($identificador[0] == 'conclusao' && $negacao == true) {
            $conclusao = $formula->getConclusao();

            if ($this->arvore == null) {
                $this->arvore = (new No($this->genereteIdNo(), $conclusao->getValorObjConclusao(), null, null, null, 1, null, null, false, false));
                $ultimoNo = $this->arvore;
            } else {
                $ultimoNo->setFilhoCentroNo(new No($this->genereteIdNo(), $conclusao->getValorObjConclusao(), null, null, null, $ultimoNo->getLinhaNo() + 1, null, null, false, false));
                $ultimoNo = $ultimoNo->getFilhoCentroNo();
            }
            return ['sucesso' => true, 'ultimoNo' => $ultimoNo];
        } else {
            if ($identificador[0] == 'premissa' && $negacao == true) {
                return ['sucesso' => false, 'mensagem' => 'Atenção!! Esto é uma premissa!', 'ultimoNo' => ''];
            }

            if ($identificador[0] == 'conclusao' && $negacao == false) {
                return ['sucesso' => false, 'mensagem' => 'Atenção!! Esto é uma conclusão!', 'ultimoNo' => ''];
            }

            return ['sucesso' => false, 'mensagem' => 'Atenção!!', 'ultimoNo' => ''];
        }
    }

    /**
     * Gera um novo id para o No
     * @return int
     */
    protected function genereteIdNo(): int
    {
        $this->idNo += 1;
        return $this->idNo;
    }

    private function validarDerivacao(PassoDerivacao $passoNovo): TentativaDerivacao
    {
        $noDerivacao = EncontraNoPeloId::exec($this->arvore, $passoNovo->getIdNoDerivacao());
        $predicado = $noDerivacao->getValorNo();
        $qntdNegado = $predicado->getNegadoPredicado();
        $listaNoInsercao = array_map(fn ($id): No => EncontraNoPeloId::exec($this->arvore, $id), $passoNovo->getIdNoInsercoes());

        if (is_null(EncontraProximoNoParaInsercao::exec($this->arvore))) {
            return new TentativaDerivacao(['sucesso' => false, 'mensagem' => 'não existe mais derivações possiveis']);
        }

        if ($noDerivacao->isUtilizado() == true) {
            return new TentativaDerivacao(['sucesso' => false, 'mensagem' => 'Este argumento já foi derivado']);
        }

        $nosFolha = EncontraNosFolha::exec($noDerivacao);

        if (count($nosFolha) == count($listaNoInsercao)) {
            foreach ($listaNoInsercao as $noInsercao) {
                if (in_array($noInsercao, $nosFolha) == false) {
                    return $noInsercao->isFechado() == true
                    ? new TentativaDerivacao([
                        'sucesso'  => false,
                        'mensagem' => "O nó '" . $noInsercao->getStringNo() . "' da linha'" . $noInsercao->getLinhaNo() . "' já foi fechado",
                    ])
                    : new TentativaDerivacao([
                        'sucesso'  => false,
                        'mensagem' => "O nó '" . $noInsercao->getStringNo() . "' da linha'" . $noInsercao->getLinhaNo() . "' não é nó folha",
                    ]);
                }
            }
        } else {
            return new TentativaDerivacao([
                'sucesso' => false,
                'mensagem'
                => count($nosFolha) > count($listaNoInsercao)
                ? 'Existe mais nós válidos para inseção'
                : 'Algum dos nós de inserção não são válidos',
            ]);
        }

        if ($predicado->getTipoPredicado() == PredicadoTipoEnum::PREDICATIVO and $qntdNegado < 2) {
            return new TentativaDerivacao([
                'sucesso'  => false,
                'mensagem' => 'Não existe derivação para este argumento',
            ]);
        }

        $regraValida = $predicado->getTipoPredicado()->regra($qntdNegado);

        if ($regraValida != $passoNovo->getRegra()) {
            return new TentativaDerivacao([
                'sucesso'  => false,
                'mensagem' => 'Regra inválida',
            ]);
        }

        return new TentativaDerivacao([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
        ]);
    }
}
