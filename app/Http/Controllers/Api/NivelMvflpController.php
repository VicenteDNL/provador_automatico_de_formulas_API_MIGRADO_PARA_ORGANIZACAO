<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\NivelMVFLP;
use Illuminate\Http\Request;

class NivelMvflpController extends Controller
{
    private $niveis;

    public function __construct(NivelMVFLP $niveis )
    {
        $this->niveis = $niveis; 
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
            return response()->json(['success' => false, 'msg'=>$e, 'data'=>''],500);

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
        $nivelMVFLP->nome = $request->nome;
        $nivelMVFLP->descricao = $request->descricao;
        $nivelMVFLP->ativo = $request->ativo;
        $nivelMVFLP->id_recompensa = $request->id_recompensa;
        $nivelMVFLP->save();
        return response()->json(['success' => true, 'msg'=>'Niviel ('.$request->nome.') cadastrado com sucesso', 'data'=>'']);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>$e, 'data'=>''],500);

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
            $nivelMVFLP->update($request->all());
            $nivelMVFLP->save();
            return response()->json(['success' => true, 'msg'=>'Niviel ('.$request->nome.') atualizado com sucesso', 'data'=>''], 200);
        
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>$e, 'data'=>''],500);
        }
       
    }


    public function all(){
        try{
            $nivelMVFLP = NivelMVFLP::all();
            return response()->json(['success' => true, 'msg'=>'', 'data'=>$nivelMVFLP]);
        
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
            $nivelMVFLP = NivelMVFLP::findOrFail($id);
            $nivelMVFLP->delete();
            return response()->json(['success' => true, 'msg'=>'Niviel ('.$nivelMVFLP->nome.') deletado com sucesso', 'data'=>''], 200);
        
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>$e, 'data'=>''],500);
        }
    
    }
}
