<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Visualizadores\Common;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\No as ProcessadoresNo;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Geradores\PassoInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores\Aresta;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores\Arvore;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores\No as VizualizadoresNo;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Common\Models\Vizualizadores\OpcaoInicializacao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\Buscadores\EncontraNoMaisProfundo;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\Common\GeradorFormula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Geradores\GeradorAutomatico;
use SimpleXMLElement;

class Visualizador extends Controller
{
    private GeradorFormula $geradorFormula;

    public function __construct()
    {
        $this->geradorFormula = new GeradorFormula();
    }

    /**
     * @param ProcessadoresNo  $arvore
     * @param SimpleXMLElement $xml
     * @param float            $width
     * @param bool             $ticar
     * @param bool             $fechar
     * @param Formula          $formula
     */
    public function gerarImpressaoArvore(ProcessadoresNo $arvore, Formula $formula, float $width, bool $ticar = false, bool $fechar = false)
    {
        $tamanhoMininoCanvas = $this->larguraMinimaCanvas($formula);

        if ($width < $tamanhoMininoCanvas) {
            $width = $tamanhoMininoCanvas;
        }

        $listaNo = $this->imprimirNos($arvore, $width, $width / 2, 0, $ticar, $fechar);
        $listaAresta = $this->imprimirArestas($listaNo);
        return new Arvore(['nos' => $listaNo, 'arestas' => $listaAresta]);
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
            $identi = str_split($passo->getIdNo(), strrpos($passo->getIdNo(), '_'));
            $id = substr($identi[1], 1);

            if ($identi[0] == 'premissa') {
                unset($premissas[$id]);
            } else {
                unset($conclusao);
            }
        }

        //REAVALIAR
        foreach ($premissas as $key => $premissa) {
            $str = $this->geradorFormula->stringArg($premissa->getValorObjPremissa()) ;
            array_push($opcoes, new OpcaoInicializacao([
                'posicao'  => $key,
                'id'       => 'premissa_' . $key,
                'tipo'     => 'premissa',
                'texto'    => $str,
            ]));
        }

        //REAVALIAR
        if (isset($conclusao)) {
            $str = $this->geradorFormula->stringArg($conclusao->getValorObjConclusao()) ;
            array_push($opcoes, new OpcaoInicializacao([
                'posicao'  => 1,
                'tipo'     => 'conclusao',
                'id'       => 'conclusao_' . 1,
                'texto'    => $str,
            ]));
        }

        return $opcoes;
    }

    /**
     * @param  Formula $formula
     * @return floar
     */
    protected function larguraMinimaCanvas(Formula $formula): float
    {
        $gerador = new GeradorAutomatico();
        $arvoreIni = $gerador->inicializar($formula);

        $nosProfundoInici = EncontraNoMaisProfundo::exec($arvoreIni);
        $profundidadeArvInicializada = empty($nosProfundoInici) ? 0 : $nosProfundoInici[0]->getLinhaNo();

        $arvorePior = $gerador->piorArvore();
        $nosProfundoPiorArvore = EncontraNoMaisProfundo::exec($arvorePior);
        $profundidadePiorArvore = empty($nosProfundoPiorArvore) ? 0 : $nosProfundoPiorArvore[0]->getLinhaNo();

        //A profundidade Final considera apenas 1 dos noós inicias, pois eles nunca são bifurcados, e portanto não interferem na largura final da Arv
        $profundidadeFinal = ($profundidadePiorArvore - $profundidadeArvInicializada) + 1;

        // O valor destinada ao espaço que deverá ser ocupado por um nó ( alterar esse valor para mudar a largura)
        $areaDoNo = 100;

        return $areaDoNo * $profundidadeFinal ;
    }

    /**
     * @param  VizualizadoresNo[] $nos
     * @return Aresta
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
            'posX'               => $posX,
            'posY'               => $posYFilho,
            'tmh'                => $tmh,
            'posXno'             => $posX - ($tmh / 2),
            'linhaDerivacao'     => $arvore->getLinhaDerivacao(),
            'posXlinhaDerivacao' => $posX + ($tmh / 2),
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
}
