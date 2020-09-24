<?php

namespace App\Http\Controllers\Api;

use App\ExercicioMVFLP;
use App\Formula;
use App\Http\Controllers\Controller;
use App\NivelMVFLP;
use App\Recompensa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExercicioMvflpController extends Controller
{

    private $exercicio;

    public function __construct(ExercicioMVFLP $exercicio )
    {
        $this->exercicio = $exercicio; 
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ExercicioMVFLP $exercicio)
    {
        try{
            // Verifica sê existe uma recompensa com o id da requisicao
            $recompensas=Recompensa::where('id', $request->id_recompensa)->get(); 
            if(count($recompensas)==0){return response()->json(['success' => false, 'msg'=>'Recompensa não cadastrada', 'data'=>''],500);}

            // Verifica sê existe um nivel com o id da requisicao
            $nivel=NivelMVFLP::where('id',$request->id_nivel['id'])->get(); 
            if(count($nivel)==0){return response()->json(['success' => false, 'msg'=>'Nivel não cadastrado', 'data'=>''],500);}

            $exercicio->id_recompensa=$request->id_recompensa['id'];
            $exercicio->id_nivel=$request->id_nivel['id'];
            $exercicio->nome=$request->nome;
            $exercicio->enunciado=$request->enunciado;
        
            $exercicio->tempo=$request->tempo;
            
            $exercicio->descricao=$request->descricao;
            $exercicio->ativo=$request->ativo;

            $exercicio->qndt_erros=$request->qndt_erros;
            $exercicio->hash='';
            $exercicio->url='';

            $formula = new Formula();
            $formula->formula =$request->id_formula["formula"];
            $formula->xml =$request->id_formula["xml"];
            $formula->quantidade_regras =$request->id_formula["quantidade_regras"];
            $formula->ticar_automaticamente =$request->id_formula["ticar_automaticamente"];
            $formula->fechar_automaticamente =$request->id_formula["fechar_automaticamente"];
            $formula->iniciar_zerada =$request->id_formula["iniciar_zerada"];
            $formula->inicio_personalizado =$request->id_formula["inicio_personalizado"];
            if($request->id_formula["inicio_personalizado"]==true && $request->id_formula["iniciar_zerada"]==false ){
                $formula->lista_passos =json_encode ($request->id_formula["lista_passos"]);
                $formula->lista_derivacoes =json_encode ($request->id_formula["lista_derivacoes"]);
                $formula->lista_ticagem =json_encode ($request->id_formula["lista_ticagem"]);
                $formula->lista_fechamento =json_encode ($request->id_formula["lista_fechamento"]);
            }

            $formula->save();
            $exercicio->id_formula=$formula->id;
            $exercicio->save();
            return response()->json(['success' => true, 'msg'=>'Cadastrado!', 'data'=>'']);
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
        //
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
            $exercicio = ExercicioMVFLP::findOrFail($id);

            $formula= Formula::findOrFail($exercicio->id_formula); 
            $exercicio->delete();
            $formula->delete();
            return response()->json(['success' => true, 'msg'=>'Niviel ('.$exercicio->nome.') deletado com sucesso', 'data'=>''], 200);
        
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>$e, 'data'=>''],500);
        }
    
    }


    public function byIdNivel($id){
        try{

            $nivelMVFLP = NivelMVFLP::find($id);
            if($nivelMVFLP==null){
                return response()->json(['success' => false, 'msg'=>'Nivel não encontrado', 'data'=>'']);
            }

            $exercicios = ExercicioMVFLP::where('id_nivel',$nivelMVFLP->id)->paginate(5);
            return response()->json(['success' => true, 'msg'=>'', 'data'=>$exercicios]);
        
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>$e, 'data'=>''],500);
        }
    }
}
