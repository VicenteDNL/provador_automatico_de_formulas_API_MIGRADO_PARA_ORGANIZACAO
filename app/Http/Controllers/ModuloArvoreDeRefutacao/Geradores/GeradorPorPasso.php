<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\TentativaDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\TentativaFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\TentativaInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\TentativaTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\GeradorArvore;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores\FecharNo;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores\FecharTodosNos;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores\TicarNo;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Manipuladores\TicarTodosNos;

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
     * @return TentativaInicializacao
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
     * Esta função tem a finalidade resconstruir os passo ja executados e
     * validar e derivar a tentativa do usuario.
     * @param  PassoDerivacao[]   $passosExecutados
     * @param  PassoDerivacao     $passo
     * @param  ?PassoDerivacao    $passoNovo
     * @return TentativaDerivacao
     */
    public function reconstruirArvore(array $passosExecutados, ?PassoDerivacao $passoNovo = null): TentativaDerivacao
    {
        foreach ($passosExecutados as $exec) {
            $tentativa = $this->derivar($exec);

            if (!$tentativa->getSucesso()) {
                return  $tentativa;
            }
        }

        if (is_null($passoNovo)) {
            return new TentativaDerivacao([
                'sucesso'  => true,
                'mensagem' => 'sucesso',
                'arvore'   => $this->arvore,
                'passos'   => $passosExecutados,
            ]);
        }

        $tentativa = $this->derivar($exec);

        if (!$tentativa->getSucesso()) {
            return  $tentativa;
        }

        return new TentativaDerivacao([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
            'arvore'   => $this->arvore,
            'passos'   => [...$passosExecutados, ...$tentativa->getPassos()],
        ]);
    }

    /**
     *
     * @param  PassoTicagem[]   $passosExecutados
     * @param  ?PassoTicagem    $novoPasso
     * @return TentativaTicagem
     */
    public function reconstruirTicagem(array $passosExecutados, ?PassoTicagem $novoPasso = null): TentativaTicagem
    {
        $tentativa = TicarTodosNos::exec($this->arvore, $passosExecutados);

        if (!$tentativa->getSucesso() || is_null($novoPasso)) {
            return $tentativa;
        }

        $tentativa = TicarNo::exec($this->arvore, $novoPasso);

        if (!$tentativa->getSucesso()) {
            return  $tentativa;
        }

        return new TentativaDerivacao([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
            'arvore'   => $this->arvore,
            'passos'   => [...$passosExecutados, ...$tentativa->getPassos()],
        ]);
    }

        /**
         *
         * @param  PassoFechamento[]   $passosExecutados
         * @param  ?PassoFechamento    $novoPasso
         * @return TentativaFechamento
         */
    public function reconstruirFechamento(array $passosExecutados, ?PassoFechamento $novoPasso = null): TentativaFechamento
    {
        $tentativa = FecharTodosNos::exec($this->arvore, $passosExecutados);

        if (!$tentativa->getSucesso() || is_null($novoPasso)) {
            return $tentativa;
        }

        $tentativa = FecharNo::exec($this->arvore, $novoPasso);

        if (!$tentativa->getSucesso()) {
            return  $tentativa;
        }

        return new TentativaDerivacao([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
            'arvore'   => $this->arvore,
            'passos'   => [...$passosExecutados, ...$tentativa->getPassos()],
        ]);
    }
}
