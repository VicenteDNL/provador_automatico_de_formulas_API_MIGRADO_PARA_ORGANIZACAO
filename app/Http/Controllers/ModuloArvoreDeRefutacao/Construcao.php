<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao;

use Illuminate\Http\Request;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Argumento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore\Gerador;
use App\Http\Controllers\Controller;


class Construcao extends Controller
{
 function __construct() {
        $this->arg = new Argumento;
        $this->gerador = new Gerador;
 }

    public function stringXmlDiretorio(){
     $dir=dirname(__FILE__,4.).'\storage\app\public\formulas';
        $diretorio = scandir($dir);
        $num = count($diretorio) - 2;
        $listaFormulas=[];
        for($i=1; $i <= $num ; $i++){
            $xml = simplexml_load_file($dir.'\formula-'.$i.'.xml');
            $formula = [
                'str'=>$this->arg->stringFormula($xml),
                'xml'=>$i
            ];
            array_push($listaFormulas,$formula);
        }

        return $listaFormulas;

    }


    public function geraListaArvore($arvore,$xml,$width, $ticar=false, $fechar=false){

        $tamanhoMininoCanvas = $this->larguraMinimaCanvas($xml);
        if ($width<$tamanhoMininoCanvas){
            $width = $tamanhoMininoCanvas;
        }

        $listaNo = $this->geraListaNO($arvore,$width,$width/2,0,$array=[],$ticar,$fechar);
        $listaAresta = $this->geraListaArestas($listaNo);
        return ['nos'=>$listaNo,'arestas'=>$listaAresta];

    }



    public function geraListaNO($arvore,$width,$posX,$posY,$array=[],$ticar,$fechar)
    {
        $posYFilho = $posY + 80;
        $str = $this->arg->stringArg($arvore->getValorNo());
        $tmh = strlen ( $str )<=4 ? 40 : (strlen ( $str  )>= 18 ? strlen ( $str) *6 : strlen ( $str )*8.5 );

        $utilizado=$ticar==false? $arvore->isTicado(): $arvore->isUtilizado();
        $fechado=$fechar==false? $arvore->isFechamento(): $arvore->isFechado();

        array_push($array,[
            'arv'=>$arvore,
            'str'=>$str,
            'idNo'=>$arvore->getIdNo(),
            'linha'=>$arvore->getLinhaNo(),
            'noFolha'=>$arvore->isNoFolha(),
            'posX'=>$posX,
            'posY'=>$posYFilho,
            'tmh'=>$tmh,
            'posXno'=>$posX-($tmh/2),
            'linhaDerivacao'=>$arvore->getLinhaDerivacao(),
            'posXlinhaDerivacao'=>$posX+($tmh/2),
            'utilizado'=>$utilizado,
            'fechado'=>$fechado,
            'linhaContradicao'=>$arvore->getLinhaContradicao(),
            'fill'=>'url(#grad1)',
            'strokeWidth'=>'2',
            'strokeColor'=>'#C0C0C0',
            ]);
        if ($arvore->getFilhoEsquerdaNo() != null) {
            $areaFilho = $width / 2 ;
            $posicaoAreaFilho = $areaFilho / 2;
            $posXFilho = $posX - $posicaoAreaFilho ;
            $array = $this->geraListaNO($arvore->getFilhoEsquerdaNo(), $areaFilho, $posXFilho, $posYFilho,$array,$ticar,$fechar);
        }
        if ($arvore->getFilhoCentroNo() != null) {

            $array = $this->geraListaNO($arvore->getFilhoCentroNo(), $width, $posX, $posYFilho,$array,$ticar,$fechar);
        }
        if ($arvore->getFilhoDireitaNo() != null) {
            $areaFilho = $width / 2 ;
            $posicaoAreaFilho = $areaFilho / 2;
            $posXFilho = $posX + $posicaoAreaFilho ;
            $array = $this->geraListaNO($arvore->getFilhoDireitaNo(), $areaFilho, $posXFilho, $posYFilho,$array,$ticar,$fechar);
        }
        return $array;
    }


    public function geraListaArestas($listaNo){
            $listaAresta=[];
            for($i = 1; $i<count($listaNo); $i++){
                if($listaNo[$i-1]['posY']>=($listaNo[$i]['posY'])){
                    for ($e = $i-1 ; $e>0;$e--){
                        if($listaNo[$e]['posY']<($listaNo[$i]['posY'])){
                            array_push($listaAresta,[
                                        'linhaX1'=>$listaNo[$e]['posX'],
                                        'linhaY1'=>$listaNo[$e]['posY']+27,
                                        'linhaX2'=>$listaNo[$i]['posX'],
                                        'linhaY2'=>$listaNo[$i]['posY']-27
                                        ]);
                            break;
                        }

                    }
                }
                else{
                    array_push($listaAresta,[
                        'linhaX1'=>$listaNo[$i-1]['posX'],
                        'linhaY1'=>$listaNo[$i-1]['posY']+27,
                        'linhaX2'=>$listaNo[$i]['posX'],
                        'linhaY2'=>$listaNo[$i]['posY']-27
                        ]);
                }


            }
            return $listaAresta;
    }


    public function geraListaPremissasConclsao($list, $listPassos){
        $lista=[];
        foreach($listPassos as $passos){
            $identi= str_split ($passos['idNo'],strrpos($passos['idNo'], "_"));
            $id=substr($identi[1], 1);
            if($identi[0]=='premissa'){
                 unset($list['premissas'][$id]);
            }
            else{

                unset($list['conclusao'][$id]);
            }
        }

        foreach($list['premissas'] as $key => $premissa){
            $str= $this->arg->stringArg($premissa->getValorObjPremissa()) ;
                array_push($lista,[
                    'pos'=>$key,
                    'id'=>'premissa_'.$key,
                    'tipo'=>'premissa',
                    'str'=>$str
                ]);
        }

        for($i = 0; $i<count($list['conclusao']); $i++ ){
            if(array_key_exists($i,$list['conclusao'])){
                $str= $this->arg->stringArg($list['conclusao'][$i]->getValorObjConclusao()) ;
                array_push($lista,[
                    'pos'=>$i,
                    'tipo'=>'conclusao',
                    'id'=>'conclusao_'.$i,
                    'str'=>$str
                ]);
            }
        }

        return $lista;

    }

    public function larguraMinimaCanvas($xml){

        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);

        $profundidadeArvInicializada = $this->gerador->getUltimaLinha();
        $this->gerador->piorArvore($arvore);
        $profundidadePiorArvore  = $this->gerador->getUltimaLinha();

        //A profundidade Final considera apenas 1 dos noós inicias, pois eles nunca são bifurcados, e portanto não interferem na largura final da Arv
        $profundidadeFinal = ($profundidadePiorArvore - $profundidadeArvInicializada) + 1;

        // O valor destinada ao espaço que deverá ser ocupado por um nó ( alterar esse valor para mudar a largura)
        $areaDoNo = 100;

        return ($areaDoNo *$profundidadeFinal );

    }



}


