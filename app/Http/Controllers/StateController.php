<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Models\Country;
use App\Models\State;

class StateController extends Controller
{
       
    public function index()
    {
        try {
            $states=State::orderBy('name')->get();
            return response()->json([
                    'states' => $states
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Store a newly created state in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //
            return response()->json([
                    'success' => true,
                    'message' => 'Estado registrado exitosamente',
                    'state' => $state
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
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
        $state = State::find($id);
        
        if($state){
            return response()->json([
                    'success' => true,
                    'state' => $state
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified state in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            //
            return response()->json([
                    'success' => true,
                    'message' => 'Estado actualizado exitosamente',
                    'state' => $state
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified state from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $state = State::find($id);
            $state->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Estado eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
