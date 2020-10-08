<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Formula;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Models\Formula\Predicado;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Models\Formula\Conclusao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Models\Formula\Premissa;

class Argumento extends Controller
{
    /* Função que verifica se o filho do elemento XML é igual à  Lpred*/
    private function childrenIsLpred ($pai){
        return $pai->children()->getName()=="LPRED" ? true : false;
    }

    /*Função que encontra o tipo do filho elemento XML,e o passa para as função de construção
    do OBJECT corespondente ao tipo do filho (CONDICIONAL,BICONDICIONAL,DISJUNCAO,CONJUNCAO) */
    private function encontraFilho($pai){
        $nome = $pai->children()->getName();
        if ($nome =="CONDICIONAL"){
            return $this->condicional($pai->children());
        }
        elseif($nome =="BICONDICIONAL"){
            return $this->bicondicional($pai->children());
        }
        elseif ($nome =="DISJUNCAO"){
            return $this->disjuncao($pai->children());
        }
        elseif($nome =="CONJUNCAO"){
            return $this->conjuncao($pai->children());
        }
    }

    /*Função que extrai o valor do elemento Lpred do XML, e o atributo de negação */
    private function lpred($lpred){
        $negacao=$lpred->attributes()["NEG"];
        return ['NEG'=>$this->qntdNegacao($negacao), 'PREDICATIVO'=>$lpred->children()->__toString()];
    }

    public function qntdNegacao($artribudto){
        return strlen ( $artribudto);

}
    /*Função recebe o elemento CONDICIONAL XML, e retorna o Objeto Predicado*/
    private function condicional($condicional){
        $antecendente_xml =$condicional->children()[0];
        $consequente_xml = $condicional->children()[1];

        if ($this->childrenIsLpred($antecendente_xml)){
            $antecendente_array = $this->lpred($antecendente_xml->children());
            $antecendente_no = new Predicado($antecendente_array['PREDICATIVO'],$antecendente_array['NEG'],'PREDICATIVO',null,null);
        }else{
            $antecendente_no = $this->encontraFilho($antecendente_xml);
        }
        if ($this->childrenIsLpred($consequente_xml)){
            $consequente_array = $this->lpred($consequente_xml->children());
            $consequente_no = new Predicado($consequente_array['PREDICATIVO'],$consequente_array['NEG'],'PREDICATIVO',null,null);
        }else{
            $consequente_no = $this->encontraFilho($consequente_xml);
        }
        $valor = $antecendente_no->getValorPredicado()."→".$consequente_no->getValorPredicado();
        return new Predicado($valor,$this->qntdNegacao($condicional->attributes()["NEG"]),'CONDICIONAL',$antecendente_no,$consequente_no);
    }

    /*Função recebe o elemento BICONDICIONAL XML, e retorna o Objeto Predicado*/
    private function bicondicional($bicondicional){
        $primario_xml = $bicondicional->children()[0];
        $secundario_xml = $bicondicional->children()[1];

        if ($this->childrenIsLpred($primario_xml)){
            $primario_array = $this->lpred($primario_xml->children());
            $primario_no = new Predicado($primario_array['PREDICATIVO'],$primario_array['NEG'],'PREDICATIVO',null,null);
        }else{
            $primario_no = $this->encontraFilho($primario_xml);
        }
        if ($this->childrenIsLpred($secundario_xml)){
            $secundario_array = $this->lpred($secundario_xml->children());
            $secundario_no = new Predicado($secundario_array['PREDICATIVO'],$secundario_array['NEG'],'PREDICATIVO',null,null);
        }else{
            $secundario_no = $this->encontraFilho($secundario_xml);
        }
        $valor = $primario_no->getValorPredicado()."↔".$secundario_no->getValorPredicado();
        return new Predicado($valor,$this->qntdNegacao($bicondicional->attributes()["NEG"]),'BICONDICIONAL',$primario_no,$secundario_no);
    }

    /*Função recebe o elemento DISJUNÇÃO XML, e retorna o Objeto Predicado*/
    private function disjuncao($disjuncao){
        $primario_xml = $disjuncao->children()[0];
        $secundario_xml = $disjuncao->children()[1];
        if ($this->childrenIsLpred($primario_xml)){
            $primario_array = $this->lpred($primario_xml->children());
            $primario_no = new Predicado($primario_array['PREDICATIVO'],$primario_array['NEG'],'PREDICATIVO',null,null);
        }else{
            $primario_no = $this->encontraFilho($primario_xml);
        }
        if ($this->childrenIsLpred($secundario_xml)){
            $secundario_array = $this->lpred($secundario_xml->children());
            $secundario_no = new Predicado($secundario_array['PREDICATIVO'],$secundario_array['NEG'],'PREDICATIVO',null,null);
        }else{
            $secundario_no = $this->encontraFilho($secundario_xml);
        }
        $valor = $primario_no->getValorPredicado()."v".$secundario_no->getValorPredicado();
        return new Predicado($valor,$this->qntdNegacao($disjuncao->attributes()["NEG"]),'DISJUNCAO',$primario_no,$secundario_no);
    }

    /*Função recebe o elemento CONJUNÇÃO XML, e retorna o Objeto Predicado*/
    private function conjuncao($conjuncao){
        $primario_xml = $conjuncao->children()[0];
        $secundario_xml = $conjuncao->children()[1];

        if ($this->childrenIsLpred($primario_xml)){
            $primario_array = $this->lpred($primario_xml->children());
            $primario_no = new Predicado($primario_array['PREDICATIVO'],$primario_array['NEG'],'PREDICATIVO',null,null);
        }else{
            $primario_no = $this->encontraFilho($primario_xml);
        }
        if ($this->childrenIsLpred($secundario_xml)){
            $secundario_array = $this->lpred($secundario_xml->children());
            $secundario_no = new Predicado($secundario_array['PREDICATIVO'],$secundario_array['NEG'],'PREDICATIVO',null,null);
        }else{
            $secundario_no = $this->encontraFilho($secundario_xml);
        }
        $valor = $primario_no->getValorPredicado()."^". $secundario_no->getValorPredicado();
        return new Predicado($valor,$this->qntdNegacao($conjuncao->attributes()["NEG"]),'CONJUNCAO',$primario_no,$secundario_no);
    }

    /*Função recebe o elemento PREMISSA XML, e retorna o Objeto Premissa*/
    private function premissa($premissa){
        if ($premissa->getName()=="PREMISSA"){
            if($this->childrenIsLpred($premissa)){
                $premissa_array = $this->lpred($premissa->children());
                $valor_no = new Predicado ($premissa_array['PREDICATIVO'],$premissa_array['NEG'],'PREDICATIVO',null,null);
                return new Premissa($valor_no->getValorPredicado(), $valor_no);
            }
            else{
                $nome = $premissa->children()->getName();
                if($nome=="CONDICIONAL"){
                    $valor = $this->condicional($premissa->children());
                }
                elseif($nome=="BICONDICIONAL"){
                    $valor = $this->bicondicional($premissa->children());
                }
                elseif($nome=="DISJUNCAO"){
                    $valor = $this->disjuncao($premissa->children());
                }
                elseif($nome=="CONJUNCAO"){
                    $valor = $this->conjuncao($premissa->children());
                }
            }
            return new Premissa($valor->getValorPredicado(), $valor);
        }
        return false;
    }

    /*Função recebe o elemento CONCLUSÃO XML, e retorna o Objeto Conclusao*/
    private function conclusao ($conclusao){
        if ($conclusao->getName()=="CONCLUSAO"){
            if($this->childrenIsLpred($conclusao)){
                $conclusao_array = $this->lpred($conclusao->children());
                $valor_no = new Predicado($conclusao_array['PREDICATIVO'],$conclusao_array['NEG'],'PREDICATIVO',null,null);
                return new Conclusao($valor_no->getValorPredicado(),"|- ", $valor_no);
            }else{
                $nome = $conclusao->children()->getName();
                if($nome=="CONDICIONAL"){
                    $valor = $this->condicional($conclusao->children());
                }
                elseif($nome=="BICONDICIONAL"){
                    $valor = $this->bicondicional($conclusao->children());
                }
                elseif($nome=="DISJUNCAO"){
                    $valor = $this->disjuncao($conclusao->children());
                }
                elseif($nome=="CONJUNCAO"){
                    $valor = $this->conjuncao($conclusao->children());
                }
            }
            return new Conclusao($valor->getValorPredicado(),"|- ",$valor);
        }
        return false;
    }

    /*Função recebe o elemento XML, e retorna o [[Objetos(Premissas)],[Objetos(Conclusao)]]*/
    public function CriaListaArgumentos($xml){
        $arrayPremissas=[];
        $arrayConclusao=[];
        
        foreach ($xml as $filhos){
            if ($filhos->getName()=='PREMISSA'){
                 array_push($arrayPremissas, $this->premissa($filhos));
            }
            if ($filhos->getName()=='CONCLUSAO'){
                array_push($arrayConclusao, $this->conclusao($filhos));
            }
        }

        return ["premissas" =>$arrayPremissas, "conclusao" =>$arrayConclusao];


    }

    public function stringArg($argumento){
        if (in_array($argumento->getTipoPredicado(), ['CONJUNCAO','BICONDICIONAL','CONDICIONAL', 'DISJUNCAO'])){
            $negacao='';
            $string=null;
            if ($argumento->getNegadoPredicado()>0){
                for($i = 0 ; $i < $argumento->getNegadoPredicado(); $i++){
                    $negacao="~ ".$negacao;
                }
            }
            switch ($argumento->getTipoPredicado()) {
                case 'CONJUNCAO':
                    $string = $negacao.' ('.$this->stringArg($argumento->getEsquerdaPredicado()).' ^ '.$this->stringArg($argumento->getDireitaPredicado()).')';
                    break;
                case 'BICONDICIONAL':
                    $string = $negacao.' ('.$this->stringArg($argumento->getEsquerdaPredicado()).' ↔ '.$this->stringArg($argumento->getDireitaPredicado()).')';
                    break;
                case 'CONDICIONAL':

                    $string = $negacao.'('.$this->stringArg($argumento->getEsquerdaPredicado()).' → '.$this->stringArg($argumento->getDireitaPredicado()).')';
                    break;
                case 'DISJUNCAO':
                    $string =$negacao.' ('.$this->stringArg($argumento->getEsquerdaPredicado()).' v '.$this->stringArg($argumento->getDireitaPredicado()).')';
                    break;
            }
            return $string;
        }
        else{
            $negacao='';
            if ($argumento->getNegadoPredicado()>0){
                for($i = 0 ; $i < $argumento->getNegadoPredicado(); $i++){
                    $negacao="~ ".$negacao;
                }
            }
            $string= $negacao.' '.$argumento->getValorPredicado();

            return $string;
        }

    }

    public function stringFormula($xml){
        $formula = '';
        $listaArgumentos=$this->CriaListaArgumentos($xml);

        foreach ($listaArgumentos['premissas'] as $premissa){
            $formula = $formula.' '.$this->stringArg( $premissa->getValorObjPremissa()).', ';
        }
        $formula = $formula.' |- '.$this->stringArg($listaArgumentos['conclusao'][0]->getValorObjConclusao());
        return $formula;
    }
}
