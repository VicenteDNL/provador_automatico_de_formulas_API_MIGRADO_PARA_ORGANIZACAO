<?php

namespace App\Core\Generators;

use App\Core\Common\Models\Formula\Formula;
use App\Core\Common\Models\Formula\Premissa;
use App\Core\Common\Models\PrintTree\Aresta;
use App\Core\Common\Models\PrintTree\Arvore;
use App\Core\Common\Models\PrintTree\Linha;
use App\Core\Common\Models\PrintTree\No as VizualizadoresNo;
use App\Core\Common\Models\Steps\PassoInicializacao;
use App\Core\Common\Models\Tree\No as ProcessadoresNo;
use App\Core\Common\Models\Tree\OpcaoInicializacao;
use App\Core\Helpers\Buscadores\EncontraNoMaisProfundo;
use App\Http\Controllers\Controller;
use SimpleXMLElement;

class Visualizador extends Controller
{
    public const AREA_LINHA = 200;
    public const AREA_NO = 100;
    private GeradorFormula $geradorFormula;
    private bool $showLines = true;

    public function __construct()
    {
        $this->geradorFormula = new GeradorFormula();
    }

    /**
     * @param ProcessadoresNo|null $arvore
     * @param SimpleXMLElement     $xml
     * @param float                $width
     * @param bool                 $ticar
     * @param bool                 $fechar
     * @param Formula              $formula
     * @param bool                 $showLines
     */
    public function gerarImpressaoArvore(?ProcessadoresNo $arvore, Formula $formula, float $width, bool $ticar = false, bool $fechar = false, bool $showLines = true)
    {
        $this->showLines = $showLines;
        $tamanhoMininoCanvas = $this->larguraMinimaCanvas($formula);

        if ($width < $tamanhoMininoCanvas) {
            $width = $tamanhoMininoCanvas;
        }

        if (is_null($arvore)) {
            return new Arvore([
                'nos'       => [],
                'arestas'   => [],
                'linhas'    => [],
                'width'     => $width + ($this->showLines ? self::AREA_LINHA : 0),
                'height'    => 0,
            ]);
        }

        $listaNo = $this->imprimirNos($arvore, $width, $width / 2, 0, $ticar, $fechar);
        $listaAresta = $this->imprimirArestas($listaNo);
        $linhas = $this->imprimirLinhas($listaNo);
        return new Arvore([
            'nos'       => $listaNo,
            'arestas'   => $listaAresta,
            'linhas'    => $this->showLines ? $linhas : [],
            'width'     => $width + ($this->showLines ? self::AREA_LINHA : 0),
            'height'    => 0,
        ]);
    }

    /**
     * @param  Formula              $formula
     * @param  PassoInicializacao[] $passosExcutado
     * @return OpcaoInicializacao[]
     */
    public function gerarOpcoesInicializacao(Formula $formula, array $passosExcutado): array
    {
        $opcoes = [];

        $premissas = $formula->getPremissas();
        $conclusao = $formula->getConclusao();

        foreach ($passosExcutado as $passo) {
            $executado = array_filter($premissas, fn (Premissa $p) => $p->getId() == $passo->getIdNo());

            if ($executado) {
                unset($premissas[key($executado)]);
            } elseif ($conclusao->getId() == $passo->getIdNo()) {
                unset($conclusao);
            }
        }

        foreach ($premissas as $premissa) {
            $str = $this->geradorFormula->stringArg($premissa->getValorObjPremissa()) ;
            array_push($opcoes, new OpcaoInicializacao([
                'id'       => $premissa->getId(),
                'texto'    => trim($str),
            ]));
        }

        if (isset($conclusao)) {
            $str = $this->geradorFormula->stringArg($conclusao->getValorObjConclusao()) ;
            array_push($opcoes, new OpcaoInicializacao([
                'id'       => $conclusao->getId(),
                'texto'    => $str,
            ]));
        }

        return $opcoes;
    }

    /**
     * @param  Formula $formula
     * @return float
     */
    protected function larguraMinimaCanvas(Formula $formula): float
    {
        $gerador = new GeradorAutomatico();

        $tentativa = $gerador->inicializar($formula);

        if (!$tentativa->getSucesso()) {
            return 0;
        }
        $arvoreIni = $tentativa->getArvore();

        $nosProfundoInici = EncontraNoMaisProfundo::exec($arvoreIni);
        $profundidadeArvInicializada = empty($nosProfundoInici) ? 0 : $nosProfundoInici[0]->getLinhaNo();

        $tentativa = $gerador->piorArvore();

        if (!$tentativa->getSucesso()) {
            return 0;
        }

        $arvore = $tentativa->getArvore();

        $nosProfundoPiorArvore = EncontraNoMaisProfundo::exec($arvore);
        $profundidadePiorArvore = empty($nosProfundoPiorArvore) ? 0 : $nosProfundoPiorArvore[0]->getLinhaNo();

        //A profundidade Final considera apenas 1 dos noós inicias, pois eles nunca são bifurcados, e portanto não interferem na largura final da Arv
        $profundidadeFinal = ($profundidadePiorArvore - $profundidadeArvInicializada) + 1;

        return self::AREA_NO * $profundidadeFinal ;
    }

    /**
     * @param  VizualizadoresNo[] $nos
     * @return Aresta[]
     */
    protected function imprimirArestas(array $nos): array
    {
        $listaAresta = [];

        for ($i = 1; $i < count($nos); ++$i) {
            if ($nos[$i - 1]->getPosY() >= $nos[$i]->getPosY()) {
                for ($e = $i - 1 ; $e > 0; --$e) {
                    if ($nos[$e]->getPosY() < $nos[$i]->getPosY()) {
                        array_push(
                            $listaAresta,
                            new Aresta([
                                'linhaX1' => $nos[$e]->getPosX(),
                                'linhaY1' => $nos[$e]->getPosY() + 27,
                                'linhaX2' => $nos[$i]->getPosX(),
                                'linhaY2' => $nos[$i]->getPosY() - 27,
                            ])
                        );
                        break;
                    }
                }
            } else {
                array_push($listaAresta, [
                    'linhaX1' => $nos[$i - 1]->getPosX(),
                    'linhaY1' => $nos[$i - 1]->getPosY() + 27,
                    'linhaX2' => $nos[$i]->getPosX(),
                    'linhaY2' => $nos[$i]->getPosY() - 27,
                ]);
            }
        }
        return $listaAresta;
    }

    /**
     * @param  ProcessadoresNo    $arvore
     * @param  float              $width
     * @param  float              $posX
     * @param  float              $posY
     * @param  bool               $ticar
     * @param  bool               $fechar
     * @param  ProcessadoresNo[]  $listaNosVisualizadores -> Usado para acesso recursivo
     * @return VizualizadoresNo[]
     */
    protected function imprimirNos(ProcessadoresNo $arvore, float $width, float $posX, float $posY, bool $ticar, bool $fechar, array $listaNosVisualizadores = []): array
    {
        $posYFilho = $posY + 80;
        $str = $this->geradorFormula->stringArg($arvore->getValorNo());
        $tmh = strlen($str) <= 4 ? 40 : (strlen($str) >= 18 ? strlen($str) * 6 : strlen($str) * 8.5);

        $utilizado = $ticar == false ? $arvore->isTicado() : $arvore->isUtilizado();
        $fechado = $fechar == false ? $arvore->isFechamento() : $arvore->isFechado();

        $no = new VizualizadoresNo([
            'str'                => $str,
            'idNo'               => $arvore->getIdNo(),
            'linha'              => $arvore->getLinhaNo(),
            'noFolha'            => $arvore->isNoFolha(),
            'posX'               => $posX + ($this->showLines ? self::AREA_LINHA : 0),
            'posY'               => $posYFilho,
            'tmh'                => $tmh,
            'posXno'             => $posX - ($tmh / 2) + ($this->showLines ? self::AREA_LINHA : 0),
            'linhaDerivacao'     => $arvore->getLinhaDerivacao(),
            'posXlinhaDerivacao' => $posX + ($tmh / 2) + ($this->showLines ? self::AREA_LINHA : 0),
            'utilizado'          => $utilizado,
            'fechado'            => $fechado,
            'linhaContradicao'   => $arvore->getLinhaContradicao(),
            'fill'               => 'url(#grad1)',
            'strokeWidth'        => 2,
            'strokeColor'        => '#C0C0C0',
        ]);
        array_push($listaNosVisualizadores, $no);

        if (!is_null($arvore->getFilhoEsquerdaNo())) {
            $areaFilho = $width / 2 ;
            $posicaoAreaFilho = $areaFilho / 2;
            $posXFilho = $posX - $posicaoAreaFilho ;
            $listaNosVisualizadores = $this->imprimirNos($arvore->getFilhoEsquerdaNo(), $areaFilho, $posXFilho, $posYFilho, $ticar, $fechar, $listaNosVisualizadores);
        }

        if (!is_null($arvore->getFilhoCentroNo())) {
            $listaNosVisualizadores = $this->imprimirNos($arvore->getFilhoCentroNo(), $width, $posX, $posYFilho, $ticar, $fechar, $listaNosVisualizadores);
        }

        if (!is_null($arvore->getFilhoDireitaNo())) {
            $areaFilho = $width / 2 ;
            $posicaoAreaFilho = $areaFilho / 2;
            $posXFilho = $posX + $posicaoAreaFilho ;
            $listaNosVisualizadores = $this->imprimirNos($arvore->getFilhoDireitaNo(), $areaFilho, $posXFilho, $posYFilho, $ticar, $fechar, $listaNosVisualizadores);
        }
        return $listaNosVisualizadores;
    }

    /**
     * @param  VizualizadoresNo[] $nos
     * @return Linha[]
     */
    protected function imprimirLinhas(array $nos)
    {
        $listaLinhas = [];

        foreach ($nos as $no) {
            $linha = array_filter($listaLinhas, fn (Linha $l) => $l->getNumero() == $no->getLinha()) ;

            if (empty($linha)) {
                array_push(
                    $listaLinhas,
                    new Linha([
                        'texto'  => 'Linha ' . $no->getLinha(),
                        'numero' => $no->getLinha(),
                        'posX'   => 30,
                        'posY'   => $no->getPosY() + 5,
                    ])
                );
            }
        }
        return $listaLinhas;
    }
}
