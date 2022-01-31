<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Models\Country;
use App\Models\State;

class CountryController extends Controller
{
       
    public function index()
    {
        try {
            $countries=Country::orderBy('name')->get();
            return response()->json([
                    'countries' => $countries
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Store a newly created country in storage.
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
                    'message' => 'País registrado exitosamente',
                    'country' => $country
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
        $country = Country::find($id);
        
        if($country){
            return response()->json([
                    'success' => true,
                    'country' => $country
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified country in storage.
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
                    'message' => 'País actualizado exitosamente',
                    'country' => $country
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified country from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $country = Country::find($id);
            $country->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'País eliminado exitosamente'
            ], 200);

        } catch (Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function states($id){
        $country = Country::findOrfail($id);
        
        return response()->json([
                'success' => true,
                'states' => $country->states()->get()
            ], 200);

    }

}
