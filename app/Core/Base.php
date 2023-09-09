<?php

namespace App\Core;

use App\Core\Common\Exceptions\ArvoreDeRefutacaoExecption;
use App\Core\Common\Models\Formula\Formula;
use App\Core\Common\Models\PrintTree\Arvore;
use App\Core\Common\Models\Steps\PassoDerivacao;
use App\Core\Common\Models\Steps\PassoFechamento;
use App\Core\Common\Models\Steps\PassoInicializacao;
use App\Core\Common\Models\Steps\PassoTicagem;
use App\Core\Common\Models\Tree\Derivacao;
use App\Core\Common\Models\Tree\Fechamento;
use App\Core\Common\Models\Tree\Inicializacao;
use App\Core\Common\Models\Tree\No;
use App\Core\Common\Models\Tree\Ticagem;
use App\Core\Generators\AplicadorRegras;
use App\Core\Generators\GeradorAutomatico;
use App\Core\Generators\GeradorFormula;
use App\Core\Generators\GeradorPorPasso;
use App\Core\Generators\Visualizador;
use App\Core\Helpers\Buscadores\EncontraNoPossivelFechamento;
use App\Core\Helpers\Buscadores\EncontraNoPossivelTicagem;
use App\Core\Helpers\Buscadores\EncontraNosFolha;
use App\Core\Helpers\Validadores\ExisteDerivacaoPossivelDeInsercao;
use Exception;
use SimpleXMLElement;

/**
 *  Classe responsavel por se comunicar com o core de árvore de refutação
 *  para realizar as operações de derivações,
 *
 *  Afim de facilizar o uso recomenda-se utilizar esta classe para
 *  todas operação de manipulação d arvore
 */
class Base
{
    /** Informações da inicialização */
    protected Inicializacao $inicializacao;

    /** Informações da derivação */
    protected Derivacao $derivacao;

    /** Informações da ticagem */
    protected Ticagem $ticados;

    /** Informações da fechamento */
    protected Fechamento $fechados;

    /** Informações da arvore já montada */
    protected ?No $arvore;

    /** Tamanho da area de desenho */
    protected float $canvas_width = 0.0;

    /** Resposta final da arvore */
    protected string $resposta;

    /** Mensagem de erro em caso da execução não ter sucesso */
    protected string $erro;

    /** String do XML da formula */
    protected string $xmlTexto;

    /** Objeto do Xml da formula */
    protected SimpleXMLElement $xmlElement;

    /** Contem as informacoes para desenha a arvore */
    protected Arvore $arvoreVisualizacao;

    /** Formula gerada a partir do XML */
    protected Formula $formula;

    /** String da Formula */
    protected string $formulaTexto;

    /** Quantidade de regras que devem retornar*/
    protected int $quantidaRegra;
    private GeradorFormula $geradorFormula;
    private GeradorAutomatico $geradorAutomatico;
    private GeradorPorPasso $geradorPorPasso;
    private Visualizador $visualizador;
    private AplicadorRegras $aplicadorRegras;

    /**
     * @param  string                     $xml
     * @throws ArvoreDeRefutacaoExecption
     */
    public function __construct(string $xml)
    {
        $this->arvore = null;
        $this->geradorFormula = new GeradorFormula();
        $this->geradorAutomatico = new GeradorAutomatico();
        $this->geradorPorPasso = new GeradorPorPasso();
        $this->visualizador = new Visualizador();
        $this->xmlTexto = $xml;
        $this->arvoreVisualizacao = new Arvore();
        $this->fechados = new Fechamento();
        $this->ticados = new Ticagem();
        $this->quantidaRegra = 9;
        $this->derivacao = new Derivacao();
        $this->inicializacao = new Inicializacao();
        $this->aplicadorRegras = new AplicadorRegras();

        try {
            $this->xmlElement = simplexml_load_string($xml);
            $this->formula = $this->geradorFormula->criarFormula($this->xmlElement);
            $this->formulaTexto = $this->geradorFormula->stringFormula($this->xmlElement);
            $this->inicializacao->setOpcoesDisponiveis($this->visualizador->gerarOpcoesInicializacao($this->formula, []));
        } catch(Exception $e) {
            throw new ArvoreDeRefutacaoExecption('XML INVALIDO!');
        }
    }

    /**
     * @return bool
     */
    public function arvoreOtimizada(): bool
    {
        try {
            $tentativa = $this->geradorAutomatico->inicializar($this->formula);

            if (!$tentativa->getSucesso()) {
                $this->erro = $tentativa->getMensagem();
                return false;
            }

            $tentativa = $this->geradorAutomatico->arvoreOtimizada();

            if (!$tentativa->getSucesso()) {
                $this->erro = $tentativa->getMensagem();
                return false;
            }

            $this->arvore = $this->geradorAutomatico->getArvore();

            return true;
        } catch(Exception $e) {
            $this->erro = 'Erro ao criar arvore otimizada';
            return false;
        }
    }

    /**
     * @return bool
     */
    public function piorArvore(): bool
    {
        try {
            $tentativa = $this->geradorAutomatico->inicializar($this->formula);

            if (!$tentativa->getSucesso()) {
                $this->erro = $tentativa->getMensagem();
                return false;
            }

            $tentativa = $this->geradorAutomatico->piorArvore();

            if (!$tentativa->getSucesso()) {
                $this->erro = $tentativa->getMensagem();
                return false;
            }

            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function concluirDerivacao(): bool
    {
        if (!is_null(ExisteDerivacaoPossivelDeInsercao::exec($this->arvore))) {
            $this->erro = 'derivação incompleta';
            return false;
        }

        if (!$this->fechados->isAutomatico()) {
            if (!is_null(EncontraNoPossivelFechamento::exec($this->arvore))) {
                $this->erro = 'derivação incompleta';
                return false;
            }
        }

        if (!$this->ticados->isAutomatico()) {
            if (!is_null(EncontraNoPossivelTicagem::exec($this->arvore))) {
                $this->erro = 'derivação incompleta';
                return false;
            }
        }

        if (!is_null(EncontraNosFolha::exec($this->arvore))) {
            $this->resposta = 'CONTRADICAO';
        }
        $this->resposta = 'TAUTOLOGIA';

        return true;
    }

    /**
     * @param  PassoInicializacao $novoPasso
     * @return bool
     */
    public function tentativaInicializacao(PassoInicializacao $novoPasso): bool
    {
        $tentativa = $this->geradorPorPasso->reconstruirInicializacao(
            $this->formula,
            $this->inicializacao->getPassosExecutados(),
            $novoPasso
        );

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $this->arvore = $tentativa->getArvore();
        $this->inicializacao->setPassosExecutados($tentativa->getPassos());
        $this->inicializacao->setOpcoesDisponiveis($this->visualizador->gerarOpcoesInicializacao($this->formula, $tentativa->getPassos()));
        $this->inicializacao->setCompleto(empty($this->inicializacao->getOpcoesDisponiveis()));

        return true;
    }

     /**
      * @param PassoDerivacao $novoPasso
      * @return bool
      */
    public function tentativaDerivacao(PassoDerivacao $novoPasso): bool
    {
        $tentativa = $this->geradorPorPasso->reconstruirInicializacao($this->formula, $this->inicializacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $this->inicializacao->setPassosExecutados($tentativa->getPassos());
        $this->inicializacao->setOpcoesDisponiveis($this->visualizador->gerarOpcoesInicializacao($this->formula, $tentativa->getPassos()));
        $this->inicializacao->setCompleto(empty($this->inicializacao->getOpcoesDisponiveis()));

        $tentativa = $this->geradorPorPasso->reconstruirDerivacao($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->derivar($novoPasso);

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $this->arvore = $tentativa->getArvore();
        $this->derivacao->addPasso($novoPasso);

        return true;
    }

    /**
     * @param  PassoFechamento $passo
     * @return bool
     */
    public function tentativaFechamento(PassoFechamento $passo): bool
    {
        $tentativa = $this->geradorPorPasso->reconstruirInicializacao($this->formula, $this->inicializacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $this->inicializacao->setPassosExecutados($tentativa->getPassos());
        $this->inicializacao->setOpcoesDisponiveis($this->visualizador->gerarOpcoesInicializacao($this->formula, $tentativa->getPassos()));
        $this->inicializacao->setCompleto(count($this->inicializacao->getOpcoesDisponiveis()) == 0);

        $tentativa = $this->geradorPorPasso->reconstruirDerivacao($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados->getPassosExecutados(), $passo);

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }
        $this->arvore = $tentativa->getArvore();
        $this->fechados->setPassosExecutados($tentativa->getPassos());

        return true;
    }

    /**
     * @param  PassoTicagem $passo
     * @return bool
     */
    public function tentativaTicagem(PassoTicagem $passo): bool
    {
        $tentativa = $this->geradorPorPasso->reconstruirInicializacao($this->formula, $this->inicializacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $this->inicializacao->setPassosExecutados($tentativa->getPassos());
        $this->inicializacao->setOpcoesDisponiveis($this->visualizador->gerarOpcoesInicializacao($this->formula, $tentativa->getPassos()));
        $this->inicializacao->setCompleto(count($this->inicializacao->getOpcoesDisponiveis()) == 0);

        $tentativa = $this->geradorPorPasso->reconstruirDerivacao($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados->getPassosExecutados(), $passo);

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $this->arvore = $tentativa->getArvore();
        $this->ticados->setPassosExecutados($tentativa->getPassos());

        return true;
    }

    /**
     * @return bool
     */
    public function reconstruirPassos(): bool
    {
        $tentativa = $this->geradorPorPasso->reconstruirInicializacao($this->formula, $this->inicializacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $this->inicializacao->setPassosExecutados($tentativa->getPassos());
        $this->inicializacao->setOpcoesDisponiveis($this->visualizador->gerarOpcoesInicializacao($this->formula, $tentativa->getPassos()));
        $this->inicializacao->setCompleto(count($this->inicializacao->getOpcoesDisponiveis()) == 0);

        $tentativa = $this->geradorPorPasso->reconstruirDerivacao($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $this->arvore = $tentativa->getArvore();
        $this->ticados->setPassosExecutados($tentativa->getPassos());

        return true;
    }

    /**
     * recebe como paramentro o request e adiciona o
     * valores essenciais para o processo de derivação
     * @param mixed $request
     */
    public function carregarCamposEssenciais($request): void
    {
        $this->inicializacao->setPassosExecutados($request['arvore']['iniciar']['passosExecutados'] ?? []);
        $this->derivacao->setPassosExecutados($request['arvore']['derivar']['passosExecutados'] ?? []);
        $this->fechados->setPassosExecutados($request['arvore']['fechar']['passosExecutados'] ?? []);
        $this->fechados->setAutomatico($request['arvore']['fechar']['isAutomatico'] ?? false);
        $this->ticados->setPassosExecutados($request['arvore']['ticar']['passosExecutados'] ?? []);
        $this->ticados->setAutomatico($request['arvore']['ticar']['isAutomatico'] ?? false);
    }

    /**
     * @param  bool  $exibirLinhas
     * @return array
     */
    public function imprimir(bool $exibirLinhas = true)
    {
        $retorno = [

            'visualizar'   => $this->gerarImpressaoArvore($exibirLinhas),
            'derivar'      => (object)[
                'passosExecutados' => $this->derivacao->getPassosExecutados(),
                'regras'           => is_null($this->arvore) ? [] : $this->aplicadorRegras->listaPosibilidades($this->arvore, $this->quantidaRegra),
            ],
            'fechar'  => $this->fechados,
            'iniciar' => $this->inicializacao,
            'ticar'   => $this->ticados,
            'formula' => [
                'xml'        => $this->xmlTexto,
                'texto'      => $this->formulaTexto,
            ],
            'isCompleto' => $this->isFinalizada(),

        ];

        return $retorno;
    }

    /**
     * @param  bool  $exibirLinhas
     * @return array
     */
    public function imprimirArvore(bool $exibirLinhas = true)
    {
        return [
            'visualizar'   => $this->gerarImpressaoArvore($exibirLinhas),
            'formula'      => [
                'xml'        => $this->xmlTexto,
                'texto'      => $this->formulaTexto,
            ],
        ] ;
    }

    /**
     * @return string
     */
    public function getErro(): string
    {
        return $this->erro;
    }

//     private function buscarRegras($exercicio, $admin)
//     {

//         if (!$this->inicializacao->isFinalizado() || $admin) {
//             return [];
//         }
//         $exercicio = ExercicioMVFLP::findOrFail($exercicio);
//         $formula = Formula::findOrFail($exercicio->id_formula);
//         return $this->gerador->arrayPerguntas($this->arvore, $formula->quantidade_regras);
//         $this->aplicadorRegras
//     };

    /**
     * Verifica se todas as etapas foram concluidas
     * @return bool
     */
    public function isFinalizada(): bool
    {
        if (!$this->inicializacao->isCompleto()) {
            return false;
        }

        if (ExisteDerivacaoPossivelDeInsercao::exec($this->arvore)) {
            return false;
        }

        if (!$this->fechados->isAutomatico()) {
            if (!is_null(EncontraNoPossivelFechamento::exec($this->arvore))) {
                return false;
            }
        }

        if (!$this->ticados->isAutomatico()) {
            if (!is_null(EncontraNoPossivelTicagem::exec($this->arvore))) {
                return false;
            }
        }

        return true;
    }

    private function gerarImpressaoArvore(bool $exibirLinhas = true)
    {
        $impressao = $this->visualizador->gerarImpressaoArvore(
            $this->arvore,
            $this->formula,
            $this->canvas_width,
            $this->ticados->isAutomatico(),
            $this->fechados->isAutomatico(),
            $exibirLinhas
        );
        $this->arvoreVisualizacao = $impressao;
        return $this->arvoreVisualizacao;
    }
}
