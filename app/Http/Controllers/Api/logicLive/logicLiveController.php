<?php

namespace App\Http\Controllers\Api\logicLive;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\config\Configuracao;
use App\Http\Controllers\LogicLive\config\Game;
use App\LogicLive;
use Illuminate\Http\Request;

class logicLiveController extends Controller
{

    private $logicLive;

    function __construct(LogicLive $logicLive) {
        $this->config = new Configuracao;
        $this->game = new Game;
        $this->logicLive =$logicLive;

  }

    public function infoModulosEndGame(){
    
        try{
            // Busca os dados do game da plataforma Logic Live
            $game = $this->game->getGame();
            if( $game['success']==false){
                return response()->json(['success' => false, 'msg'=>$game['success'], 'data'=>''],500);
            }
            $game =  $game['data'];
            
            // Busca os dados dos modulos da plataforma Logic Live
            $modulo =  $this->game->getModulo();
            if( $modulo['success']==false){
                return response()->json(['success' => false, 'msg'=>$modulo['success'], 'data'=>''],500);
            }else{
                $modulo =  $modulo['data'];
            }

           
            $resposta=false;
            if( count($modulo)==3 ){
                $resposta=true;

                // Verifica se o game da base de dados é o mesmo da API do Logic Live
                $baseDados = LogicLive::where('tipo', '=', 'game')->get();
                if($baseDados[0]->meu_id!=$game['gam_codigo']){
                    return response()->json(['success' => false, 'msg'=>'Conflito na base de dados', 'data'=>''],500);
                }
                // ----------
                
                // Verifica se o modulo1 da base de dados é o mesmo da API do Logic Live
                $baseDados = LogicLive::where('tipo', '=', 'modulo1')->get();
                $modulo1 =  $this->game->getModuloId($baseDados[0]->meu_id);
                if( $modulo1['success']==false){
                    return response()->json(['success' => false, 'msg'=>$modulo1['msg'], 'data'=>''],500);
                }

                // Verifica se o  modulo2 da base de dados é o mesmo da API do Logic Live
                $baseDados = LogicLive::where('tipo', '=', 'modulo2')->get();
                $modulo2 =  $this->game->getModuloId($baseDados[0]->meu_id);
                if( $modulo2['success']==false){
                    return response()->json(['success' => false, 'msg'=>$modulo2['msg'], 'data'=>''],500);
                }
                // ----------
                
                // Verifica se o  modulo3 da base de dados é o mesmo da API do Logic Live
                $baseDados = LogicLive::where('tipo', '=', 'modulo3')->get();
                $modulo3 =  $this->game->getModuloId($baseDados[0]->meu_id);
                if( $modulo3['success']==false){
                    return response()->json(['success' => false, 'msg'=>$modulo3['msg'], 'data'=>''],500);
                }
                // ----------
            
                return response()->json(['success' => true, 'msg'=>'', 'data'=>['game'=> $game, 'modulos'=>[$modulo1,$modulo2,$modulo3],'cadastrados'=>$resposta]]);
            }
            elseif (count($game)==0 && count($modulo)==0){
                return response()->json(['success' => true, 'msg'=>'', 'data'=>['game'=> $game, 'modulos'=>$modulo,'cadastrados'=>$resposta]]);
            }
            else{
                return response()->json(['success' => false, 'msg'=>'Conflito na base de dados', 'data'=>''],500);
            }
            
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'Erro Interno', 'data'=>''],500);

        }

    }

    public function criarModulosEndGame(){

            $gameBase = LogicLive::where('tipo', '=', 'game')->get();
            $gameApi = $this->game->getGame()['data'];
            $moduloBase = LogicLive::where('tipo', '=', ['modulo1','modulo2','modulo3'])->get();
            $moduloApi = $this->game->getGame()['data'];


            if(count($gameBase)==0 && count($gameApi)==0 && count($moduloBase)==0 && count($moduloApi)==0){
                
                $gameModulos = $this->game->criarGameEndModulos();


                if($gameModulos['success']){
                    
                    $gameCriado =$gameModulos['data']['game'];
                    $modulosCriado =$gameModulos['data']['modulos'];

                    // Criando Game
                    $logicLive_game = new LogicLive(); 
                    $logicLive_game->tipo='game';
                    $logicLive_game->meu_id=$gameCriado['gam_codigo'];
                    $logicLive_game->nome=$gameCriado['gam_nome'];
                    $logicLive_game->descricao=$gameCriado['gam_descricao'];
                    $logicLive_game->ativo=$gameCriado['gam_ativo'];
                    $logicLive_game->save();

                    // Criando Modulo 1
                    $logicLive_modulo1 = new LogicLive(); 
                    $logicLive_modulo1->tipo='modulo1';
                    $logicLive_modulo1->meu_id=$modulosCriado[0]['mod_codigo'];
                    $logicLive_modulo1->game_id=$modulosCriado[0]['gam_codigo'];
                    $logicLive_modulo1->hash=$modulosCriado[0]['mod_hash'];
                    $logicLive_modulo1->nome=$modulosCriado[0]['mod_nome'];
                    $logicLive_modulo1->descricao=$modulosCriado[0]['mod_descricao'];
                    $logicLive_modulo1->ativo=$modulosCriado[0]['mod_ativo'];
                    $logicLive_modulo1->save();

                    // Criando Modulo 2
                    $logicLive_modulo2 = new LogicLive(); 
                    $logicLive_modulo2->tipo='modulo2';
                    $logicLive_modulo2->meu_id=$modulosCriado[1]['mod_codigo'];
                    $logicLive_modulo2->game_id=$modulosCriado[1]['gam_codigo'];
                    $logicLive_modulo2->hash=$modulosCriado[1]['mod_hash'];
                    $logicLive_modulo2->nome=$modulosCriado[1]['mod_nome'];
                    $logicLive_modulo2->descricao=$modulosCriado[1]['mod_descricao'];
                    $logicLive_modulo2->ativo=$modulosCriado[1]['mod_ativo'];
                    $logicLive_modulo2->save();

                    // Criando Modulo 3
                    $logicLive_modulo3 = new LogicLive(); 
                    $logicLive_modulo3->tipo='modulo3';
                    $logicLive_modulo3->meu_id=$modulosCriado[2]['mod_codigo'];
                    $logicLive_modulo3->game_id=$modulosCriado[2]['gam_codigo'];
                    $logicLive_modulo3->hash=$modulosCriado[2]['mod_hash'];
                    $logicLive_modulo3->nome=$modulosCriado[2]['mod_nome'];
                    $logicLive_modulo3->descricao=$modulosCriado[2]['mod_descricao'];
                    $logicLive_modulo3->ativo=$modulosCriado[2]['mod_ativo'];
                    $logicLive_modulo3->save();


                    return response()->json(['success' => true, 'msg'=>'criado com sucesso!', 'data'=>['game'=> $gameCriado, 'modulos'=>$modulosCriado,'cadastrados'=>true]]);

                }
                else{
                    return response()->json(['success' => false, 'msg'=>$gameModulos['msg'], 'data'=>'']);  
                }
                
            }
            else{
                return response()->json(['success' => false, 'msg'=>'Conflito na base de dados', 'data'=>''],500); 
            }
    }
}
