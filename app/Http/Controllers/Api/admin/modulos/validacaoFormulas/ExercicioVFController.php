<?php

namespace App\Http\Controllers\Api\admin\modulos\validacaoFormulas;

use App\ExercicioMVFLP;
use App\Formula;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\config\Configuracao;
use App\Http\Controllers\LogicLive\modulos\validacaoFormulas\ExercicioVF;
use App\NivelMVFLP;
use App\Recompensa;
use App\Resposta;
use Illuminate\Http\Request;


class ExercicioVFController extends Controller
{

    public function __construct(ExercicioMVFLP $exercicio )
    {
        $this->exercicio = $exercicio;
        $this->config = new Configuracao;
        $this->logicLive_exercicio =  new ExercicioVF;
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
            if(count($recompensas)==0){return response()->json(['success' => false, 'msg'=>'recompensa informada nao foi encontrada', 'data'=>''],500);}

            // Verifica sê existe um nivel com o id da requisicao
            $nivel=NivelMVFLP::where('id',$request->id_nivel['id'])->get();
            if(count($nivel)==0){return response()->json(['success' => false, 'msg'=>'nivel informado nao foi encontrado', 'data'=>''],500);}

            $widthcanvas ="";

            $formula = new Formula();
            $formula->formula =$request->id_formula["formula"];
            $formula->xml =$request->id_formula["xml"];
            $formula->quantidade_regras =$request->id_formula["quantidade_regras"];
            $formula->ticar_automaticamente =$request->id_formula["ticar_automaticamente"];
            $formula->fechar_automaticamente =$request->id_formula["fechar_automaticamente"];
            $formula->iniciar_zerada =$request->id_formula["iniciar_zerada"];
            $formula->inicio_personalizado =$request->id_formula["inicio_personalizado"];
            $formula->inicializacao_completa =$request->id_formula["inicializacao_completa"];
            $formula->save();
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
            $exercicio->id_formula=$formula->id;
            $exercicio->save();
            $exercicio->url=$this->config->urlExercicioValidacao().$exercicio->id;

            if($request->id_formula["inicio_personalizado"]==true && $request->id_formula["iniciar_zerada"]==false ){
                $formula->lista_passos =json_encode ($request->id_formula["lista_passos"]);
                $formula->lista_derivacoes =json_encode ($request->id_formula["lista_derivacoes"]);
                $formula->lista_ticagem =json_encode ($request->id_formula["lista_ticagem"]);
                $formula->lista_fechamento =json_encode ($request->id_formula["lista_fechamento"]);
                $formula->save();
            }

            if($this->config->ativo()){
                $criadoLogicLive = $this->logicLive_exercicio->criarExercicio([
                    'rec_codigo'=>$recompensas[0]->id_logic_live,
                    'niv_codigo'=>$nivel[0]->meu_id_logic_live,
                    'exe_tempoexecucao'=> $request->tempo,
                    'exe_link'=>$exercicio->url,
                    'exe_nome'=>$request->nome,
                    'exe_descricao'=>$request->descricao,
                    'exe_ativo'=>$request->ativo
                ]);
                if($criadoLogicLive['success']==false){
                    return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
                    $exercicio->delete();
                    $formula->delete();
                }
                $exercicio->hash= $criadoLogicLive['data']['exe_hash'];
                $exercicio->id_logic_live= $criadoLogicLive['data']['exe_codigo'];
            }

            $exercicio->save();

            return response()->json(['success' => true, 'msg'=>'exercicio criado no banco de dados', 'data'=>'']);
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
    public function show($id)
    {
        $exercicio = $this->exercicio->find($id);
        $exercicio->id_formula = Formula::findOrFail($exercicio->id_formula);
        $exercicio->id_recompensa = Recompensa::findOrFail($exercicio->id_recompensa);
        return response()->json(['success' => true, 'msg'=>'', 'data'=> $exercicio]);
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
        // try{

            $exercicio = ExercicioMVFLP::findOrFail($id);
            $exercicio->update($request->all());
            $exercicio->save();


            if($this->config->ativo()){
                $criadoLogicLive = $this->logicLive_exercicio->atualizarExercicio($exercicio->id_logic_live,[
                    'rec_codigo'=>Recompensa::findOrFail($exercicio->id_recompensa)->id_logic_live,
                    'niv_codigo'=>NivelMVFLP::findOrFail($exercicio->id_nivel)->meu_id_logic_live,
                    'exe_tempoexecucao'=> 111,
                    'exe_link'=>$exercicio->url,
                    'exe_nome'=>$exercicio->nome,
                    'exe_descricao'=>$exercicio->descricao,
                    'exe_ativo'=>$exercicio->ativo
                    ]);
                // return  response()->json($exercicio->meu_id_logic_live);
                if($criadoLogicLive['success']==false){

                    return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
                }

            }

            return response()->json(['success' => true, 'msg'=>'exercicio atualizado no banco de dados', 'data'=>''], 200);



        // }catch(\Exception $e){
        //     return response()->json(['success' => false, 'msg'=>'erro no servidor', 'data'=>''],500);
        // }

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

            // Caso exista uma resposta para o exercicio não deixa ele ser excluido
            if(count(Resposta::where('id_exercicio','=', $exercicio->id)->get())!=0){
                return response()->json(['success' => false, 'msg'=>'existe resposta para esse exercicio', 'data'=>''],500);
            }

            if($this->config->ativo()){
                $criadoLogicLive = $this->logicLive_exercicio->deletarExercicio($exercicio->id_logic_live);
                if($criadoLogicLive['success']==false){

                    return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
                }
            }

            $formula->delete();
            $exercicio->delete();

            return response()->json(['success' => true, 'msg'=>'exercicio deletado do banco de dados', 'data'=>''], 200);

        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'erro no servidor', 'data'=>''],500);
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
