<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao;

use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoDerivacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Processadores\PassoTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores\Arvore;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\AplicadorRegras;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores\EncontraNoPossivelFechamento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores\EncontraNoPossivelTicagem;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores\EncontraNosFolha;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\Common\Buscadores\EncontraProximoNoParaInsercao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\GeradorAutomatico;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\GeradorFormula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Processadores\GeradorPorPasso;
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
    public No $arvore;

    /** Tamanho da area de desenho */
    protected float $canvas_width = 0.0;

    /** Ticagem Automatica */
    protected bool $ticagemAutomatica = false;

    /** Fechamento Automatico */
    protected bool $fechamentoAutomatico = false;

    /** Resposta final da arvore */
    protected string $resposta;

    /** Mensagem de error em caso da execução não ter sucesso */
    protected string $error;

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
    private GeradorFormula $geradorFormula;
    private GeradorAutomatico $geradorAutomatico;
    private GeradorPorPasso $geradorPorPasso;
    private Visualizador $visualizador;
    private AplicadorRegras $aplicadorRegras;

    public function __construct(string $xml)
    {
        $this->geradorFormula = new GeradorFormula();
        $this->geradorAutomatico = new GeradorAutomatico();
        $this->geradorPorPasso = new GeradorPorPasso();
        $this->visualizador = new Visualizador();
        $this->xmlTexto = $xml;
        $this->formula = $this->geradorFormula->criarFormula($this->xmlElement);
        $this->formulaTexto = $this->geradorFormula->stringFormula($this->xmlElement);
        $this->arvoreVisualizacao = new Arvore();
        $this->fechados = [];
        $this->ticados = [];
        $this->derivacao = new Derivacao();
        $this->inicializacao = new Inicializacao();

        try {
            $this->xmlElement = simplexml_load_string($xml);
        } catch(Exception $e) {
            $this->error = 'XML INVALIDO!';
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
            $this->error = 'derivação incompleta';
            return false;
        }

        if (!$this->fechamentoAutomatico) {
            if (!is_null(EncontraNoPossivelFechamento::exec($this->arvore))) {
                $this->error = 'derivação incompleta';
                return false;
            }
        }

        if (!$this->ticagemAutomatica) {
            if (!is_null(EncontraNoPossivelTicagem::exec($this->arvore))) {
                $this->error = 'derivação incompleta';
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
            $this->error = $tentativa->getMensagem();
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
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirArvore($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados);

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados);

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->derivar($novoPasso);

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
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
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirArvore($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados);

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados, $passo);

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
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
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirArvore($this->derivacao->getPassosExecutados());

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirFechamento($this->fechados);

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
            return  false;
        }

        $tentativa = $this->geradorPorPasso->reconstruirTicagem($this->ticados, $passo);

        if (!$tentativa->getSucesso()) {
            $this->error = $tentativa->getMensagem();
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

    public function setAll($request, $fechar_auto, $ticar_auto)
    {
        $this->setListaPassos($request['inicio']['lista']);
        $this->setListaTicagem($request['ticar']['lista']);
        $this->setListaFechamento($request['fechar']['lista']);
        $this->fecharAutomatido($fechar_auto);
        $this->ticarAutomatico($ticar_auto);
        $this->derivacao->setPassosExecutados($request['derivacao']['lista']);
        $this->inicializacao->isFinalizado(true);
    }

    public function retorno($exercicio, $usu_has, $exe_has, $admin = false)
    {
        $retorno = [
            'regras'       => $exercicio != null ? $this->buscarRegras($exercicio, $admin) : null,
            'exe_hash'     => $exe_has != null ? $exe_has : null,
            'usu_hash'     => $usu_has != null ? $usu_has : null,
            'id_exercicio' => $exercicio,
            'arestas'      => $this->arestas,
            'nos'          => $this->nos,
            'derivacao'    => (object)[
                'lista'  => $this->derivacao->getListaDerivacoes(),
                'folhas' => [],
                'no'     => null,
                'regra'  => null,
            ],
            'fechar' => (object)[
                'lista' => $this->fechados,
                'no'    => null,
                'folha' => null,
                'auto'  => $this->fechamentoAutomatico,
            ],
            'inicio' => (object)[
                'completa' => $this->inicializacao->getFinalizado(),
                'lista'    => $this->inicializacao->getListaInseridos(),
                'negacao'  => null,
                'no'       => null,
                'opcoes'   => $this->inicializacao->getListaOpcoes(),
            ],
            'ticar' => (object)[
                'auto'  => $this->ticagemAutomatica,
                'lista' => $this->ticados,
                'no'    => null,
            ],
            'finalizada' => $this->isFinalizada(),
            'strformula' => $this->getStrFormula(),
            'xml'        => $this->xml,

        ];

        if ($admin == true) {
            array_splice($retorno, 0, 4);
        }
        return $retorno;
    }

//     public function retornoOtimizada()
//     {
//         return [
//             'arestas'    => $this->arestas,
//             'nos'        => $this->nos,
//             'strformula' => $this->getStrFormula(),
//         ] ;
//     }

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
// }
