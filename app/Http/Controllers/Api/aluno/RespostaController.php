<?php

namespace App\Http\Controllers\Api\aluno;

use App\Http\Controllers\Controller;
use App\Resposta;
use Illuminate\Http\Request;

class RespostaController extends Controller
{
    

    public function criarResposta($jogador, $exercicio){

        $resposta = Resposta::where('id_jogador', '=',$jogador->id)
                    ->where('id_exercicio','=',$exercicio->id)->get();
        if(count($resposta)==1 && $resposta[0]->concluida==true ){
            return ['success'=>false ,'msg'=>"Exercicio já respondido", 'data'=>$resposta];

            
        }
        $resposta = new Resposta;
        $resposta->id_jogador= $jogador->id;
        $resposta->id_exercicio= $exercicio->id;
        $resposta->ativa =true;
        $resposta->tentativas_invalidas =0;
        $resposta->tempo=0;
        $resposta->concluida=false;
        $resposta->save();
        return ['success'=>true ,'msg'=>"Erro ao criar resposta", 'data'=>$resposta];
    }


}
