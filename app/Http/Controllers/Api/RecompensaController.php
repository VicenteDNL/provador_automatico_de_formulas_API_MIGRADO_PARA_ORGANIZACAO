<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Recompensa;
use Illuminate\Http\Request;

class RecompensaController extends Controller
{
    private $recompensa;

    public function __construct(Recompensa $recompensa )
    {
        $this->$recompensa = $recompensa; 
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
}
