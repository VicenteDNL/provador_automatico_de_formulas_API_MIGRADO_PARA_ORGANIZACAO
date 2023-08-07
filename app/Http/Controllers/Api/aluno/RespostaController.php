<?php

namespace App\Http\Controllers\Api\aluno;

use App\ExercicioMVFLP;
use App\Formula;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\modulos\Jogador as ModulosJogador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Base;
use App\Jogador;
use App\Recompensa;
use App\Resposta;
use Illuminate\Http\Request;

class RespostaController extends Controller
{
    private $logicLive_jogador;
    
    public function __construct()
    {

        $this->logicLive_jogador =  new ModulosJogador;
 
 
    }
    

    public function criarResposta($jogador, $exercicio){

        $recompensa = Recompensa::where('id', '=',$exercicio->id_recompensa)->first();

        $resposta = Resposta::where('id_jogador', '=',$jogador->id)->where('id_exercicio','=',$exercicio->id)->get();
        
        if(count($resposta)==1  ){
            
            if( $resposta[0]->concluida==true){
                return ['success'=>false ,'msg'=>"Exercicio já respondido", 'data'=>$resposta[0]];
            }
            return ['success'=>true ,'msg'=>"", 'data'=>$resposta[0], 'novo'=>false];

        }
        $resposta = new Resposta;
        $resposta->id_jogador= $jogador->id;
        $resposta->id_exercicio= $exercicio->id;
        $resposta->ativa =true;
        $resposta->tentativas_invalidas =0;
        $resposta->tempo=date("Y-m-d H:i:s");
        $resposta->concluida=false;
        $resposta->pontuacao=$recompensa->pontuacao;
        $resposta->repeticao=0;
        $resposta->save();
        return ['success'=>true ,'msg'=>"", 'data'=>$resposta, 'novo'=>true];
    }



    public function deletarResposta($id,Request $request){

        if(!isset($request->usu_hash)){
            return response()->json(['success' => false, 'msg'=>'hash jogador não informado!', 'data'=>''],500);
        }

        $criadoLogicLive = $this->logicLive_jogador->getJogador($request->usu_hash);
        $jogador_cadastrado = Jogador::where('id_logic_live',$criadoLogicLive['data']['jog_codigo'])->get();
        $exercicio = ExercicioMVFLP::findOrFail($id); 

       
        $resposta = Resposta::where('id_jogador', '=',$jogador_cadastrado[0]->id) ->where('id_exercicio','=',$exercicio->id)->get();
        
        if(count($resposta)!=1 ){
            return response()->json(['success'=>false ,'msg'=>"Resposta não encontrada", 'data'=>''],500);
        }
        
        if ($resposta[0]->concluida==true){
            return response()->json(['success'=>false ,'msg'=>"Exercicio concluido", 'data'=>'']);
        }

        $resposta[0]->tentativas_invalidas=0;
        $resposta[0]->tempo=date("Y-m-d H:i:s");
        $resposta[0]->repeticao=$resposta[0]->repeticao+1;
        $resposta[0]->save();


        $formula =  Formula::findOrFail($exercicio->id_formula);

        $arvore = new Base($formula->xml);
        // $arvore->setListaPassos( json_decode ($formula->lista_passos,true));
        $arvore->setListaPassos( $formula->lista_passos==[] ? [] :json_decode ($formula->lista_passos,true));
        $arvore->setListaTicagem($formula->lista_ticagem==[] ? [] : json_decode ($formula->lista_ticagem,true));
        $arvore->setListaFechamento( $formula->lista_fechamento==[]?[] : json_decode ($formula->lista_fechamento,true));
        $arvore->derivacao->setListaDerivacoes($formula->lista_derivacoes==[] ? [] : json_decode ($formula->lista_derivacoes,true));
        $arvore->fecharAutomatido($formula->fechar_automaticamente);
        $arvore->ticarAutomatico($formula->ticar_automaticamente);
        $arvore->inicializacao->setFinalizado($formula->inicializacao_completa);

        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>'Error ar criar arvore', 'data'=>''],500);
        }

        return response()->json([
            'success'=>true ,
            'msg'=>"Resposta deletada", 
            'data'=>[
                'arvore'=>$arvore->retorno($exercicio->id,$request->usu_hash, $request->exe_hash),
                'tentativas'=>$this->validaResposta($resposta[0],$exercicio, 'busca',true)
                ]
            ]);
       
       
       
    }

    public function validaResposta(Resposta $resposta, ExercicioMVFLP $exercicio,$tipo='buscar', $busca=false){
        $recompensa ='';
        return [
            'tempo'=>$this->tempo($resposta,$exercicio), 
            'erros'=> $busca==true? $this->buscaErros($resposta,$exercicio) :$this->erros($resposta,$exercicio, $recompensa),
            'pontuacao'=> $this->pontuacao($resposta,$exercicio,$tipo)
            // $busca==true?$this->buscaPontuacao($resposta,$exercicio,$tipo):
        ];
       

    }


    private function pontuacao($resposta,$exercicio,$tipo){
     
    
        /**
         * Estipula uma porcentagem para a quantidade maxima
         * que o valor possa ser subtraido para cada uma das 3
         * tentativas mapeadas
         */
        $porcentagem_sub_maxima_1 = 0.30;
        $porcentagem_sub_maxima_2 = 0.40;
        $porcentagem_sub_maxima_3 = 0.50;


        $recompensa = Recompensa ::where('id',$exercicio->id_recompensa)->first();

        if($exercicio->qndt_erros==null ){
            if($tipo=='responder'){

                $pont=floor($resposta->pontuacao/2);

                if($pont<=0){
                    $resposta->pontuacao=0;
                }
                else{
                    $resposta->pontuacao= $pont;
                }
        
                $resposta->save();
                return ['ponto'=>$resposta->pontuacao, 'maximo'=>$recompensa->pontuacao];

            }
            return ['ponto'=>$resposta->pontuacao, 'tentativa'=>$resposta->repeticao];
        }
        
        if($resposta->repeticao==0){
            $recompensa_maxima = $recompensa->pontuacao;
            $porcentagem_sub_ativa = $porcentagem_sub_maxima_1;

        }
        elseif($resposta->repeticao==1){
            $recompensa_minina_1 = $recompensa->pontuacao - ($recompensa->pontuacao*$porcentagem_sub_maxima_1);
            $recompensa_maxima = $recompensa_minina_1; 
            $porcentagem_sub_ativa = $porcentagem_sub_maxima_2;
        } 
        else{
            $recompensa_minina_1 = $recompensa->pontuacao - ($recompensa->pontuacao*$porcentagem_sub_maxima_1);
            $recompensa_minina_2 =$recompensa_minina_1- ($recompensa_minina_1*$porcentagem_sub_maxima_2);
            $recompensa_maxima = $recompensa_minina_2;
            $porcentagem_sub_ativa = $porcentagem_sub_maxima_3;
            

        }
        
        switch ($tipo){
            case 'adicionar':
                // Estipula a porcentagem maxima que uma tentativa invalida pode subtrair da pontuacao a cada vez
                 $porcentagem_adicionar = ($porcentagem_sub_ativa /$exercicio->qndt_erros)/2; 
                 $erros = $recompensa_maxima - $resposta->pontuacao; 
                 $nova_recompensa =$recompensa_maxima -floor ((( $recompensa_maxima*$porcentagem_adicionar))+$erros); 
                break;
            case 'fechar':
                // Estipula a porcentagem maxima que uma tentativa invalida pode subtrair da pontuacao a cada vez
                $porcentagem_fechar= $porcentagem_sub_ativa /$exercicio->qndt_erros;
                $erros = $recompensa_maxima - $resposta->pontuacao; 
                $nova_recompensa = $recompensa_maxima -floor ((($recompensa_maxima*$porcentagem_fechar))+$erros); 
                break;
            case 'ticar':
                // Estipula a porcentagem maxima que uma tentativa invalida pode subtrair da pontuacao a cada vez
                $porcentagem_ticar = ($porcentagem_sub_ativa /$exercicio->qndt_erros)/2;
                $erros = $recompensa_maxima - $resposta->pontuacao; 
                $nova_recompensa = $recompensa_maxima -floor ((($recompensa_maxima*$porcentagem_ticar))+$erros); 

                break;
            case 'derivar':
                // Estipula a porcentagem maxima que uma tentativa invalida pode subtrair da pontuacao a cada vez
                $porcentagem_derivar = $porcentagem_sub_ativa /$exercicio->qndt_erros; 
                $erros = $recompensa_maxima - $resposta->pontuacao; 
                $nova_recompensa = $recompensa_maxima -floor ((($recompensa_maxima*$porcentagem_derivar))+$erros); 
                break;

            case 'buscar':
                if($recompensa_maxima>=$resposta->pontuacao){
                    $nova_recompensa = $resposta->pontuacao; 
                }
                else{
                    $nova_recompensa = $recompensa_maxima;
                }
                break;
            default:
                if($recompensa_maxima>=$resposta->pontuacao){
                    $nova_recompensa = $resposta->pontuacao; 
                }
                else{
                    $nova_recompensa = $recompensa_maxima;
                }
        }

        if($nova_recompensa<=0){
            $resposta->pontuacao=0;
        }
        else{
            $resposta->pontuacao= $nova_recompensa;
        }

        $resposta->save();
        return ['ponto'=>$resposta->pontuacao, 'maximo'=>$recompensa->pontuacao];

    }

    private function erros($resposta,$exercicio, $recompensa){

        if($exercicio->qndt_erros==null){
            return null;
        }
  
        // verifica a quantidade de tentativas invalidas
        $resposta->tentativas_invalidas = $resposta->tentativas_invalidas + 1;
        $restantes = ($exercicio->qndt_erros) - ($resposta->tentativas_invalidas) ;
        if($restantes<0){
            $saida['tempo']=0;
        }
        $resposta->save();
        return $restantes;
        
    }

    public function buscaErros(Resposta $resposta, ExercicioMVFLP $exercicio){
        if($exercicio->qndt_erros==null){
            return null;
        }

        return ($exercicio->qndt_erros) - ($resposta->tentativas_invalidas);
    
    }



    private function tempo($resposta,$exercicio){

        if($exercicio->tempo==null){
           return  ['minutos'=>null,'segundos'=>null];

        }
        $tempo= $exercicio->tempo *60;
        $inicio= strtotime($resposta->tempo)+$tempo;
        $atual = strtotime(date("Y-m-d H:i:s"));

        if($inicio>$atual){
            $minu = floor(($inicio - $atual)/60);
            $segundos = round(round((($inicio - $atual)/60)-$minu,2)*60);
            return ['minutos'=>$minu,'segundos'=>$segundos]
           ;
        }
        elseif($inicio<$atual){
            return ['minutos'=>0,'segundos'=>$resposta->repeticao];


        }
        return null;
    }



    public function tempoParaResposta($resposta,$exercicio){

        if($exercicio->tempo==null){
           return  null;

        }
        $tempo= $exercicio->tempo *60;
        $inicio= strtotime($resposta->tempo)+$tempo;
        $atual = strtotime(date("Y-m-d H:i:s"));

        if($inicio>$atual){
            $segundos = $inicio-$atual;

            return $segundos;
           ;
        }
        elseif($inicio<$atual){
            return null;


        }
        return null;
    }



    public function buscaTempo($resposta,$exercicio){

        if($exercicio->tempo==null){
            return null;
         }

        if($exercicio->tempo>0){
            $minu = floor($resposta->tempo);
            $segundos = round(round(($resposta->tempo)-$minu,2)*60);
            return ['minutos'=> $minu,'segundos'=>$segundos];

        }
        return ['minutos'=> 0,'segundos'=>0];

    }


}
