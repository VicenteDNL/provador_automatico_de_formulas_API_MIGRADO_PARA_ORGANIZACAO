<?php

namespace App\Http\Controllers\Api;

use App\ExercicioMVFLP;
use App\Http\Controllers\Controller;
use App\NivelMVFLP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExercicioMvflpController extends Controller
{
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
    public function store(Request $request)
    {
        //
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
        //
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
