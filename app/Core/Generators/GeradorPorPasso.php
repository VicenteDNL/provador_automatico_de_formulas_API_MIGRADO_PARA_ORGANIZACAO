<?php

namespace App\Core\Generators;

use App\Core\Common\Models\Attempts\TentativaDerivacao;
use App\Core\Common\Models\Attempts\TentativaFechamento;
use App\Core\Common\Models\Attempts\TentativaInicializacao;
use App\Core\Common\Models\Attempts\TentativaTicagem;
use App\Core\Common\Models\Formula\Formula;
use App\Core\Common\Models\Steps\PassoDerivacao;
use App\Core\Common\Models\Steps\PassoFechamento;
use App\Core\Common\Models\Steps\PassoInicializacao;
use App\Core\Common\Models\Steps\PassoTicagem;
use App\Core\Helpers\Manipuladores\FecharNo;
use App\Core\Helpers\Manipuladores\FecharTodosNos;
use App\Core\Helpers\Manipuladores\TicarNo;
use App\Core\Helpers\Manipuladores\TicarTodosNos;

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
        foreach ($passosExecutados as $passo) {
            $tentativa = $this->inserirNoIncializacao($formula, $passo->getIdNo(), $passo->getNegacao());

            if (!$tentativa->getSucesso()) {
                return  $tentativa;
            }
        }

        if (is_null($novoPasso)) {
            return new TentativaInicializacao([
                'sucesso'  => true,
                'mensagem' => 'sucesso',
                'arvore'   => $this->arvore,
                'passos'   => $passosExecutados,
            ]);
        }

        $tentativa = $this->inserirNoIncializacao($formula, $novoPasso->getIdNo(), $novoPasso->getNegacao());

        if (!$tentativa->getSucesso()) {
            return  $tentativa;
        }

        array_push($passosExecutados, $novoPasso);
        return new TentativaInicializacao([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
            'arvore'   => $this->arvore,
            'passos'   => $passosExecutados,
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
    public function reconstruirDerivacao(array $passosExecutados, ?PassoDerivacao $passoNovo = null): TentativaDerivacao
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
        if (is_null($this->arvore)) {
            return new TentativaTicagem([
                'sucesso'  => true,
                'mensagem' => 'sucesso',
                'arvore'   => $this->arvore,
                'passos'   => $passosExecutados,
            ]);
        }

        $tentativa = TicarTodosNos::exec($this->arvore, $passosExecutados);

        if (!$tentativa->getSucesso() || is_null($novoPasso)) {
            return $tentativa;
        }

        $tentativa = TicarNo::exec($this->arvore, $novoPasso);

        if (!$tentativa->getSucesso()) {
            return  $tentativa;
        }

        return new TentativaTicagem([
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
        if (is_null($this->arvore)) {
            return new TentativaFechamento([
                'sucesso'  => true,
                'mensagem' => 'sucesso',
                'arvore'   => $this->arvore,
                'passos'   => $passosExecutados,
            ]);
        }

        $tentativa = FecharTodosNos::exec($this->arvore, $passosExecutados);

        if (!$tentativa->getSucesso() || is_null($novoPasso)) {
            return $tentativa;
        }

        $tentativa = FecharNo::exec($this->arvore, $novoPasso);

        if (!$tentativa->getSucesso()) {
            return  $tentativa;
        }

        return new TentativaFechamento([
            'sucesso'  => true,
            'mensagem' => 'sucesso',
            'arvore'   => $this->arvore,
            'passos'   => [...$passosExecutados, ...$tentativa->getPassos()],
        ]);
    }
}
