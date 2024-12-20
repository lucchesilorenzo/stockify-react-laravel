<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  /**
   * Handle the registration of a new user.
   *
   * @param Request $request
   * @return void
   */
  public function signUp(Request $request)
  {
    // Validation
    $rules = Validator::make($request->all(), ([
      'first_name' => 'required|string|max:20',
      'last_name' => 'required|string|max:20',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|string|max:20',
      'confirm_password' => 'required|string|max:20|same:password',
    ]));

    // Check if validation fails
    if ($rules->fails()) {
      return response()->json(['message' => 'Invalid credentials.'], 400);
    }

    // Get validated data
    $validatedData = $rules->validated();

    // Hash password
    $password = bcrypt($validatedData['password']);

    try {
      // Create user
      $newUser = [
        'first_name' => $validatedData['first_name'],
        'last_name' => $validatedData['last_name'],
        'email' => $validatedData['email'],
        'password' => $password,
      ];

      // Store user in database
      $user = User::create($newUser);

      // Generate Sanctum token
      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json(['token' => $token], 201);
    } catch (\Throwable $e) {
      return response()->json(['message' => 'Could not create user.'], 500);
    }
  }

  /**
   * Handle the login of a user.
   *
   * @param Request $request
   * @return void
   */
  public function logIn(Request $request)
  {
    // Validation
    $rules = Validator::make($request->all(), ([
      'email' => 'required|email',
      'password' => 'required',
    ]));

    // Check if validation fails
    if ($rules->fails()) {
      return response()->json(['message' => 'Invalid credentials.'], 400);
    }

    // Get validated data
    $validatedData = $rules->validated();

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
   * @param Request $request
   * @return void
   */
  public function logOut(Request $request)
  {
    $tokens = $request->user()->tokens;

    foreach ($tokens as $token) {
      $token->delete();
    }

    return response()->json(['message' => 'Logged out.'], 200);
  }
}
