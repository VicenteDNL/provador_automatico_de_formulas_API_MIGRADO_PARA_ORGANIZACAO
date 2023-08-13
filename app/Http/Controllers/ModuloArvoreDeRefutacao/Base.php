<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Exceptions\ArvoreDeRefutacaoExecption;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores\Arvore;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\AplicadorRegras;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoPossivelFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoPossivelTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNosFolha;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraProximoNoParaInsercao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\GeradorFormula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\GeradorAutomatico;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\GeradorPorPasso;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Visualizadores\Common\Visualizador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Visualizadores\Derivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Visualizadores\Inicializacao;
use App\Models\ExercicioMVFLP;
use Exception;
use SimpleXMLElement;

/**
 *  Classe responsavel por se comunicar com o módulo de árvore de refutação
 *  para realizar as operações de derivações
 *
 *  Toda Operação para criar a arvore de refutação deve passar por essa classe
 */
class Base
{
    /** Informações da inicialização */
    public Inicializacao $inicializacao;

    /** Informações da derivação */
    public Derivacao $derivacao;

    /** Informações da arvore já montada */
    public ?No $arvore;

    /** Tamanho da area de desenho */
    protected float $canvas_width = 0.0;

    /** Ticagem Automatica */
    protected bool $ticagemAutomatica = false;

    /** Fechamento Automatico */
    protected bool $fechamentoAutomatico = false;

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

    /**
     * Lista dos nos já ticados
     *  @var PassosTicagem[]
     */
    protected $ticados;

    /**
     * Lista dos nos já derivador
     * @var PassosFechamento[]
     */
    protected $fechados;

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
        $this->fechados = [];
        $this->ticados = [];
        $this->quantidaRegra = 9;
        $this->derivacao = new Derivacao();
        $this->inicializacao = new Inicializacao();
        $this->aplicadorRegras = new AplicadorRegras();

        try {
            $this->xmlElement = simplexml_load_string($xml);
            $this->formula = $this->geradorFormula->criarFormula($this->xmlElement);
            $this->formulaTexto = $this->geradorFormula->stringFormula($this->xmlElement);
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
            $this->geradorAutomatico->inicializar($this->formula);
            $this->geradorAutomatico->arvoreOtimizada();
            $this->arvore = $this->geradorAutomatico->getArvore();

            $impressao = $this->visualizador->gerarImpressaoArvore(
                $this->arvore,
                $this->formula,
                $this->canvas_width,
                true,
                true
            );
            $this->arvoreVisualizacao->setArestas($impressao->getArestas());
            $this->arvoreVisualizacao->setNos($impressao->getNos());

            return true;
        } catch(Exception $e) {
            $this->erro = 'Erro ao criar arvore otimizada';
            return false;
        }
    }

    /**
     * @return bool
     */
    public function piorArvore()
    {
        try {
            $this->geradorAutomatico->inicializar($this->formula);
            $this->arvore = $this->geradorAutomatico->piorArvore();

            $impressao = $this->visualizador->gerarImpressaoArvore(
                $this->arvore,
                $this->formula,
                $this->canvas_width,
                true,
                true
            );
            $this->arvoreVisualizacao->setArestas($impressao->getArestas());
            $this->arvoreVisualizacao->setNos($impressao->getNos());

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
        if (!is_null(EncontraProximoNoParaInsercao::exec($this->arvore))) {
            $this->erro = 'derivação incompleta';
            return false;
        }

        if (!$this->fechamentoAutomatico) {
            if (!is_null(EncontraNoPossivelFechamento::exec($this->arvore))) {
                $this->erro = 'derivação incompleta';
                return false;
            }
        }

        if (!$this->ticagemAutomatica) {
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
        $this->inicializacao->setFinalizado($this->inicializacao->getOpcoesDisponiveis() == 0);

        $impressao = $this->visualizador->gerarImpressaoArvore(
            $this->arvore,
            $this->formula,
            $this->canvas_width,
            $this->ticagemAutomatica,
            $this->fechamentoAutomatico
        );

        $this->arvoreVisualizacao->setArestas($impressao->getArestas());
        $this->arvoreVisualizacao->setNos($impressao->getNos());
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

        $tentativa = $this->geradorPorPasso->reconstruirArvore($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados);

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados);

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

        $impressao = $this->visualizador->gerarImpressaoArvore(
            $this->arvore,
            $this->formula,
            $this->canvas_width,
            $this->ticagemAutomatica,
            $this->fechamentoAutomatico
        );
        $this->arvoreVisualizacao->setArestas($impressao->getArestas());
        $this->arvoreVisualizacao->setNos($impressao->getNos());

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

        $tentativa = $this->geradorPorPasso->reconstruirArvore($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados);

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados, $passo);

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }
        $this->arvore = $tentativa->getArvore();
        $this->fechados = $tentativa->getPassos();

        $impressao = $this->visualizador->gerarImpressaoArvore(
            $this->arvore,
            $this->formula,
            $this->canvas_width,
            $this->ticagemAutomatica,
            $this->fechamentoAutomatico
        );
        $this->arvoreVisualizacao->setArestas($impressao->getArestas());
        $this->arvoreVisualizacao->setNos($impressao->getNos());

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

        $tentativa = $this->geradorPorPasso->reconstruirArvore($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados);

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados, $passo);

        if (!$tentativa->getSucesso()) {
            $this->erro = $tentativa->getMensagem();
            return  false;
        }

        $this->arvore = $tentativa->getArvore();
        $this->ticados = $tentativa->getPassos();

        $impressao = $this->visualizador->gerarImpressaoArvore(
            $this->arvore,
            $this->formula,
            $this->canvas_width,
            $this->ticagemAutomatica,
            $this->fechamentoAutomatico
        );

        $this->arvoreVisualizacao->setArestas($impressao->getArestas());
        $this->arvoreVisualizacao->setNos($impressao->getNos());

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
        $this->fechados = $request['arvore']['fechar']['passosExecutados'] ?? [];
        $this->ticados = $request['arvore']['ticar']['passosExecutados'] ?? [];
        $this->ticagemAutomatica = $request['arvore']['ticar']['automatico'] ?? false;
        $this->fechamentoAutomatico = $request['arvore']['fechar']['automatico'] ?? false;
    }

    public function imprimir()
    {
        $retorno = [

            'visualizar'   => $this->arvoreVisualizacao,
            'derivar'      => (object)[
                'passosExecutados' => $this->derivacao->getPassosExecutados(),
                'regras'           => is_null($this->arvore) ? [] : $this->aplicadorRegras->listaPosibilidades($this->arvore, $this->quantidaRegra),
            ],
            'fechar'       => (object)[
                'passosExecutados'       => $this->fechados,
                'automatico'             => $this->fechamentoAutomatico,
            ],
            'iniciar' => (object)[
                'passosExecutados'    => $this->inicializacao->getPassosExecutados(),
                'opcoes'              => $this->visualizador->gerarOpcoesInicializacao($this->formula, $this->inicializacao->getPassosExecutados()),
                'isCompleto'          => $this->inicializacao->isFinalizado(),
            ],
            'ticar' => (object)[
                'passosExecutados'    => $this->ticados,
                'automatico'          => $this->ticagemAutomatica,
            ],
            'formula' => [
                'xml'        => $this->xmlTexto,
                'strformula' => $this->formulaTexto,
            ],

        ];

        return $retorno;
    }

    /**
     * @return array{ arestas: Aresta[], nos:No[] ,strformula:string }
     */
    public function imprimirArvore()
    {
        return [
            'arestas'    => $this->arvoreVisualizacao->getArestas(),
            'nos'        => $this->arvoreVisualizacao->getNos(),
            'strformula' => $this->formulaTexto,
        ] ;
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

//     private function isFinalizada()
//     {
//         if (!$this->inicializacao->getFinalizado()) {
//             return false;
//         }

//         if ($this->gerador->proximoNoParaInsercao($this->arvore) != null) {
//             return false;
//         }

//         if ($this->fechamentoAutomatico == false) {
//             if ($this->gerador->existeNoPossivelFechamento($this->arvore) != null) {
//                 return false;
//             }
//         }

//         if ($this->ticagemAutomatica == false) {
//             if ($this->gerador->existeNoPossivelTicagem($this->arvore) != null) {
//                 return false;
//             }
//         }

//         return true;
//     }

    /**
     * @return string
     */
    public function getErro(): string
    {
        return $this->erro;
    }
}
