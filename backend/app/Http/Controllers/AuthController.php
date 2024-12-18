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
    $validated = Validator::make($request->all(), ([
      'firstName' => 'required|string|min:1|max:20',
      'lastName' => 'required|string|min:1|max:20',
      'email' => 'required|email|unique:users',
      'password' => 'required|string|min:8|max:20',
      'confirmPassword' => 'required|string|min:8|max:20|same:password',
    ]));

    // Check if validation fails
    if ($validated->fails()) {
      return response()->json(['message' => 'Invalid credentials.'], 400);
    }

    // Get validated data
    $validatedData = $validated->validated();

    // Hash password
    $password = bcrypt($validatedData['password']);

    try {
      // Create user
      $newUser = [
        'firstName' => $validatedData['firstName'],
        'lastName' => $validatedData['lastName'],
        'email' => $validatedData['email'],
        'password' => $password,
      ];

      // Store user in database
      $user = User::create($newUser);

      // Generate Sanctum token
      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json(['token' => $token], 201);
    } catch (\Throwable $e) {
      return response()->json([
        'message' => 'Could not create user.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Handle the login of a user.
   *
   * @param Request $request
   * @return void
   */
  public function login(Request $request)
  {
    // Validation
    $validated = Validator::make($request->all(), ([
      'email' => 'required|email',
      'password' => 'required',
    ]));

    // Check if validation fails
    if ($validated->fails()) {
      return response()->json(['message' => 'Invalid credentials.'], 400);
    }

    // Get validated data
    $validatedData = $validated->validated();

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
      return response()->json([
        'message' => 'Could not login user.',
        'error' => $e->getMessage()
      ], 500);
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
