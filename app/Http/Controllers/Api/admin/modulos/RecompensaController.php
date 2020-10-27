<?php

namespace App\Http\Controllers\Api\admin\modulos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\config\Configuracao;
use App\Http\Controllers\LogicLive\modulos\Recompensa as ModulosRecompensa;
use App\Recompensa;
use Illuminate\Http\Request;

class RecompensaController extends Controller
{

    public function __construct(Recompensa $recompensa )
    {
        $this->$recompensa = $recompensa; 
        $this->logicLive_recompensa= new ModulosRecompensa;
        $this->config = new Configuracao;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $data= Recompensa::all();
            return response()->json(['success' => true, 'msg'=>'', 'data'=>$data]);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>$e, 'data'=>''],500);

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Recompensa $recompensa)
    {
        try{

            if($this->config->ativo()){
                $criadoLogicLive = $this->logicLive_recompensa->criarRecompensa(['rec_nome'=>$request->nome, 'rec_imagem'=>'nada sendo passado', 'rec_pontuacao'=>$request->pontuacao]);
                if($criadoLogicLive['success']=false){
                    return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
                }
            }
                $recompensa->nome = $request->nome;
                $recompensa->imagem = 'nada sendo passado';
                $recompensa->pontuacao = $request->pontuacao;
                $recompensa->id_logic_live = $criadoLogicLive['data']['rec_codigo'];
                $recompensa->save();
          
            return response()->json(['success' => true, 'msg'=>'Niviel ('.$request->nome.') cadastrado com sucesso', 'data'=>'']);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'Error interno', 'data'=>''],500);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $recompensa = Recompensa::findOrFail($id);

            if($this->config->ativo()){
                $criadoLogicLive = $this->logicLive_recompensa->atualizarRecompensa($recompensa->id_logic_live,['rec_nome'=>$request->nome, 'rec_imagem'=>'nada sendo passado', 'rec_pontuacao'=>$request->pontuacao]);
                if($criadoLogicLive['success']==false){
                   
                    return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
                }
            }
     
            $recompensa->update($request->all());
            $recompensa->save();
            return response()->json(['success' => true, 'msg'=>'Recompensa ('.$request->nome.') atualizado com sucesso', 'data'=>''], 200);
        
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>$e, 'data'=>''],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $recompensa = Recompensa::findOrFail($id);
            if($this->config->ativo()){
                $criadoLogicLive = $this->logicLive_recompensa->deletarRecompensa($recompensa->id_logic_live);
                if($criadoLogicLive['success']==false){
                    return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
                }
            }
            $recompensa->delete();
            return response()->json(['success' => true, 'msg'=>'Niviel ('.$recompensa->nome.') deletado com sucesso', 'data'=>''], 200);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>$e, 'data'=>''],500);
        }
     
    }
}
