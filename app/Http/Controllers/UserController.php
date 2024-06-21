<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
class AuthController extends Controller
{
    //Regresar todos los usuarios
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], Response::HTTP_OK);
    }

    public function show($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json(['user' => $user], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Validate the request data for update
        $request->validate([
            'name' => ['string'],
            'email' => ['email', Rule::unique('users')->ignore($user->id)],
            'password' => ['confirmed'],
        ]);

        // Update user data
        $user->fill($request->only(['name', 'email', 'image', 'phoneNumber', 'rol']));

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        //Guardar al usuario actualizado
        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return response()->json(['message' => 'Usuario eliminado exitosamente'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }
    }
}
