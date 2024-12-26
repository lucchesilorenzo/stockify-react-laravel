<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogInRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  /**
   * Handle the signup of a user.
   *
   * @param SignUpRequest $request
   * @return JsonResponse
   */
  public function signUp(SignUpRequest $request): JsonResponse
  {
    // Get validated data
    $validatedData = $request->validated();

    try {
      // Create user
      $user = User::create([
        'first_name' => $validatedData['first_name'],
        'last_name' => $validatedData['last_name'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
      ]);

      // Generate Sanctum token
      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json(['token' => $token], 201);
    } catch (QueryException $e) {
      if ($e->getCode() === 23000) {
        return response()->json(['message' => 'User already exists.'], 400);
      }
      return response()->json(['message' => 'Could not create user.'], 500);
    }
  }

  /**
   * Handle the login of a user.
   *
   * @param LogInRequest $request
   * @return JsonResponse
   */
  public function logIn(LogInRequest $request): JsonResponse
  {
    // Get validated data
    $validatedData = $request->validated();

    try {
      // Check if user exists and password is correct
      $user = User::where('email', $validatedData['email'])->first();
      if (!$user || !Hash::check($validatedData['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials.'], 401);
      }

      // Generate Sanctum token
      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json(['token' => $token], 200);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Could not login user.'], 500);
    }
  }

  /**
   * Handle the logout of a user.
   *
   * @param  Request  $request
   * @return JsonResource
   */
  public function logOut(): JsonResponse
  {
    auth()->user()->tokens()->delete();

    return response()->json(['message' => 'Logged out.'], 200);
  }
}
