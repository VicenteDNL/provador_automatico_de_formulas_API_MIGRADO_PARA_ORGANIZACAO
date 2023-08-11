<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PredicadoTipoEnum;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\TentativaDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\TentativaInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores\EncontraNoPeloId;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores\EncontraNosFolhas;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores\EncontraProximoNoParaInsercao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\GeradorArvore;

class GeradorPorPasso extends GeradorArvore
{
    /**
     * Reconstroi a arvore atraves da listas de passos já
     * executados e tenta inserir o novo passo.
     * @param Formula              $formula
     * @param PassoInicializacao[] $lista
     * @param PassoInicializacao   $novoPasso
     * @param array
     * @param  array                  $passosExecutados
     * @return TentativaInicializacao $passosExecutados
     */
    public function reconstruirInicializacao(Formula $formula, array $passosExecutados, ?PassoInicializacao $novoPasso = null): TentativaInicializacao
    {
        $ultimoNo = null;
        $resposta = null;

        foreach ($passosExecutados as $passo) {
            $resposta = $this->inserirNoIncializacao($formula, $passo->getIdNo(), $passo->getNegacao(), $ultimoNo);

            if ($resposta['sucesso'] == false) {
                return new TentativaInicializacao([
                    'sucesso'  => false,
                    'mensagem' => $resposta['mensagem'],
                ]);
            }
            $ultimoNo = $resposta['ultimoNo'];
        }

        if (is_null($novoPasso)) {
            return new TentativaInicializacao([
                'sucesso'  => true,
                'mensagem' => 'sucesso',
                'arvore'   => $this->arvore,
                'passos'   => $passosExecutados,
            ]);
        }

        $result = $this->inserirNoIncializacao($formula, $novoPasso->getIdNo(), $novoPasso->getNegacao(), $ultimoNo);
        array_push($passos, $novoPasso);

        if ($result['sucesso'] == false) {
            return  new TentativaInicializacao([
                'sucesso'  => false,
                'mensagem' => $result['mensagem'],
            ]);
        }

        return new TentativaInicializacao([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
            'arvore'   => $this->arvore,
            'passos'   => $passos,
        ]);
    }

    /**
     * Esta função tem a finalidade resconstruir os passo ja execudados e
     * validar e derivar a tentativa do usuario.
     * @param PassoDerivacao[] $passosExecutados
     * @param PassoDerivacao   $passo
     * @param ?PassoDerivacao  $passoNovo
     */
    public function reconstruirArvore(array $passosExecutados, ?PassoDerivacao $passoNovo = null)
    {
        foreach ($passosExecutados as $exec) {
            $insercoesExec = array_map(fn ($id) => EncontraNoPeloId::exec($this->arvore, $id), $exec->getIdNoInsercoes());
            $derivacaoExec = EncontraNoPeloId::exec($this->arvore, $exec->getIdNoDerivacao());

            $qntdNegadoExec = $derivacaoExec->getValorNo()->getNegadoPredicado();
            $regraValidaExec = $derivacaoExec->getValorNo()->getTipoPredicado()->regra($qntdNegadoExec);

            $this->derivarByRegra($derivacaoExec, $insercoesExec, $regraValidaExec);
        }

        if (is_null($passoNovo)) {
            return new TentativaDerivacao([
                'sucesso'  => true,
                'mensagem' => 'sucesso',
                'arvore'   => $this->arvore,
                'passos'   => $passosExecutados,
            ]);
        }

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

        $nosFolha = EncontraNosFolhas::exec($noDerivacao);

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

        if ($regraValida == $passoNovo->getRegra()) {
            return ['sucesso' => false, 'mensagem' => 'Regra inválida'];
        }
        parent::derivarByRegra($noDerivacao, $listaNoInsercao, $regraValida);
        return new TentativaDerivacao([
            'sucesso'  => true,
            'mensagem' => 'Derivado com sucesso',
            'arvore'   => $this->arvore,
            'passos'   => $passosExecutados,
        ]);
    }

    /**
     * @param  Formula                      $formula
     * @param  int                          $idInsersao
     * @param  bool                         $negacao
     * @param  ?No                          $ultimoNo
     * @return Array<string,bool|No|string>
     */
    private function inserirNoIncializacao(Formula $formula, int $idInsersao, bool $negacao, $ultimoNo = null): array
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
}
