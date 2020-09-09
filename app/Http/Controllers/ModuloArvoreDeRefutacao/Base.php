<?php

namespace App\Http\Controllers\ModuloArvoreDeRefutacao;
use Illuminate\Http\Request;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Argumento;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore\Gerador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Construcao;
use App\Http\Controllers\Controller;

class Base extends Controller
{
        /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    function __construct() {
      $this->arg = new Argumento;
      $this->gerador = new Gerador;
      $this->constr = new Construcao;


}
    #Carrega as formular e exibe a pagina inicial
    public function Index(){
        $listaFormulas=$this->constr->stringXmlDiretorio();
        return view('arvore',['listaFormulas'=> $listaFormulas, 'formulaGerada'=> 'Nenhuma Fórmula Carregada...']);

    }

    #Salva na pasta public o Arquivo XML
   public function SalvarXml(Request $request){
    if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()){
        $dir=dirname(__FILE__,4.).'\storage\app\public\formulas';
        $diretorio = scandir($dir);
        $num = count($diretorio) - 1;
        $request->file('arquivo')->storeAs('formulas', 'formula-'.$num.'.xml');
        return $this->Index();
    }

  }

    #Busca o XML, e gera a arvore Otimizada
    public function CriarArvoreOtimizada(Request $request){
        # Busca XML no diretorio
        $id = $request->all()['idFormula'];
        $dir=dirname(__FILE__,4.).'\storage\app\public\formulas';
        $xml = simplexml_load_file($dir.'\formula-'.$id.'.xml');
        #--------

        #Cria a arvore passando o XML
        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
        $arv =  $this->gerador->arvoreOtimizada($arvore);
        #--------

        #Gera lista das possicoes de cada no da tabela
        $impresaoAvr = $this->constr->geraListaArvore($arv,700,350,0);
        #--------=>$str, 'posX'=>$posX, 'posY'=>$posYFilho,]


        #Gera uma string da Formula XML
        $formulaGerada = $this->arg->stringFormula($xml);
        #--------

        #Gera lista das arvores para exibir na tabela
        $listaFormulas=$this->constr->stringXmlDiretorio();
        #--------

        return view('arvoreotimizada',['arv'=>$impresaoAvr,'listaFormulas'=> $listaFormulas, 'formulaGerada'=> $formulaGerada]);
    }

    #Carrega pagina inicia de resolucao por etapa
    public function PorEtapa(Request $request){
        #Gera lista das arvores para exibir na tabela
        $listaFormulas=$this->constr->stringXmlDiretorio();
        #--------

        return view('porEtapa.baseEtapa',['listaFormulas'=> $listaFormulas, 'formulaGerada'=> 'Nenhuma Fórmula Carregada...']);
    }


    #Inicializa o processo de criacao por etapa
    public function Inicializando(Request $request){

        $formulario =$request->all();

        # Busca XML no diretorio
        $idFormula = $formulario['idFormula'];
        $dir=dirname(__FILE__,4.).'\storage\app\public\formulas';
        $xml = simplexml_load_file($dir.'\formula-'.$idFormula.'.xml');
        #-----

        #Cria a arvore passando o XML
        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
        #-----

        $listaDerivacoes="{}";


        #Gera lista das possicoes de cada no da arvore
        $impresaoAvr = $this->constr->geraListaArvore($arvore,700,350,0);
        #-----

        #Gera uma string da Formula XML
        $formulaGerada = $this->arg->stringFormula($xml);
        #-----

        #Gera lista das formulas para exibir na tabela
        $listaFormulas=$this->constr->stringXmlDiretorio();
        #-----

        #Gera array com tres alternativas senda 1 valida e 2 invalidas
        $regras=$this->gerador->arrayPerguntas($arvore);

    
        #-----
        $proximoNoInsercao=$this->gerador->proximoNoParaInsercao( $arvore);
  
       

        return view('porEtapa.arvorePorEtapa',['arv'=>$impresaoAvr,'listaFormulas'=> $listaFormulas, 'formulaGerada'=> $formulaGerada, 'regras'=>$regras, 'listaDerivacoes'=>$listaDerivacoes, 'idFormula'=>$idFormula, 'proximoNoInsercao'=>$proximoNoInsercao,'modal'=>['sucesso'=>false,'messagem'=>'']]);
    }

    public function ValidaResposta(Request $request) {

        #pega Itens do formulario
        $formulario = $request->all();
        #-----

        #Inializa a arvore
        $dir=dirname(__FILE__,4.).'\storage\app\public\formulas';
        $xml = simplexml_load_file($dir.'\formula-'.$formulario['idFormula'].'.xml');
        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
        #-----

         #transforma o json em array
         $listaDerivacoes=$formulario['derivacoes'];
      
         $listaDerivacoes=json_decode($listaDerivacoes,true);
         #-----

        #Reconstroi a arvore
        $arvorePasso = $this->gerador->gerarArvorePassoPasso($arvore,$listaDerivacoes);
        #-----

        
        #Deriva a tentativa atual, caso erro retorna a mensagem
        $arvoreFinal =$this->gerador->derivar($arvorePasso, $formulario['linha'],$formulario['regra']);

        

        if($arvoreFinal['sucesso']==false){


            $modal = ['sucesso'=>true,'messagem'=>"Linha:".$formulario['linha']." - ".$arvoreFinal['messagem']];

            $proximoNoInsercao=$this->gerador->proximoNoParaInsercao( $arvorePasso);
            $impresaoAvr = $this->constr->geraListaArvore($arvorePasso,700,350,0);
            $formulaGerada = $this->arg->stringFormula($xml);
            $listaFormulas=$this->constr->stringXmlDiretorio();
            $listaDerivacoes =json_encode ($listaDerivacoes);
            $regras=$this->gerador->arrayPerguntas( $arvorePasso);
        }
        else{
            $proximoNoInsercao=$this->gerador->proximoNoParaInsercao($arvoreFinal['arv']);
            $impresaoAvr = $this->constr->geraListaArvore($arvoreFinal['arv'],700,350,0);

            $formulaGerada = $this->arg->stringFormula($xml);

            $listaFormulas=$this->constr->stringXmlDiretorio();

            array_push( $listaDerivacoes, ['linha'=>$formulario['linha'],'regra'=>$formulario['regra']]);
            $listaDerivacoes =json_encode ($listaDerivacoes);
            $regras=$this->gerador->arrayPerguntas($arvoreFinal['arv']);
            $modal = ['sucesso'=>false,'messagem'=>''];

            
    
            
             }
        return view('porEtapa.arvorePorEtapa',['arv'=>$impresaoAvr,'listaFormulas'=> $listaFormulas, 'formulaGerada'=> $formulaGerada, 'regras'=>$regras, 'listaDerivacoes'=> $listaDerivacoes, 'idFormula'=>$formulario['idFormula'], 'proximoNoInsercao'=>$proximoNoInsercao, 'modal'=>$modal]);

    }





}
