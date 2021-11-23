<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Models\Arvore\No;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Regras;

class Gerador extends Controller
{
    private $idNo;
    private $arvore;
    private $ultimalinha=0;

     function __construct() {
            $this->regras = new Regras();
     }

    public function getUltimaLinha(){
        return $this->ultimalinha;
    }

     private function addLinha(){
         $this->ultimalinha=$this->ultimalinha+1;
     }


    public function getNoPeloId($arvore, $id)
    {
        $NoId=false;
        if ($arvore->getIdNo()== $id){
                return $arvore;
        }
        else{
            if ($arvore->getFilhoEsquerdaNo()!=null and $NoId==false ){;
                $NoId = $this->getNoPeloId($arvore->getFilhoEsquerdaNo(),$id);
            }
            if ($arvore->getFilhoCentroNo()!=null and $NoId==false ){
                $NoId = $this->getNoPeloId($arvore->getFilhoCentroNo(),$id);
            }
            if ($arvore->getFilhoDireitaNo()!=null and $NoId==false ){
                $NoId = $this->getNoPeloId($arvore->getFilhoDireitaNo(),$id);
            }
            return $NoId;
        }
    }

    /*Recebe o numero da linha  e a arvore e retorna todos os nos que estão no mesma linha*/
    public function getNoslinha($arvore,$linha, $nos=[]){
          //corrigir retorno mais de um elemento

        if ($arvore->getLinhaNo()==$linha){
            array_push($nos,$arvore);
            }

        if($arvore->getFilhoCentroNo()!=null){
            $nos= $this->getNoslinha($arvore->getFilhoCentroNo(),$linha,$nos);
        }
        elseif($arvore->getFilhoEsquerdaNo()!=null and $arvore->getFilhoDireitaNo()!=null){
            $nos= $this->getNoslinha($arvore->getFilhoEsquerdaNo(),$linha, $nos);
            $nos= $this->getNoslinha($arvore->getFilhoDireitaNo(),$linha, $nos);
        }
        return $nos;
    }

     /* Esta função gera e retorna as primeiras linhas da arvores de refutacao, a partir das premissas e conclusão */
    public function inicializarDerivacao ($premissas,$conclusao){

        $ultimoNo=null;
        if ($premissas !=null){
            foreach ($premissas as $premissa){
                $this->addLinha();
                if ($this->arvore==null){
                    $this->idNo+=1;
                    $this->arvore = new No($this->idNo,$premissa->getValorObjPremissa(),null,null,null,$this->getUltimaLinha(),null,null,false,false);
                    $ultimoNo=$this->arvore;
                }
                else{
                    $this->idNo+=1;
                    $ultimoNo->setFilhoCentroNo(new No( $this->idNo,$premissa->getValorObjPremissa(),null,null,null,$this->getUltimaLinha(),null,null,false,false));
                    $ultimoNo=$ultimoNo->getFilhoCentroNo();
                }

            }
        }
        if ($conclusao !=null){
            $conclusao[0]->getValorObjConclusao()->addNegacaoPredicado();
            if ($this->arvore==null){
                $this->idNo+=1;
                $this->arvore= (new No($this->idNo,$conclusao[0]->getValorObjConclusao(),null,null,null,1,null,null,false,false));
                $ultimoNo=$this->arvore;
            }else{
                $this->idNo+=1;
                $ultimoNo->setFilhoCentroNo(new No( $this->idNo,$conclusao[0]->getValorObjConclusao(),null,null,null,$this->getUltimaLinha(),null,null,false,false));
                $ultimoNo=$ultimoNo->getFilhoCentroNo();
            }
            $this->addLinha();
        }
        return $this->arvore;
    }

     /* está função encontra o NO que possui dupla negação e o retorna, se nao encontrar retorna false
        buscando do nó raiz até os nos folhas*/
    public function encontraDuplaNegacao($arvore,$noSemBifur){
        $negacao=false;
        if ($arvore->getValorNo()->getNegadoPredicado()>=2 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}
        }
        else{
            if ($arvore->getFilhoEsquerdaNo()!=null and $negacao==false ){;
                $negacao = $this->encontraDuplaNegacao($arvore->getFilhoEsquerdaNo(),$noSemBifur);
            }
            if ($arvore->getFilhoCentroNo()!=null and $negacao==false ){
                $negacao = $this->encontraDuplaNegacao($arvore->getFilhoCentroNo(),$noSemBifur);
            }
            if ($arvore->getFilhoDireitaNo()!=null and $negacao==false ){
                $negacao = $this->encontraDuplaNegacao($arvore->getFilhoDireitaNo(),$noSemBifur);
            }
            return $negacao;
        }

    }

     /* está função encontrar o NO que possui bifurcação e ainda nao utilizado e o retorna No, se nao encontrar retorna false, buscando do nó raiz até os nos folhas*/
     public function encontraNoBifuca($arvore,$noSemBifur){
         $NoBifucacao = false;
         if (in_array($arvore->getValorNo()->getTipoPredicado(), ['DISJUNCAO','CONDICIONAL','BICONDICIONAL']) and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}
         }
         else if (in_array($arvore->getValorNo()->getTipoPredicado(), ['CONJUNCAO','BICONDICIONAL']) and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}
         }
         else{
            if ($arvore->getFilhoEsquerdaNo()!=null and $NoBifucacao==false){
                $NoBifucacao = $this->encontraNoBifuca($arvore->getFilhoEsquerdaNo(),$noSemBifur);
             }
            if ($arvore->getFilhoCentroNo()!=null and $NoBifucacao==false){
                $NoBifucacao = $this->encontraNoBifuca($arvore->getFilhoCentroNo(),$noSemBifur);
             }
             if ($arvore->getFilhoDireitaNo()!=null and $NoBifucacao==false){
                 $NoBifucacao = $this->encontraNoBifuca($arvore->getFilhoDireitaNo(),$noSemBifur);
             }
             return  $NoBifucacao;
         }
     }

     /* está função encontrar o NO que possui nao possui bifurcação e ainda nao utilizado e o retorna No, se nao encontrar retorna false, buscando do nó raiz até os nos folhas*/
     public function encontraNoSemBifucacao($arvore,$noSemBifur){
         $NoSemBifucacao = false;
         if ($arvore->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}

         }
         else if (in_array($arvore->getValorNo()->getTipoPredicado(), ['DISJUNCAO','CONDICIONAL']) and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}
         }
         else{
             if ($arvore->getFilhoEsquerdaNo()!=null and $NoSemBifucacao==false){
                 $NoSemBifucacao = $this->encontraNoSemBifucacao($arvore->getFilhoEsquerdaNo(),$noSemBifur);
             }
             if ($arvore->getFilhoCentroNo()!=null and $NoSemBifucacao==false){
                 $NoSemBifucacao = $this->encontraNoSemBifucacao($arvore->getFilhoCentroNo(),$noSemBifur);
             }
             if ($arvore->getFilhoDireitaNo()!=null and $NoSemBifucacao==false){
                 $NoSemBifucacao = $this->encontraNoSemBifucacao($arvore->getFilhoDireitaNo(),$noSemBifur);
             }
             return $NoSemBifucacao;
         }
     }

     /*esta funçao recebe com parametro a arvore atual, e retorna uma array com a referencia de todos os nós folhas que não foram fechado*/
     public function getNosFolha($arvore, $ListaDeNo=null){


        if ($arvore->getFilhoDireitaNo() ==null and  $arvore->getFilhoEsquerdaNo() ==null and  $arvore->getFilhoCentroNo() ==null  and $arvore->isFechado()==false){
            $ListaDeNo[] =  $arvore;
            return  $ListaDeNo;
        }
        else {
            if ($arvore->getFilhoCentroNo()!=null){

                $ListaDeNo = $this->getNosFolha($arvore->getFilhoCentroNo(),$ListaDeNo);
            }
            if ($arvore->getFilhoEsquerdaNo()!=null){
                $ListaDeNo = $this->getNosFolha($arvore->getFilhoEsquerdaNo(),$ListaDeNo);
            }
            if ($arvore->getFilhoDireitaNo()!=null ){
                $ListaDeNo = $this->getNosFolha($arvore->getFilhoDireitaNo(),$ListaDeNo);
            }
            return $ListaDeNo;
        }
    }


    /*esta funçao recebe com parametro a arvore atual, e retorna uma array com a referencia de todos os nós folhas incluindo os nós fechados*/
    public function getTodosNosFolha($arvore, $ListaDeNo=null){


        if ($arvore->getFilhoDireitaNo() ==null and  $arvore->getFilhoEsquerdaNo() ==null and  $arvore->getFilhoCentroNo() ==null){
            $ListaDeNo[] =  $arvore;
            return  $ListaDeNo;
        }
        else {
            if ($arvore->getFilhoCentroNo()!=null){

                $ListaDeNo = $this->getTodosNosFolha($arvore->getFilhoCentroNo(),$ListaDeNo);
            }
            if ($arvore->getFilhoEsquerdaNo()!=null){
                $ListaDeNo = $this->getTodosNosFolha($arvore->getFilhoEsquerdaNo(),$ListaDeNo);
            }
            if ($arvore->getFilhoDireitaNo()!=null ){
                $ListaDeNo = $this->getTodosNosFolha($arvore->getFilhoDireitaNo(),$ListaDeNo);
            }
            return $ListaDeNo;
        }
    }


    /*esta funçao recebe com parametro a arvore atual, e um boleano (indica se entre os seus descentende foi encontrado um No que ainda nao foi fechado pelo usuario)d,
    percorrendo da centro-esquerda-direita- para ate encontrar
     um No folha apto para ser fechado */
    public function existeNoPossivelFechamento($arvore, $noAberto = false){

        $proximoNo =null;

         if ($arvore->getFilhoDireitaNo() ==null and  $arvore->getFilhoEsquerdaNo() ==null and  $arvore->getFilhoCentroNo() ==null  and $arvore->isFechamento()==false and $arvore->isFechado()==true){
            return $arvore;
         }


         else {
             if ($arvore->getFilhoCentroNo()!=null and $proximoNo ==null){

                 $proximoNo = $this->existeNoPossivelFechamento($arvore->getFilhoCentroNo(),$noAberto);
             }
             if ($arvore->getFilhoEsquerdaNo()!=null and $proximoNo ==null){
                 $proximoNo = $this->existeNoPossivelFechamento($arvore->getFilhoEsquerdaNo(),$noAberto);
             }
             if ($arvore->getFilhoDireitaNo()!=null and $proximoNo ==null){
                 $proximoNo = $this->existeNoPossivelFechamento($arvore->getFilhoDireitaNo(),$noAberto);
             }
             return $proximoNo;
         }

    }


    public function existeNoPossivelTicagem($arvore, $ticado = false){
        $proximoNo =null;

        if ($arvore->isUtilizado()==true and ( (in_array($arvore->getValorNo()->getTipoPredicado(), ['DISJUNCAO','CONDICIONAL','BICONDICIONAL','CONJUNCAO'])) or ( $arvore->getValorNo()->getTipoPredicado()=='PREDICATIVO' and $arvore->getValorNo()->getNegadoPredicado()>=2)) and $arvore->isTicado()==false){

            return $arvore;
        }


        else {
            if ($arvore->getFilhoCentroNo()!=null and $proximoNo ==null){

                $proximoNo = $this->existeNoPossivelTicagem($arvore->getFilhoCentroNo(),$ticado);
            }
            if ($arvore->getFilhoEsquerdaNo()!=null and $proximoNo ==null){
                $proximoNo = $this->existeNoPossivelTicagem($arvore->getFilhoEsquerdaNo(),$ticado);
            }
            if ($arvore->getFilhoDireitaNo()!=null and $proximoNo ==null){
                $proximoNo = $this->existeNoPossivelTicagem($arvore->getFilhoDireitaNo(),$ticado);
            }
            return $proximoNo;
        }

    }

        /*esta funçao recebe com parametro a arvore atual, e um boleano (indica se entre os seus descentende foi encontrado um No que ainda nao foi derivado), percorrendo da centro -esquerda-direita- para ate encontras
     um No folha apto para ser o proximo a ser inserido, caso nao encontre returna NULL*/
        public function  proximoNoParaInsercao($arvore, $descendenteSemDerivacao = false){

         $proximoNo =null;


         if ( $arvore->isUtilizado()==false and ( (in_array($arvore->getValorNo()->getTipoPredicado(), ['DISJUNCAO','CONDICIONAL','BICONDICIONAL','CONJUNCAO'])) or ( $arvore->getValorNo()->getTipoPredicado()=='PREDICATIVO' and $arvore->getValorNo()->getNegadoPredicado()>=2) )){
            $descendenteSemDerivacao = true;
         }

         if ($arvore->getFilhoDireitaNo() ==null and  $arvore->getFilhoEsquerdaNo() ==null and  $arvore->getFilhoCentroNo() ==null  and $arvore->isFechado()==false and  $descendenteSemDerivacao==true){
            return $arvore;
         }
         else {
             if ($arvore->getFilhoCentroNo()!=null and $proximoNo ==null){

                 $proximoNo = $this->proximoNoParaInsercao($arvore->getFilhoCentroNo(),$descendenteSemDerivacao);
             }
             if ($arvore->getFilhoEsquerdaNo()!=null and $proximoNo ==null){
                 $proximoNo = $this->proximoNoParaInsercao($arvore->getFilhoEsquerdaNo(),$descendenteSemDerivacao);
             }
             if ($arvore->getFilhoDireitaNo()!=null and $proximoNo ==null){
                 $proximoNo = $this->proximoNoParaInsercao($arvore->getFilhoDireitaNo(),$descendenteSemDerivacao);
             }
             return $proximoNo;
         }
     }

     /*esta funcao recebe uma arvore (a partir de um NO qualquer), e o No de interese, afim de verificar se o no raiz da arvore é parente do no de interesse, e retorna true se a condicao for vedadeira e false se nao for
     verdadeira*/
     public function isDecendente($arvore,$no){


         $noDescendente=false;

        if ($arvore->getValorNo()=== $no->getValorNo()){
            return true;
        }
        else {
            if ($arvore->getFilhoCentroNo()!=null and $noDescendente==false){

                $noDescendente = $this->isDecendente($arvore->getFilhoCentroNo(),$no);

            }
            if ($arvore->getFilhoEsquerdaNo()!=null and $noDescendente==false ){

                $noDescendente = $this->isDecendente($arvore->getFilhoEsquerdaNo(),$no);
            }
            if ($arvore->getFilhoDireitaNo()!=null and $noDescendente==false){
                $noDescendente = $this->isDecendente($arvore->getFilhoDireitaNo(),$no);

            }
            return $noDescendente;

        }
     }

     /* Esta função recebe uma arvore e um NO, e verifica se existe uma contradicao para o NO na Arvore, se verdadeiro retorna o NÓ contraditorio, se nao retorna False*/
     public function encontraContradicao($arvore,$no){
        $contradicao = false;
         if ($arvore->getValorNo()->getValorPredicado() == $no->getValorNo()->getValorPredicado()){

             $negacaoNo = $no->getValorNo()->getNegadoPredicado();

             if ($negacaoNo == 1 and $arvore->getValorNo()->getNegadoPredicado()==0){
                 if ($this->isDecendente($arvore,$no)){

                     return $arvore;}
             }
             elseif ($negacaoNo == 0 and $arvore->getValorNo()->getNegadoPredicado()==1) {
                 if ($this->isDecendente($arvore, $no)) {
                     return $arvore;}
             }
            else{
                if ($arvore->getFilhoCentroNo()!=null and $contradicao == false){
                    $contradicao = $this->encontraContradicao($arvore->getFilhoCentroNo(),$no);
                }
                if ($arvore->getFilhoEsquerdaNo()!=null and $contradicao == false){
                    $contradicao = $this->encontraContradicao($arvore->getFilhoEsquerdaNo(),$no);
                }
                if ($arvore->getFilhoDireitaNo()!=null and $contradicao == false){
                    $contradicao =  $this->encontraContradicao($arvore->getFilhoDireitaNo(),$no);
                }
                return $contradicao;

            }
        }
        else {
            if ($arvore->getFilhoCentroNo()!=null and $contradicao == false){
                $contradicao = $this->encontraContradicao($arvore->getFilhoCentroNo(),$no);
            }
            if ($arvore->getFilhoEsquerdaNo()!=null and $contradicao == false){
                $contradicao = $this->encontraContradicao($arvore->getFilhoEsquerdaNo(),$no);
            }
            if ($arvore->getFilhoDireitaNo()!=null and $contradicao == false){
                $contradicao =  $this->encontraContradicao($arvore->getFilhoDireitaNo(),$no);
            }
            return $contradicao;
        }

     }

     /*esta função recebe a referencia do No que vai sofrer inserção, a arvore atual, e o array dos nos resultantes da aplicacao da regra de derivacao e faz a verificação da contradicao do novo no*/
     public function criarNoBifurcado($noInsercao,$arvore,$array_filhos,$linhaDerivado){


        $this->idNo+=1;
        $noInsercao->setFilhoEsquerdaNo(new No($this->idNo,$array_filhos['esquerda'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));

        $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoEsquerdaNo());
        if($contradicao!=false){
            $noInsercao->getFilhoEsquerdaNo()->FecharRamo($contradicao->getLinhaNo());
        }

        $this->idNo+=1;
        $noInsercao->setFilhoDireitaNo(new No($this->idNo,$array_filhos['direita'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
        $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoDireitaNo());
        if($contradicao!=false){
            $noInsercao->getFilhoDireitaNo()->FecharRamo($contradicao->getLinhaNo());
        }
     }

     /*esta função recebe a referencia do No que vai sofrer inserção, a arvore atual, e o array dos nos resultantes da aplicacao da regra de derivacao e faz a verificação da contradicao do novo no*/
     public function criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$linhaDerivado){


        $this->idNo+=1;
        $noInsercao->setFilhoEsquerdaNo(new No($this->idNo,$array_filhos['esquerda'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
        $contradEsq1 = $this->encontraContradicao($arvore,$noInsercao->getFilhoEsquerdaNo());

        $this->idNo+=1;
        $noInsercao->getFilhoEsquerdaNo()->setFilhoCentroNo(new No($this->idNo,$array_filhos['esquerda'][1],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
        $contradEsq2 = $this->encontraContradicao($arvore,$noInsercao->getFilhoEsquerdaNo()->getFilhoCentroNo());
        if($contradEsq2!=false){
            $noInsercao->getFilhoEsquerdaNo()->getFilhoCentroNo()->FecharRamo($contradEsq2->getLinhaNo());
        }
        elseif($contradEsq1!=false and $contradEsq2==false){
            $this->idNo+=1;
            $noInsercao->setFilhoEsquerdaNo(new No($this->idNo, $array_filhos['esquerda'][1],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
            $this->idNo+=1;
            $noInsercao->getFilhoEsquerdaNo()->setFilhoCentroNo(new No($this->idNo,$array_filhos['esquerda'][0],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
            $noInsercao->getFilhoEsquerdaNo()->getFilhoCentroNo()->FecharRamo($contradEsq1->getLinhaNo());
        }


        $this->idNo+=1;
        $noInsercao->setFilhoDireitaNo(new No($this->idNo,$array_filhos['direita'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
        $contradDir1 = $this->encontraContradicao($arvore,$noInsercao->getFilhoDireitaNo());

        $this->idNo+=1;
        $noInsercao->getFilhoDireitaNo()->setFilhoCentroNo(new No($this->idNo,$array_filhos['direita'][1],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
        $contradDir2 = $this->encontraContradicao($arvore,$noInsercao->getFilhoDireitaNo()->getFilhoCentroNo());
        if($contradDir2!=false){
            $noInsercao->getFilhoDireitaNo()->getFilhoCentroNo()->FecharRamo($contradDir2->getLinhaNo());
        }
        elseif($contradDir1!=false and $contradDir2==false){
            $this->idNo+=1;
            $noInsercao->setFilhoDireitaNo(new No($this->idNo,$array_filhos['direita'][1],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
            $this->idNo+=1;
            $noInsercao->getFilhoDireitaNo()->setFilhoCentroNo(new No($this->idNo,$array_filhos['direita'][0],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
            $noInsercao->getFilhoDireitaNo()->getFilhoCentroNo()->FecharRamo($contradDir1->getLinhaNo());
        }
     }

     /*esta função recebe a referencia do No que vai sofrer inserção, a arvore atual, e o array dos nos resultantes da aplicacao da regra de derivacao e faz a verificação da contradicao do novo no*/
     public function criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$linhaDerivado){

        $this->idNo+=1;
        $primeiroNo=new No($this->idNo,$array_filhos['centro'][0],null,null,null,null,null,$linhaDerivado,false,false);
        $this->idNo+=1;
        $segundoNo=new No($this->idNo,$array_filhos['centro'][1],null,null,null,null,null,$linhaDerivado,false,false);


         $noInsercao->setFilhoCentroNo($primeiroNo);
         $noInsercao->getFilhoCentroNo()->setFilhoCentroNo($segundoNo);


         $contradicaoPrim = $this->encontraContradicao($arvore,$noInsercao->getFilhoCentroNo());

         $contradicaoSeg = $this->encontraContradicao($arvore,$noInsercao->getFilhoCentroNo()->getFilhoCentroNo());


         if ($contradicaoPrim!=false and $contradicaoSeg==false){
            $noInsercao->getFilhoCentroNo()->removeFilhoCentroNo();
             $noInsercao->removeFilhoCentroNo();

            $noInsercao->setFilhoCentroNo($segundoNo);
            $noInsercao->getFilhoCentroNo()->setFilhoCentroNo($primeiroNo);

            $primeiroNo->setLinhaNo($noInsercao->getLinhaNo()+2);
            $segundoNo->setLinhaNo($noInsercao->getLinhaNo()+1);
            $noInsercao->getFilhoCentroNo()->getFilhoCentroNo()->FecharRamo($contradicaoPrim->getLinhaNo());
         }
         elseif(($contradicaoPrim!=false and $contradicaoSeg!=false) or ($contradicaoPrim==false and $contradicaoSeg!=false) ){
            $primeiroNo->setLinhaNo($noInsercao->getLinhaNo()+1);
            $segundoNo->setLinhaNo($noInsercao->getLinhaNo()+2);
            $noInsercao->getFilhoCentroNo()->getFilhoCentroNo()->FecharRamo($contradicaoSeg->getLinhaNo());
         }
         else{
            $primeiroNo->setLinhaNo($noInsercao->getLinhaNo()+1);
            $segundoNo->setLinhaNo($noInsercao->getLinhaNo()+2);
         }

     }

     /*esta função recebe a referencia do No que vai sofrer inserção, a arvore atual, e o array dos nos resultantes da aplicacao da regra de derivacao e faz a verificação da contradicao do novo no*/
     public function criarNo($noInsercao,$arvore,$array_filhos,$linhaDerivado){
        $this->idNo+=1;
         $noInsercao->setFilhoCentroNo(new No($this->idNo,$array_filhos['centro'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));

         $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoCentroNo());
         if($contradicao!=false){
             $noInsercao->getFilhoCentroNo()->FecharRamo($contradicao->getLinhaNo());
         }
     }


     public function arvoreOtimizada($arvore){
        $noInsercao=$this->proximoNoParaInsercao($arvore);

        if ($noInsercao==null){
            return $arvore;

        }
        else{

            $no =$this->encontraDuplaNegacao($arvore,$noInsercao);
            $noBifur =$this->encontraNoBifuca($arvore,$noInsercao);
            $noSemBifur =$this->encontraNoSemBifucacao($arvore,$noInsercao);

             if ($no){
                 $array_filhos =$this->regras->DuplaNeg($no->getValorNo());
                 $no->utilizado(true);
                 $this->criarNo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                 return $this->arvoreOtimizada($arvore);
             }
             elseif($noSemBifur){
                 if($noSemBifur->getValorNo()->getTipoPredicado()=='CONJUNCAO' and $noSemBifur->getValorNo()->getNegadoPredicado()==0){
                     $array_filhos = $this->regras->conjuncao($noSemBifur->getValorNo());
                     $noSemBifur->utilizado(true);
                     $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$noSemBifur->getLinhaNo());
                 }
                 else if ($noSemBifur->getValorNo()->getTipoPredicado()== 'DISJUNCAO' and $noSemBifur->getValorNo()->getNegadoPredicado()==1){
                     $array_filhos = $this->regras->disjuncaoNeg($noSemBifur->getValorNo());
                     $noSemBifur->utilizado(true);
                     $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$noSemBifur->getLinhaNo());
                 }
                 elseif ($noSemBifur->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $noSemBifur->getValorNo()->getNegadoPredicado()==1) {
                     $array_filhos = $this->regras->condicionalNeg($noSemBifur->getValorNo());
                     $noSemBifur->utilizado(true);
                     $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$noSemBifur->getLinhaNo());
                 }
                return $this->arvoreOtimizada($arvore);
             }
             elseif($noBifur){
                 if($noBifur->getValorNo()->getTipoPredicado()=='DISJUNCAO' and $noBifur->getValorNo()->getNegadoPredicado()==0){
                     $array_filhos = $this->regras->disjuncao($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 else if ($noBifur->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $noBifur->getValorNo()->getNegadoPredicado()==0){
                     $array_filhos = $this->regras->condicional($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 else if ($noBifur->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $noBifur->getValorNo()->getNegadoPredicado()==0){
                     $array_filhos = $this->regras->bicondicional($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 else if ($noBifur->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $noBifur->getValorNo()->getNegadoPredicado()==1){

                     $array_filhos = $this->regras->conjuncaoNeg($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 else if ($noBifur->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $noBifur->getValorNo()->getNegadoPredicado()==1){
                     $array_filhos = $this->regras->bicondicionalNeg($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 return $this->arvoreOtimizada($arvore);
             }
             return $arvore;
        }
     }

    public function piorArvore($arvore){


        $ListanosFolha = $this->getNosFolha($arvore);

        if ($ListanosFolha==null){
            return $arvore;

        }else{
            $no =$this->encontraDuplaNegacao($arvore,$ListanosFolha[0]);
            $noBifur =$this->encontraNoBifuca($arvore,$ListanosFolha[0]);
            $noSemBifur =$this->encontraNoSemBifucacao($arvore,$ListanosFolha[0]);
            if($noBifur){

                for ($i=0 ; $i<count($ListanosFolha) ; $i++){
                    if (!$this->isDecendente($noBifur, $ListanosFolha[$i])){
                        unset($ListanosFolha[$i]);
                    }
                }

                if($noBifur->getValorNo()->getTipoPredicado()=='DISJUNCAO' and $noBifur->getValorNo()->getNegadoPredicado()==0){
                    foreach ($ListanosFolha as $nosFolha){
                        $array_filhos = $this->regras->disjuncao($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcado($nosFolha,$arvore,$array_filhos,$noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                }
                else if ($noBifur->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $noBifur->getValorNo()->getNegadoPredicado()==0){
                    foreach ($ListanosFolha as $nosFolha){
                        $array_filhos = $this->regras->condicional($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcado($nosFolha,$arvore,$array_filhos,$noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                }
                else if ($noBifur->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $noBifur->getValorNo()->getNegadoPredicado()==0){
                    foreach ($ListanosFolha as $nosFolha){
                        $array_filhos = $this->regras->bicondicional($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcadoDuplo($nosFolha,$arvore,$array_filhos,$noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                    $this->addLinha();
                }
                else if ($noBifur->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $noBifur->getValorNo()->getNegadoPredicado()==1){
                    foreach ($ListanosFolha as $nosFolha){
                        $array_filhos = $this->regras->conjuncaoNeg($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcado($nosFolha,$arvore,$array_filhos,$noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                }
                else if ($noBifur->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $noBifur->getValorNo()->getNegadoPredicado()==1){
                    foreach ($ListanosFolha as $nosFolha){
                        $array_filhos = $this->regras->bicondicionalNeg($noBifur->getValorNo());
                        $noBifur->utilizado(true);
                        $this->criarNoBifurcadoDuplo($nosFolha,$arvore,$array_filhos,$noBifur->getLinhaNo());
                    }
                    $this->addLinha();
                    $this->addLinha();
                }
                return $this->piorArvore($arvore);
            }
            elseif($noSemBifur){

                for ($i=0 ; $i<count($ListanosFolha) ; $i++){
                    if (!$this->isDecendente($noSemBifur, $ListanosFolha[$i])){
                        unset($ListanosFolha[$i]);
                    }
                }

                if($noSemBifur->getValorNo()->getTipoPredicado()=='CONJUNCAO' and $noSemBifur->getValorNo()->getNegadoPredicado()==0){
                    foreach ($ListanosFolha as $nosFolha){
                        $array_filhos = $this->regras->conjuncao($noSemBifur->getValorNo());
                        $noSemBifur->utilizado(true);
                        $this->criarNoSemBifucacao($nosFolha,$arvore,$array_filhos,$noSemBifur->getLinhaNo());
                    }
                }
                else if ($noSemBifur->getValorNo()->getTipoPredicado()== 'DISJUNCAO' and $noSemBifur->getValorNo()->getNegadoPredicado()==1){
                    foreach ($ListanosFolha as $nosFolha){
                        $array_filhos = $this->regras->disjuncaoNeg($noSemBifur->getValorNo());
                        $noSemBifur->utilizado(true);
                        $this->criarNoSemBifucacao($nosFolha,$arvore,$array_filhos,$noSemBifur->getLinhaNo());
                    }
                }
                elseif ($noSemBifur->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $noSemBifur->getValorNo()->getNegadoPredicado()==1) {
                    foreach ($ListanosFolha as $nosFolha){
                        $array_filhos = $this->regras->condicionalNeg($noSemBifur->getValorNo());
                        $noSemBifur->utilizado(true);
                        $this->criarNoSemBifucacao($nosFolha,$arvore,$array_filhos,$noSemBifur->getLinhaNo());
                    }
                }
                $this->addLinha();
                $this->addLinha();
                return $this->piorArvore($arvore);
            }
            elseif($no){

                for ($i=0 ; $i<count($ListanosFolha) ; $i++){
                    if (!$this->isDecendente($no, $ListanosFolha[$i])){
                        unset($ListanosFolha[$i]);
                    }
                }

                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos = $this->regras->DuplaNeg($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNo($nosFolha,$arvore,$array_filhos,$no->getLinhaNo());
                }
                $this->addLinha();
                return $this->piorArvore($arvore);
            }
            return $arvore;
        }

    }

     public function existePossibilidade($array, $id){
         foreach($array as $valor){
             if( $valor['id']==$id){
                 return true;
             }
         }
         return false;
     }

     public function possibilidades($arvore,$array=[]){


        if ($arvore->getValorNo()->getNegadoPredicado()>=2 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,1)==false){
                array_push($array, ['id'=>1,'str'=>'DUPLANAGACAO']);
            }
        }
        elseif($arvore->getValorNo()->getTipoPredicado()=='CONJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,2)==false){
                array_push($array, ['id'=>2,'str'=>'CONJUNCAO']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'DISJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,3)==false){
                array_push($array, ['id'=>3,'str'=>'DISJUNCAONEGADA']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false) {
            if ($this->existePossibilidade($array,4)==false){
                array_push($array, ['id'=>4,'str'=>'CONDICIONALNEGADA']);
            }
        }
        elseif($arvore->getValorNo()->getTipoPredicado()=='DISJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,5)==false){
                array_push($array, ['id'=>5,'str'=>'DISJUNCAO']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,6)==false){
                array_push($array, ['id'=>6,'str'=>'CONDICIONAL']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,7)==false){
                array_push($array, ['id'=>7,'str'=>'BICONDICIONAL']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,8)==false){
                array_push($array, ['id'=>8,'str'=>'CONJUNCAONEGADA']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,9)==false){
                array_push($array, ['id'=>9,'str'=>'BICONDICIONALNEGADA']);
            }
        }


        if ($arvore->getFilhoCentroNo()!=null ){

            $array=$this->possibilidades($arvore->getFilhoCentroNo(),$array);

        }
        elseif($arvore->getFilhoEsquerdaNo()!=null and $arvore->getFilhoDireitaNo()!=null){

            $array=$this->possibilidades($arvore->getFilhoEsquerdaNo(),$array);


            $array=$this->possibilidades($arvore->getFilhoDireitaNo(),$array);


        }
        return  $array;

    }

     public function arrayPerguntas($arvore, $qtdRegras){
        $listapossibilidades = $this->possibilidades($arvore);
         $listaPosValida=[];

         $possibilidades =[
             'DUPLANAGACAO'=>['id'=>1,'str'=>'Negação Negada'],
             'CONJUNCAO'=>['id'=>2,'str'=>'Conjunção'],
             'DISJUNCAONEGADA'=>['id'=>3,'str'=>'Disjunção Negada'],
             'CONDICIONALNEGADA'=>['id'=>4,'str'=>'Condicional Negado'],
             'DISJUNCAO'=>['id'=>5,'str'=>'Disjunção'],
             'CONDICIONAL'=>['id'=>6,'str'=>'Condicional'],
             'BICONDICIONAL'=>['id'=>7,'str'=>'Bicondicional'],
             'CONJUNCAONEGADA'=>['id'=>8,'str'=>'Conjunção Negada'],
             'BICONDICIONALNEGADA'=>['id'=>9,'str'=>'Bicondicional Negado']
         ];

         foreach ( $listapossibilidades as $pos){
                array_push($listaPosValida,$possibilidades[$pos['str']]);

         };


        if (count( $listaPosValida)<$qtdRegras){
            $comp=false;
            while($comp==false){
                $nova = array_rand($possibilidades,1);
                $existe=false;
                foreach ($listaPosValida as $pos){
                    if ($possibilidades[$nova]['id']==$pos['id']){
                        $existe=true;
                    }
                }
                if($existe==false){
                    array_push($listaPosValida,$possibilidades[$nova]);
                    if(count($listaPosValida)==$qtdRegras){
                        $comp=true;}
                }
            }
        }
        elseif(count( $listaPosValida)>$qtdRegras){
            $comp=false;
            while($comp==false){
                shuffle($listaPosValida);
                array_shift ($listaPosValida);
                if (count( $listaPosValida)==$qtdRegras){
                    $comp=true;
                }
            }

        }

        shuffle($listaPosValida);

        return $listaPosValida;
     }


    // Esta função tem a finalidade validar e derivar a tentativa do usuario, para isso ela recebe a arvore a linha e regra do nó que deseja derivar
     public function derivar($arvore, $derivacao,$insercao,$regra){


        $noDerivacao = $this->getNoPeloId($arvore,$derivacao); // o nó a ser derivado
        $ListanoInsercao =[]; // lista de nós folha no qual o nó derivado deve ser inserido

        foreach ($insercao as $no){
            array_push($ListanoInsercao,$this->getNoPeloId($arvore, $no['idNo']));
        }




        #responsavel por descobrir qual o proximo do marcado para inserção, se retornar null significa que não existe mais derivações possiveis
        $existeNoInsercao = $this->proximoNoParaInsercao($arvore);
        if($existeNoInsercao==null){
            return ['sucesso'=>false, 'messagem'=>'não existe mais derivações possiveis'];
        }
        #----------------------


        #Analise sê o no a ser derivado possui mais de um nóFolha valido para inserção
        $ListanosFolha = $this->getNosFolha($arvore);
        for ($i=0 ; $i<count($ListanosFolha) ; $i++){
            if (!$this->isDecendente($noDerivacao, $ListanosFolha[$i])){
                unset($ListanosFolha[$i]);
            }
        }

        if(count($ListanosFolha)==count($ListanoInsercao)){
            foreach($ListanoInsercao as $noInsercao){
                if(in_array($noInsercao,$ListanosFolha)==false){
                    return $noInsercao->isFechado()==true?['sucesso'=>false, 'messagem'=>"O nó '".$noInsercao->getStringNo()."' da linha'".$noInsercao->getLinhaNo()."' já foi fechado"]: ['sucesso'=>false, 'messagem'=>"O nó '".$noInsercao->getStringNo()."' da linha'".$noInsercao->getLinhaNo()."' não é nó folha"];

                }
            }
        }
        else{
            if(count($ListanosFolha)>count($ListanoInsercao)){
                return ['sucesso'=>false, 'messagem'=>"Existe mais de um nó valido para inseção"];
            }
            else{
                return ['sucesso'=>false, 'messagem'=>"Algum dos nos de insersção nao é válido"];
            }
        }
        #---------------------





        #Verifica sê os nos de inserção pertencem ao mesmo ramo
        if(!$this->isDecendente($noDerivacao,$noInsercao)){
            return ['sucesso'=>false, 'messagem'=>'Os nós não pertencem ao mesmo ramo'];
        }
        #-------------


        if($noDerivacao->isUtilizado()==true){
            return ['sucesso'=>false, 'messagem'=>'Este argumento já foi derivado'];
        }





            if(($noDerivacao->getValorNo()->getTipoPredicado()=='PREMISSA' OR $noDerivacao->getValorNo()->getTipoPredicado()=='CONCLUSAO' OR $noDerivacao->getValorNo()->getTipoPredicado()=='PREDICATIVO')and $noDerivacao->getValorNo()->getNegadoPredicado()<2){

                    return ['sucesso'=>false, 'messagem'=>'Não existe derivação para este argumento'];


            }
            elseif($noDerivacao->getValorNo()->getNegadoPredicado()>=2  and $regra==1){
                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos =$this->regras->DuplaNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    $this->criarNo($nosFolha,$arvore,$array_filhos,$noDerivacao->getLinhaNo());
                }

                return ['sucesso'=>true, 'messagem'=>'Negação_Dupla','arv'=>$arvore];
            }
            elseif($noDerivacao->getValorNo()->getTipoPredicado()=='CONJUNCAO' and $noDerivacao->getValorNo()->getNegadoPredicado()==0 and $regra==2){
                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos = $this->regras->conjuncao($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    $this->criarNoSemBifucacao($nosFolha,$arvore,$array_filhos,$noDerivacao->getLinhaNo());
                }
                return ['sucesso'=>true, 'messagem'=>'Conjunção','arv'=>$arvore];
            }
            elseif ($noDerivacao->getValorNo()->getTipoPredicado()== 'DISJUNCAO' and $noDerivacao->getValorNo()->getNegadoPredicado()==1  and $regra==3){
                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos = $this->regras->disjuncaoNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    $this->criarNoSemBifucacao($nosFolha,$arvore,$array_filhos,$noDerivacao->getLinhaNo());

                }

                return ['sucesso'=>true, 'messagem'=>'Negação_Disjunção','arv'=>$arvore];
            }
            elseif ($noDerivacao->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $noDerivacao->getValorNo()->getNegadoPredicado()==1  and $regra==4) {
                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos = $this->regras->condicionalNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    $this->criarNoSemBifucacao($nosFolha,$arvore,$array_filhos,$noDerivacao->getLinhaNo());
                }

                return ['sucesso'=>true, 'messagem'=>'Negacão_Condicional','arv'=>$arvore];
            }
            elseif($noDerivacao->getValorNo()->getTipoPredicado()=='DISJUNCAO' and $noDerivacao->getValorNo()->getNegadoPredicado()==0  and $regra==5){
                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos = $this->regras->disjuncao($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    $this->criarNoBifurcado($nosFolha,$arvore,$array_filhos,$noDerivacao->getLinhaNo());
                }

                 return ['sucesso'=>true, 'messagem'=>'Disjunção','arv'=>$arvore];
            }
            elseif ($noDerivacao->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $noDerivacao->getValorNo()->getNegadoPredicado()==0  and $regra==6){

                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos = $this->regras->condicional($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    $this->criarNoBifurcado($nosFolha,$arvore,$array_filhos,$noDerivacao->getLinhaNo());
                }
                return ['sucesso'=>true, 'messagem'=>'Condicional','arv'=>$arvore];
            }
            elseif ($noDerivacao->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $noDerivacao->getValorNo()->getNegadoPredicado()==0  and $regra==7){
                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos = $this->regras->bicondicional($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    $this->criarNoBifurcadoDuplo($nosFolha,$arvore,$array_filhos,$noDerivacao->getLinhaNo());
                }

                 return ['sucesso'=>true, 'messagem'=>'Bicondicional','arv'=>$arvore];
            }
            elseif ($noDerivacao->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $noDerivacao->getValorNo()->getNegadoPredicado()==1  and $regra==8){
                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos = $this->regras->conjuncaoNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    $this->criarNoBifurcado($nosFolha,$arvore,$array_filhos,$noDerivacao->getLinhaNo());
                }

                return ['sucesso'=>true, 'messagem'=>'Negação_Conjunção','arv'=>$arvore];
            }
            elseif ($noDerivacao->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $noDerivacao->getValorNo()->getNegadoPredicado()==1  and $regra==9){
                foreach ($ListanosFolha as $nosFolha){
                    $array_filhos = $this->regras->bicondicionalNeg($noDerivacao->getValorNo());
                    $noDerivacao->utilizado(true);
                    $this->criarNoBifurcadoDuplo($nosFolha,$arvore,$array_filhos,$noDerivacao->getLinhaNo());
                }
                 return ['sucesso'=>true, 'messagem'=>'Negação_Bicondicional','arv'=>$arvore];
                }

            return ['sucesso'=>false, 'messagem'=>'Regra Invalida'];

     }

     public function gerarArvorePassoPasso($arvore,$listaDerivacoes){
        foreach ($listaDerivacoes as $derivacao){


            $ListanoInsercao =[]; // lista de nós folha no qual o nó derivado deve ser inserido
            foreach ($derivacao['insercao'] as $no){
                array_push($ListanoInsercao,$this->getNoPeloId($arvore, $no['idNo']));
            }
            $noDerivacao=$this->getNoPeloId($arvore, $derivacao['derivacao']);




            $no=null;
            $valido=true;
            foreach ($ListanoInsercao as $noInsercao){
                $noDescendente =$this->isDecendente($noDerivacao,$noInsercao);
                if($noDescendente==false){
                    $valido=false;
                }
            }
            if($valido==true){
                $no=$noDerivacao;
            }




            if($derivacao['regra']==9){
                foreach ($ListanoInsercao as $nosFolha){
                    $array_filhos = $this->regras->bicondicionalNeg($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNoBifurcadoDuplo($nosFolha,$arvore,$array_filhos,$no->getLinhaNo());
                }

            }
            elseif($derivacao['regra']==8){
                foreach ($ListanoInsercao as $nosFolha){
                    $array_filhos = $this->regras->conjuncaoNeg($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNoBifurcado($nosFolha,$arvore,$array_filhos,$no->getLinhaNo());
                }

            }
            elseif($derivacao['regra']==7){
                foreach ($ListanoInsercao as $nosFolha){
                    $array_filhos = $this->regras->bicondicional($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNoBifurcadoDuplo($nosFolha,$arvore,$array_filhos,$no->getLinhaNo());
                }

            }
            elseif($derivacao['regra']==6){
                foreach ($ListanoInsercao as $nosFolha){
                    $array_filhos = $this->regras->condicional($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNoBifurcado($nosFolha,$arvore,$array_filhos,$no->getLinhaNo());
                }

            }
            elseif($derivacao['regra']==5){
                foreach ($ListanoInsercao as $nosFolha){
                    $array_filhos = $this->regras->disjuncao($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNoBifurcado($nosFolha,$arvore,$array_filhos,$no->getLinhaNo());
                }

            }
            elseif($derivacao['regra']==4){
                foreach ($ListanoInsercao as $nosFolha){
                    $array_filhos = $this->regras->condicionalNeg($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNoSemBifucacao($nosFolha,$arvore,$array_filhos,$no->getLinhaNo());
                }

            }
            elseif($derivacao['regra']==3){
                foreach ($ListanoInsercao as $nosFolha){
                    $array_filhos = $this->regras->disjuncaoNeg($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNoSemBifucacao($nosFolha,$arvore,$array_filhos,$no->getLinhaNo());
                }

            }
            elseif($derivacao['regra']==2){
                foreach ($ListanoInsercao as $nosFolha){
                    $array_filhos = $this->regras->conjuncao($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNoSemBifucacao($nosFolha,$arvore,$array_filhos,$no->getLinhaNo());
                }

            }
            else{
                foreach ($ListanoInsercao as $nosFolha){
                    $array_filhos =$this->regras->DuplaNeg($no->getValorNo());
                    $no->utilizado(true);
                    $this->criarNo($nosFolha,$arvore, $array_filhos,$no->getLinhaNo());
                }


            }


        }
        return $arvore;


    }




    public function inicializarPassoPasso($listaArgumentos,$id,$lista, $negacao){
        $ultimoNo=null;
        $resposta=null;

        foreach ($lista as $no){

            $resposta = $this->criarNoIncializacao($listaArgumentos,$no['idNo'],$no['negacao'], $ultimoNo);
            if($resposta['sucesso']==false){
                return ['sucesso'=>false, 'messagem'=>$resposta['messagem']];
            }
            $ultimoNo= $resposta==null?null:$resposta['ultimoNo'];
        }
        $ultimoNo= $resposta==null?null:$resposta['ultimoNo'];


        if($id==null){
            return ['sucesso'=>true, 'arv'=>$this->arvore, 'lista'=>$lista];
        }

        $result = $this->criarNoIncializacao($listaArgumentos,$id,$negacao, $ultimoNo);
        array_push($lista,['idNo'=>$id,'negacao'=>$negacao]);
        if ($result['sucesso']==false){
            return ['sucesso'=>false, 'messagem'=>$result['messagem']];

        }else{
            return ['sucesso'=>true, 'arv'=>$this->arvore, 'lista'=>$lista] ;
        }






    }

    public function criarNoIncializacao($listaArgumentos,$id,$negacao,$ultimoNo){
        $identi= str_split ($id,strrpos($id, "_"));
        if($identi[0]=='premissa' && $negacao==false){
            $premissa = $listaArgumentos['premissas'][substr($identi[1], 1)];

            if ($this->arvore==null){
                $this->idNo+=1;
                $this->arvore = new No($this->idNo,$premissa->getValorObjPremissa(),null,null,null,1,null,null,false,false);
                $ultimoNo=$this->arvore;
            }
            else{
                $this->idNo+=1;
                $ultimoNo->setFilhoCentroNo(new No($this->idNo,$premissa->getValorObjPremissa(),null,null,null,$this->getUltimaLinha(),null,null,false,false));
                $ultimoNo=$ultimoNo->getFilhoCentroNo();
            }
            $this->addLinha();

            return ['sucesso'=>true, 'ultimoNo'=>$ultimoNo];
        }
        else if($identi[0]=='conclusao' && $negacao==true){

            $conclusao = $listaArgumentos['conclusao'][substr($identi[1], 1)];
            $conclusao->getValorObjConclusao()->addNegacaoPredicado();
            if ($this->arvore==null){
                $this->idNo+=1;
                $this->arvore= (new No($this->idNo,$conclusao->getValorObjConclusao(),null,null,null,1,null,null,false,false));
                $ultimoNo=$this->arvore;
            }else{
                $this->idNo+=1;
                $ultimoNo->setFilhoCentroNo(new No($this->idNo,$conclusao->getValorObjConclusao(),null,null,null,$this->getUltimaLinha(),null,null,false,false));
                $ultimoNo=$ultimoNo->getFilhoCentroNo();
            }
            $this->addLinha();
            return ['sucesso'=>true, 'ultimoNo'=>$ultimoNo];
        }else{
            if($identi[0]=='premissa' && $negacao==true){
            return ['sucesso'=>false, 'messagem'=>'Atenção!! Esto é uma premissa!', 'ultimoNo'=>''];
            }
            if($identi[0]=='conclusao' && $negacao==false){
                return ['sucesso'=>false, 'messagem'=>'Atenção!! Esto é uma conclusão!', 'ultimoNo'=>''];
            }

            return ['sucesso'=>false, 'messagem'=>'Atenção!!', 'ultimoNo'=>''];


        }
    }

    public function ticarNo($arvore, $no){
        $noTicado = $this->getNoPeloId($arvore,$no['idNo']); // o nó a ser ticado
        if(($noTicado->getValorNo()->getTipoPredicado()=='PREMISSA' OR $noTicado->getValorNo()->getTipoPredicado()=='CONCLUSAO' OR $noTicado->getValorNo()->getTipoPredicado()=='PREDICATIVO')and $noTicado->getValorNo()->getNegadoPredicado()<2){

            return ['sucesso'=>false, 'messagem'=>'Este argumento não pode ser ticado, pois não existe derivação'];
        }
        else{
            if($noTicado->isUtilizado()==true){
                if($noTicado->isTicado()==true){
                    return ['sucesso'=>false, 'messagem'=>'Este nó já foi ticado'];
                }
                else{
                    $noTicado->ticarNo();
                    return ['sucesso'=>true, 'arv'=>$arvore,];
                }

            }else{
                return ['sucesso'=>false, 'messagem'=>'Este nó ainda não foi deriavado'];
            }
        }
    }

    public function ticarTodosNos($arvore, $listaNo){
        foreach( $listaNo as $no){
            $noTicado = $this->getNoPeloId($arvore,$no['idNo']); // o nó a ser ticado

            if(($noTicado->getValorNo()->getTipoPredicado()=='PREMISSA' OR $noTicado->getValorNo()->getTipoPredicado()=='CONCLUSAO' OR $noTicado->getValorNo()->getTipoPredicado()=='PREDICATIVO')and $noTicado->getValorNo()->getNegadoPredicado()<2){

                return ['sucesso'=>false, 'messagem'=>"Não existe derivação para o argumento'". $noTicado->getStringNo()."' da linha'".$noTicado->getLinhaNo()."'"];
            }
            else{
                if($noTicado->isUtilizado()==true){
                    if($noTicado->isTicado()==true){
                        return ['sucesso'=>false, 'messagem'=>"O nó '". $noTicado->getStringNo()."' da linha '".$noTicado->getLinhaNo()."' já foi ticado"];
                    }
                    else{
                        $noTicado->ticarNo();
                    }

                }else{
                    return ['sucesso'=>false, 'messagem'=>"O nó '". $noTicado->getStringNo()."' da linha '".$noTicado->getLinhaNo()." ainda não foi deriavado"];
                }
            }
        }
        return ['sucesso'=>true, 'arv'=>$arvore];
    }



    public function fecharTodosNos($arvore, $listaNo){
        foreach( $listaNo as $no){
            $noFolha = $this->getNoPeloId($arvore,$no['nofechado']['idNo']); // o nó a ser fechado
            $noContradicao = $this->getNoPeloId($arvore,$no['noContradicao']['idNo']);


            $descendente=$this->isDecendente($noContradicao,$noFolha);
            if($descendente==true){

                if ($noContradicao->getValorNo()->getValorPredicado() == $noFolha->getValorNo()->getValorPredicado()){

                    $negacaoContradicao = $noContradicao->getValorNo()->getNegadoPredicado();
                    $negacaoFolha = $noFolha->getValorNo()->getNegadoPredicado();

                    if ($negacaoContradicao == 1 and $negacaoFolha==0){

                        $noFolha->fechamentoNo();

                    }
                    elseif ($negacaoContradicao == 0 and $negacaoFolha==1) {

                        $noFolha->fechamentoNo();

                    }
                    else{
                        return ['sucesso'=>false, 'messagem'=>'Os argumentos iguais mas não contraditórios'];
                    }

               }
                else{
                    return ['sucesso'=>false, 'messagem'=>'Os argumentos não são iguais'];
               }

            }
            else{
                return ['sucesso'=>false, 'messagem'=>'O nó não pertence ao mesmo ramo'];
            }
        }
        return ['sucesso'=>true, 'messagem'=>'', 'arv'=>$arvore];

    }


    public function fecharNo($arvore, $folha, $contradicao){
        $noContradicao = $this->getNoPeloId($arvore,$contradicao['idNo']);
        $noFolha = $this->getNoPeloId($arvore,$folha['idNo']);
        $descendente=$this->isDecendente($noContradicao,$noFolha);
        if($descendente==true){

            if ($noContradicao->getValorNo()->getValorPredicado() == $noFolha->getValorNo()->getValorPredicado()){

                $negacaoContradicao = $noContradicao->getValorNo()->getNegadoPredicado();
                $negacaoFolha = $noFolha->getValorNo()->getNegadoPredicado();

                if ($negacaoContradicao == 1 and $negacaoFolha==0){
                    if ($noFolha->isFechamento()){
                        return ['sucesso'=>false, 'messagem'=>'O ramo já foi fechado'];
                    }
                    $noFolha->fechamentoNo();
                    return ['sucesso'=>true, 'messagem'=>'', 'arv'=>$arvore];
                }
                elseif ($negacaoContradicao == 0 and $negacaoFolha==1) {
                    if ($noFolha->isFechamento()){
                        return ['sucesso'=>false, 'messagem'=>'O ramo já foi fechado'];
                    }
                    $noFolha->fechamentoNo();
                    return ['sucesso'=>true, 'messagem'=>'', 'arv'=>$arvore];

                }
                else{
                    return ['sucesso'=>false, 'messagem'=>'Os argumentos iguais mas não contraditórios'];
                }

           }
            else{
                return ['sucesso'=>false, 'messagem'=>'Os argumentos não são iguais'];
           }

        }
        else{
            return ['sucesso'=>false, 'messagem'=>'O nó não pertence ao mesmo ramo'];
        }

    }


    /*esta funçao recebe com parametro a arvore atual, e retorna uma array com a referencia de todos os nós folhas que não foram fechados pelo usuario*/
    public function getNosFolhasAberto($arvore, $ListaDeNo=null){


        if ($arvore->getFilhoDireitaNo() ==null and  $arvore->getFilhoEsquerdaNo() ==null and  $arvore->getFilhoCentroNo() ==null  and ($arvore->isFechado()==true && $arvore->isFechamento()==false)){
            $ListaDeNo[] =  $arvore;
            return  $ListaDeNo;
        }
        else {
            if ($arvore->getFilhoCentroNo()!=null){

                $ListaDeNo = $this->getNosFolhasAberto($arvore->getFilhoCentroNo(),$ListaDeNo);
            }
            if ($arvore->getFilhoEsquerdaNo()!=null){
                $ListaDeNo = $this->getNosFolhasAberto($arvore->getFilhoEsquerdaNo(),$ListaDeNo);
            }
            if ($arvore->getFilhoDireitaNo()!=null ){
                $ListaDeNo = $this->getNosFolhasAberto($arvore->getFilhoDireitaNo(),$ListaDeNo);
            }
            return $ListaDeNo;
        }
    }





}
