<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Models\State;
use File;
use Storage;


class UserController extends Controller
{
       
    public function index()
    {
        try {
            $users=User::orderBy('name')->get();
            return response()->json([
                    'users' => $users
                ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Store a newly created user in storage.
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
                    'message' => 'Usuario registrado exitosamente',
                    'user' => $user
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
        $user = User::find($id);
        
        if($user){
            return response()->json([
                    'success' => true,
                    'user' => $user
                ], 200);

        }else{
            return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);                
        }            
    }
   
   /**
     * Update the specified user in storage.
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
                    'user' => $user
                ], 200);
            
        } catch (Exception $e) {
            
            return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            
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
        $user = User::findOrfail($id);
        
        return response()->json([
                'success' => true,
                'states' => $user->states()->get()
            ], 200);

    }


    public function signature(Request $request, $id){
        try {
            $user=User::findOrFail($id);
            $file = $request->signature;        
            if (File::exists($file)){
                if($user->signature){
                    if($user->subscriber_id){
                        Storage::delete($user->subscriber_id.'/users/'.$user->signature);
                        Storage::delete($user->subscriber_id.'/users/thumbs/'.$user->signature);
                    }else{
                        Storage::delete('/users/'.$user->signature);
                        Storage::delete('/users/thumbs/'.$user->signature);
                    }
                }
                $user->signature_name = $file->getClientOriginalName();
                $user->signature_type = $file->getClientOriginalExtension();
                $user->signature_size = $file->getSize();
                $path=($user->subscriber_id)?$user->subscriber_id.'/users/':'/users/';
                $user->signature=$this->upload_file($path, $file);
            }
            $user->save();

            return response()->json([
                'success' => true,
                'url' => $user->url_signature
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }    
    }

    public function avatar(Request $request, $id){
        try {
            $user=User::findOrFail($id);
            $file = $request->avatar;        
            if (File::exists($file)){
                if($user->avatar){
                    if($user->subscriber_id){
                        Storage::delete($user->subscriber_id.'/users/'.$user->avatar);
                        Storage::delete($user->subscriber_id.'/users/thumbs/'.$user->avatar);
                    }else{
                        Storage::delete('/users/'.$user->avatar);
                        Storage::delete('/users/thumbs/'.$user->avatar);
                    }
                }
                $user->avatar_name = $file->getClientOriginalName();
                $user->avatar_type = $file->getClientOriginalExtension();
                $user->avatar_size = $file->getSize();
                $path=($user->subscriber_id)?$user->subscriber_id.'/users/':'/users/';
                $user->avatar=$this->upload_file($path, $file);
            }
            $user->save();

            return response()->json([
                'success' => true,
                'url' => $user->url_avatar
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }    
    }

}
