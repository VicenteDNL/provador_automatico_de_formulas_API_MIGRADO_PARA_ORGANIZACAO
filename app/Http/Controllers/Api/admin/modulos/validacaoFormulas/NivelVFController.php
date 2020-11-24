<?php

namespace App\Http\Controllers\Api\admin\modulos\validacaoFormulas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\config\Configuracao;
use App\Http\Controllers\LogicLive\modulos\validacaoFormulas\NivelVF;
use App\LogicLive;
use App\NivelMVFLP;
use Illuminate\Http\Request;

class NivelVFController extends Controller
{
    private $niveis;

    public function __construct(NivelMVFLP $niveis )
    {
        $this->niveis = $niveis; 
        $this->config = new Configuracao;
        $this->logicLive_nivel= new NivelVF;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
        $data= $this->niveis->orderBy('created_at', 'desc')->paginate(5);
        return response()->json(['success' => true, 'msg'=>'', 'data'=>$data]);
        
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'erro no servidor', 'data'=>''],500);

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, NivelMVFLP $nivelMVFLP)
    {
        try{
            if($this->config->ativo()){
                $baseDados = LogicLive::where('tipo', '=', 'modulo1')->get();
                $baseDados = $baseDados[0];
                $criadoLogicLive = $this->logicLive_nivel->criarNivel(['mod_codigo'=>$baseDados->meu_id,'niv_nome'=>$request->nome,'niv_descricao'=>$request->descricao,'niv_ativo'=>$request->ativo,]);

                if($criadoLogicLive['success']=false){
                    return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
                }

                $nivelMVFLP->meu_id_logic_live = $criadoLogicLive['data']['niv_codigo'];
            }
            $nivelMVFLP->id_modulo = $baseDados->meu_id;
            $nivelMVFLP->nome = $request->nome;
            $nivelMVFLP->descricao = $request->descricao;
            $nivelMVFLP->ativo = $request->ativo;
            $nivelMVFLP->id_recompensa = $request->id_recompensa;
            $nivelMVFLP->save();
        return response()->json(['success' => true, 'msg'=>'nivel deletado do banco de dados', 'data'=>'']);
    
    }catch(\Exception $e){
        return response()->json(['success' => false, 'msg'=>'erro no servidor', 'data'=>''],500);
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {

        return response()->json(['success' => true, 'msg'=>'', 'data'=>$this->niveis->find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        try{

            

            $nivelMVFLP = NivelMVFLP::findOrFail($id);

            if($this->config->ativo()){
                $criadoLogicLive = $this->logicLive_nivel->atualizarNivel($nivelMVFLP->meu_id_logic_live,['niv_nome'=>$request->nome,'niv_descricao'=>$request->descricao,'niv_ativo'=>$request->ativo,'mod_codigo'=>$nivelMVFLP->id_modulo]);
                if($criadoLogicLive['success']==false){
                   
                    return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
                }

            }
     
            $nivelMVFLP->update($request->all());
            $nivelMVFLP->save();
            return response()->json(['success' => true, 'msg'=>'nivel atualizado do banco de dados', 'data'=>''], 200);
        
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'erro no servidor', 'data'=>''],500);
        }
       
    }


    public function all(){
        try{
            $nivelMVFLP = NivelMVFLP::all();
            return response()->json(['success' => true, 'msg'=>'', 'data'=>$nivelMVFLP]);
        
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'erro no servidor', 'data'=>''],500);
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

            if($this->config->ativo()){
                $nivelMVFLP = NivelMVFLP::findOrFail($id);
                $criadoLogicLive = $this->logicLive_nivel->deletarNivel($nivelMVFLP->meu_id_logic_live);
                if($criadoLogicLive['success']==false){
                   
                    return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
                }
            }

            $nivelMVFLP = NivelMVFLP::findOrFail($id);
            $nivelMVFLP->delete();
            return response()->json(['success' => true, 'msg'=>'nivel deletado do banco de dados', 'data'=>''], 200);
        
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'erro no servidor', 'data'=>''],500);
        }
    
    }
}
