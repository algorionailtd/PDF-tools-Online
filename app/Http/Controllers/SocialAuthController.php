<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SocialAuthController extends Controller
{
    public function googleLogin(Request $request)
    {
        try {
            $request->validate([
                'idToken' => [
                    'required',
                    'string',
                ],
            ]);

            $firebaseApiKey = env('FIREBASE_WEB_API_KEY');

            if (empty($firebaseApiKey)) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'FIREBASE_WEB_API_KEY is missing from .env',
                ], 500);
            }

            $idToken = $request->input('idToken');

            $firebaseUrl =
                'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key='
                . $firebaseApiKey;

            $firebaseResponse = Http::timeout(20)
                ->acceptJson()
                ->asJson()
                ->post(
                    $firebaseUrl,
                    [
                        'idToken' => $idToken,
                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | IMPORTANT: Show Exact Firebase Error
            |--------------------------------------------------------------------------
            */

            if ($firebaseResponse->failed()) {
                $status = $firebaseResponse->status();

                $body = $firebaseResponse->body();

                $firebaseError = $firebaseResponse->json();

                Log::error(
                    'Firebase token verification failed.',
                    [
                        'status' => $status,
                        'body' => $body,
                        'json' => $firebaseError,
                    ]
                );

                return response()->json([
                    'success' => false,

                    'message' =>
                        'Firebase token verification failed.',

                    'firebase_status' =>
                        $status,

                    'firebase_response' =>
                        $body,

                    'firebase_error' =>
                        $firebaseError,
                ], 401);
            }

            $firebaseData = $firebaseResponse->json();

            $firebaseUser =
                $firebaseData['users'][0]
                ?? null;

            if (! $firebaseUser) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Firebase user was not returned.',
                    'firebase_response' =>
                        $firebaseData,
                ], 401);
            }

            $email =
                $firebaseUser['email']
                ?? null;

            $name =
                $firebaseUser['displayName']
                ?? 'Google User';

            if (empty($email)) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Google account email was not found.',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Find User
            |--------------------------------------------------------------------------
            */

            $user = User::where(
                'email',
                $email
            )->first();

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            if (! $user) {
                $user = User::create([
                    'name' =>
                        $name,

                    'email' =>
                        $email,

                    'password' =>
                        bcrypt(
                            Str::random(40)
                        ),

                    'email_verified_at' =>
                        now(),
                ]);
            } else {
                $updates = [];

                if (
                    empty($user->name) &&
                    ! empty($name)
                ) {
                    $updates['name'] =
                        $name;
                }

                if (
                    empty(
                        $user->email_verified_at
                    )
                ) {
                    $updates[
                        'email_verified_at'
                    ] = now();
                }

                if (! empty($updates)) {
                    $user->update(
                        $updates
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Login
            |--------------------------------------------------------------------------
            */

            Auth::login(
                $user,
                true
            );

            $request
                ->session()
                ->regenerate();

            return response()->json([
                'success' => true,

                'message' =>
                    'Google login successful.',

                'redirect' =>
                    route(
                        'home',
                        absolute: false
                    ),
            ]);
        } catch (Throwable $e) {
            Log::error(
                'Google login exception.',
                [
                    'exception' =>
                        get_class($e),

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,

                'message' =>
                    'Google login exception: '
                    . $e->getMessage(),

                'exception' =>
                    get_class($e),
            ], 500);
        }
    }
}

