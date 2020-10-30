<?php

namespace App\Http\Controllers\Api\aluno;

use App\ExercicioMVFLP;
use App\Http\Controllers\Controller;
use App\Resposta;
use Illuminate\Http\Request;

class RespostaController extends Controller
{
    

    public function criarResposta($jogador, $exercicio){

        $resposta = Resposta::where('id_jogador', '=',$jogador->id)
                    ->where('id_exercicio','=',$exercicio->id)->get();
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
        $resposta->tempo=0;
        $resposta->concluida=false;
        $resposta->save();
        return ['success'=>true ,'msg'=>"Erro ao criar resposta", 'data'=>$resposta, 'novo'=>true];
    }



    public function validaResposta(Resposta $resposta, ExercicioMVFLP $exercicio){
        if($exercicio->qndt_erros==null){
            return null;
        }

        $resposta->tentativas_invalidas = $resposta->tentativas_invalidas + 1;
        $restantes = ($exercicio->qndt_erros) - ($resposta->tentativas_invalidas) ;
        if($restantes<0){
            return 0;   
        }
        $resposta->save();
        return $restantes;
        
    }




    public function buscaResposta(Resposta $resposta, ExercicioMVFLP $exercicio){
  
        return ($exercicio->qndt_erros) - ($resposta->tentativas_invalidas);
    
    }


    public function deletarResposta($jogador, $exercicio){

        $resposta = Resposta::where('id_jogador', '=',$jogador->id)
                    ->where('id_exercicio','=',$exercicio->id)->get();
        if(count($resposta)!=1 ){
            return ['success'=>false ,'msg'=>"Resposta não encontrada", 'data'=>''];
        }
        
        if ($resposta[0]->concluida==true){
            return ['success'=>false ,'msg'=>"Exercicio concluido", 'data'=>''];
        }
        $resposta[0]->tentativas_invalidas=0;
        $resposta[0]->save();
        return ['success'=>true ,'msg'=>"Resposta deletada", 'data'=>$resposta[0]];
    }


}
