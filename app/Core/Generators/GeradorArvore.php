<?php

namespace App\Core\Generators;

use App\Core\Common\Models\Attempts\TentativaDerivacao;
use App\Core\Common\Models\Attempts\TentativaInicializacao;
use App\Core\Common\Models\Enums\PredicadoTipoEnum;
use App\Core\Common\Models\Enums\RegrasEnum;
use App\Core\Common\Models\Formula\Formula;
use App\Core\Common\Models\Steps\PassoDerivacao;
use App\Core\Common\Models\Tree\No;
use App\Core\Helpers\Buscadores\EncontraNoPeloId;
use App\Core\Helpers\Buscadores\EncontraNosFolha;
use App\Core\Helpers\Criadores\CriarNoBifurcado;
use App\Core\Helpers\Criadores\CriarNoBifurcadoDuplo;
use App\Core\Helpers\Criadores\CriarNoCentro;
use App\Core\Helpers\Criadores\CriarNoCentroDuplo;
use App\Core\Helpers\Validadores\ExisteDerivacaoPossivelDeInsercao;

class GeradorArvore
{
    protected ?No $arvore;
    protected AplicadorRegras $regras;
    private int $idNo;

    public function __construct()
    {
        $this->arvore = null;
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

        $listaNoInsercao = array_map(fn ($id) => EncontraNoPeloId::exec($this->arvore, $id), $passo->getIdsNoInsercoes());
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
     * @param  Formula                $formula
     * @param  string                 $idInsersao
     * @param  bool                   $negacao
     * @param  ?No                    $ultimoNo
     * @return TentativaInicializacao
     */
    protected function inserirNoIncializacao(Formula $formula, string $idInsersao, bool $negacao): TentativaInicializacao
    {
        $premissa = array_reduce(
            $formula->getPremissas(),
            function ($carry, $item) use ($idInsersao) {
                if ($item->getId() == $idInsersao) {
                    return clone $item->getValorObjPremissa();
                }
                return $carry;
                ;
            },
            null
        );

        $conclusao = $formula->getConclusao()->getId() == $idInsersao
        ? clone $formula->getConclusao()->getValorObjConclusao()
        : null;

        if (!is_null($premissa)) {
            if ($negacao) {
                return new TentativaInicializacao([
                    'sucesso'  => false,
                    'mensagem' => 'Atenção, isso é uma premissa',
                ]);
            }
        } elseif (!is_null($conclusao)) {
            if (!$negacao) {
                return new TentativaInicializacao([
                    'sucesso'  => false,
                    'mensagem' => 'Atenção, isso é uma conclusao',
                ]);
            }
            $conclusao->addNegacaoPredicado();
        } else {
            return new TentativaInicializacao([
                'sucesso'  => false,
                'mensagem' => 'id não encontrado',
            ]);
        }

        $predicado = $premissa ?? $conclusao;

        if (is_null($this->arvore)) {
            $this->arvore = new No($this->genereteIdNo(), $predicado, null, null, null, 1, null, null, false, false);
        } else {
            $ultimoNo = EncontraNosFolha::exec($this->arvore);

            if (count($ultimoNo) > 1) {
                return new TentativaInicializacao([
                    'sucesso'  => false,
                    'mensagem' => 'Foi encontrado bifurcações na árvore ',
                ]);
            }

            $ultimoNo[0]->setFilhoCentroNo(new No($this->genereteIdNo(), $predicado, null, null, null, $ultimoNo[0]->getLinhaNo() + 1, null, null, false, false));
        }

        return new TentativaInicializacao([
            'sucesso'  => true,
            'mensagem' => 'Adicionado com sucesso',
            'arvore'   => $this->arvore,
            'passos'   => [],
        ]);
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
        $listaNoInsercao = array_map(fn ($id): No => EncontraNoPeloId::exec($this->arvore, $id), $passoNovo->getIdsNoInsercoes());

        if (is_null(ExisteDerivacaoPossivelDeInsercao::exec($this->arvore))) {
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
