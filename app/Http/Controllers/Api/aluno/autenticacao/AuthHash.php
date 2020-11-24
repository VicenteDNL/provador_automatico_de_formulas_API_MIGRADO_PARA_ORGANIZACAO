<?php

namespace App\Http\Controllers\Api\aluno\autenticacao;

use App\Http\Controllers\Controller;
use App\Jogador;
use Illuminate\Http\Request;
use App\Http\Controllers\LogicLive\modulos\Jogador as ModulosJogador;

class AuthHash extends Controller
{

    public function __construct()
    {
        $this->logicLive_jogador =  new ModulosJogador;
    }

    public function hash(Request $request ){

        $criadoLogicLive = $this->logicLive_jogador->getJogador($request->usu_hash);
        if($criadoLogicLive['success']==false){
            return response()->json(['success' => false, 'msg'=>'hash inválido', 'data'=>'']);
        }
        $jogador_cadastrado = Jogador::where('id_logic_live',$criadoLogicLive['data']['jog_codigo'])->first(); 
      
        if($jogador_cadastrado==null){
            $jogador_cadastrado= new Jogador();
            $jogador_cadastrado->nome=$criadoLogicLive['data']['jog_nome'];
            $jogador_cadastrado->usunome=$criadoLogicLive['data']['jog_nome'];
            $jogador_cadastrado->email=$criadoLogicLive['data']['jog_email'];
            $jogador_cadastrado->avatar=$criadoLogicLive['data']['jog_avatar'];
            $jogador_cadastrado->token=$request->usu_hash;
            $jogador_cadastrado->ativo=$criadoLogicLive['data']['jog_ativo'];
            $jogador_cadastrado->provedor=$criadoLogicLive['data']['jog_provedor'];
            $jogador_cadastrado->id_logic_live=$criadoLogicLive['data']['jog_codigo'];
            $jogador_cadastrado->save();
        }
        else{
            $jogador_cadastrado->nome=$criadoLogicLive['data']['jog_nome'];
            $jogador_cadastrado->usunome=$criadoLogicLive['data']['jog_nome'];
            $jogador_cadastrado->email=$criadoLogicLive['data']['jog_email'];
            $jogador_cadastrado->avatar=$criadoLogicLive['data']['jog_avatar'];
            $jogador_cadastrado->token=$request->usu_hash;
            $jogador_cadastrado->ativo=$criadoLogicLive['data']['jog_ativo'];
            $jogador_cadastrado->provedor=$criadoLogicLive['data']['jog_provedor'];
            $jogador_cadastrado->id_logic_live=$criadoLogicLive['data']['jog_codigo'];
            $jogador_cadastrado->save();
        }

        return response()->json(['success' => true, 'msg'=>'hash válido!', 'data'=>'']);

    }
}
