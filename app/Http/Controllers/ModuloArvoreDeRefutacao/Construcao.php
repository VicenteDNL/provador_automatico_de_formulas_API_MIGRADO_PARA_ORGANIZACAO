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


    public function geraListaArvore($arvore,$width,$posX,$posY,$array=[]){
        
        $listaNo = $this->geraListaNO($arvore,$width,$posX,$posY,$array=[]);
        $listaAresta = $this->geraListaArestas($listaNo);
        return ['nos'=>$listaNo,'arestas'=>$listaAresta];

    }
        


    public function geraListaNO($arvore,$width,$posX,$posY,$array=[])
    {
        $posYFilho = $posY + 80;
        $str = $this->arg->stringArg($arvore->getValorNo());
        $tmh = strlen ( $str )<=4 ? 40 : (strlen ( $str  )>= 18 ? strlen ( $str) *6 : strlen ( $str )*8.5 );
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
            'utilizado'=>$arvore->isTicado(),
            'fechado'=>$arvore->isFechamento(),
            'linhaContradicao'=>$arvore->getLinhaContradicao(),
            'fill'=>'url(#grad1)',
            'strokeWidth'=>'2',
            'strokeColor'=>'#C0C0C0',
            ]);
        if ($arvore->getFilhoEsquerdaNo() != null) {
            $divisao = $width / ($arvore->getFilhoEsquerdaNo()->getLinhaNo() + 1);
            $posXFilho = 0;
            for ($i = 0; $i < ($arvore->getFilhoEsquerdaNo()->getLinhaNo() + 1); $i++) {
                if (($divisao + $posXFilho) < $posX) {
                    $posXFilho = $posXFilho + $divisao;
                }
            }
            $array = $this->geraListaNO($arvore->getFilhoEsquerdaNo(), $width, $posXFilho, $posYFilho,$array);
        }
        if ($arvore->getFilhoCentroNo() != null) {

            $array = $this->geraListaNO($arvore->getFilhoCentroNo(), $width, $posX, $posYFilho,$array);
        }
        if ($arvore->getFilhoDireitaNo() != null) {
            $divisao = $width / ($arvore->getFilhoDireitaNo()->getLinhaNo() + 1);
            $posXFilho = $width;
            for ($i = 0; $i <($arvore->getFilhoDireitaNo()->getLinhaNo() + 1); $i++) {
                if ( $posXFilho-$divisao > $posX) {
                    $posXFilho = $posXFilho - $divisao;
                }
            }
            $array = $this->geraListaNO($arvore->getFilhoDireitaNo(), $width, $posXFilho, $posYFilho,$array);
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
            $str= $premissa->getValorStrPremissa();
                array_push($lista,[
                    'pos'=>$key,
                    'id'=>'premissa_'.$key,
                    'tipo'=>'premissa',
                    'str'=>$str
                ]);
        }

        for($i = 0; $i<count($list['conclusao']); $i++ ){
            if(array_key_exists($i,$list['conclusao'])){
                $str= $list['conclusao'][$i]->getValorStrConclusao();
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


    public function geraSVG($arv){

        // Configuraçoes do estilo

        $corRGB = 'rgb(175,175,175)';
        $largura='1';
        $linecap="'butt'";



        $svg='<svg width="700" height="660">';

        for ($i = 1; $i<count($arv); $i++){
            if($arv[$i-1]['posY']>=($arv[$i]['posY'])){
                for ($e = $i-1 ; $e>0;$e--){
                    if($arv[$e]['posY']<($arv[$i]['posY'])){

                        $posSvgX_1=strval($arv[$e]['posX']);
                        $posSvgY_1=strval($arv[$e]['posY']+27);

                        $posSvgX_2=strval($arv[$i]['posX']);
                        $posSvgY_2=strval($arv[$i]['posY']-27);
                    }
                }
            }
            else{

                $posSvgX_1=strval($arv[$i-1]['posX']);
                $posSvgY_1=strval($arv[$i-1]['posY']+27);

                $posSvgX_2=strval($arv[$i]['posX']);
                $posSvgY_2=strval($arv[$i]['posY']-27);


            }

        };

        $svg=$svg+"<line x1=".$posSvgX_1." y1=".$posSvgY_1." x2=".$posSvgX_2." y2=".$posSvgY_2." stroke=".$corRGB." stroke-width=".$largura." stroke-linecap=".$linecap."/>";


        foreach($arv as $valor){
            $svg=$svg+"<circle cx=".$valor['posX']." cy=".($valor['posY']+27)." r=".'3'." fill='#AFAFA4'/>";
            $svg=$svg+"<circle cx=".$valor['posX']." cy=".($valor['posY']-27)." r=".'3'." fill='#AFAFAF'/>";


            $svg=$svg+"<defs>";
                $svg=$svg+"<linearGradient id='grad1' x1='30%' y1='0%' x2='90%' y2='50%'>";
                    $svg=$svg+"<stop offset='0%' style='stop-color:rgb(32,178,170);stop-opacity:1' />";
                    $svg=$svg+"<stop offset='100%' style='stop-color:rgb(0,128,128);stop-opacity:1' />";
                $svg=$svg+"</linearGradient>";
            $svg=$svg+"</defs>";

            $svg=$svg+"<rect x=".($valor['posX']-($valor['tmh']/2))." y=".($valor['posY']-20)." rx=20 ry=20 width=".$valor['tmh']." height='40' fill='url(#grad1)' stroke=#C0C0C0 stroke-width=2/>";
            $svg=$svg+"<text text-anchor='middle' font-size='15' font-weight='bold' fill='white'  font-family='Helvetica, sans-serif, Arial' x=".$valor['posX']." y=".($valor['posY']+5).">".$valor['str']."</text>";
            $svg=$svg+ "<text font-size='15' font-weight='bold' fill='rgb(175,175,175)' x=".($valor['posX']+($valor['tmh']/2))." y=".($valor['posY']+25).">".($valor['arv']->getLinhaDerivacao())."</text>";

            
            if($valor['arv']->isUtilizado()==1){
                $svg=$svg+ "<svg x=".($valor['posX']+($valor['tmh']/2)+12)." y=".($valor['posY']-10)." fill=#61CE61>";
                $svg=$svg+"<path d='M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z'/>";
                $svg=$svg+"</svg>";
            }
            if($valor['arv']->isFechado()==true){
                $svg=$svg+"<line x1=".($valor['posX']-15)." y1=".($valor['posY']+15)." x2=".($valor['posX']+15)." y2=".($valor['posY']+40)." stroke='#DC0F4B' stroke-width=4 stroke-dasharray='1'/>";
                $svg=$svg+"<line x1=".($valor['posX']+15)." y1=".($valor['posY']+15)." x2=".($valor['posX']-15)." y2=".($valor['posY']+40)." stroke='#DC0F4B' stroke-width=4 stroke-dasharray='1'/>";
                $svg=$svg+"<text font-size='17' fill='#DC0F4B' x=".($valor['posX']-5 )." y=".($valor['posY']+70 ).">".($valor['arv']->getLinhaContradicao())."</text>";
            };
        };
    
        $svg=$svg+'</svg>';
        return $svg;
        
        

    }




}


