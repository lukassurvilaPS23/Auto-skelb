<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Autentifikacija",
 *   description="Registracija, prisijungimas, profilis ir atsijungimas"
 * )
 */


class AutentifikacijaController extends Controller
{
    /**
 * @OA\Post(
 *   path="/api/registruotis",
 *   tags={"Autentifikacija"},
 *   summary="Vartotojo registracija ir tokeno gavimas",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"vardas","el_pastas","slaptazodis"},
 *       @OA\Property(property="vardas", type="string", example="Lukas"),
 *       @OA\Property(property="el_pastas", type="string", example="lukas@auto.lt"),
 *       @OA\Property(property="slaptazodis", type="string", example="slaptazodis")
 *     )
 *   ),
 *   @OA\Response(
 *     response=201,
 *     description="Sėkminga registracija",
 *     @OA\JsonContent(
 *       @OA\Property(property="zinute", type="string", example="Registracija sėkminga"),
 *       @OA\Property(property="token", type="string", example="1|abc...")
 *     )
 *   ),
 *   @OA\Response(response=422, description="Validacijos klaida")
 * )
 */

    public function registruotis(Request $request)
    {
        $data = $request->validate([
            'vardas' => 'required|string|max:255',
            'el_pastas' => 'required|email|unique:users,email',
            'slaptazodis' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $data['vardas'],
            'email' => $data['el_pastas'],
            'password' => Hash::make($data['slaptazodis']),
        ]);

        // Reikia, kad roles būtų suseedintos:
        $user->assignRole('vartotojas');

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'zinute' => 'Registracija sėkminga',
            'token' => $token,
        ], 201);
    }

/**
 * @OA\Post(
 *   path="/api/prisijungti",
 *   tags={"Autentifikacija"},
 *   summary="Prisijungimas ir tokeno gavimas",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"el_pastas","slaptazodis"},
 *       @OA\Property(property="el_pastas", type="string", example="org@auto.lt"),
 *       @OA\Property(property="slaptazodis", type="string", example="slaptazodis")
 *     )
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Sėkmingas prisijungimas",
 *     @OA\JsonContent(
 *       @OA\Property(property="zinute", type="string", example="Prisijungta"),
 *       @OA\Property(property="token", type="string", example="1|abc...")
 *     )
 *   ),
 *   @OA\Response(response=422, description="Validacijos klaida")
 * )
 */

    public function prisijungti(Request $request)
    {
        $request->validate([
            'el_pastas' => 'required|email',
            'slaptazodis' => 'required|string',
        ]);

        $user = User::where('email', $request->el_pastas)->first();

        if (!$user || !Hash::check($request->slaptazodis, $user->password)) {
            throw ValidationException::withMessages([
                'el_pastas' => ['Neteisingi prisijungimo duomenys'],
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'zinute' => 'Prisijungta',
            'token' => $token,
        ], 200);
    }

    /**
 * @OA\Post(
 *   path="/api/atsijungti",
 *   tags={"Autentifikacija"},
 *   summary="Atsijungti (ištrina current token)",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(
 *     response=200,
 *     description="OK",
 *     @OA\JsonContent(
 *       @OA\Property(property="zinute", type="string", example="Atsijungta")
 *     )
 *   ),
 *   @OA\Response(response=401, description="Neprisijungęs")
 * )
 */

    public function atsijungti(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'zinute' => 'Atsijungta',
        ], 200);
    }

    /**
 * @OA\Get(
 *   path="/api/as",
 *   tags={"Autentifikacija"},
 *   summary="Gauti prisijungusio vartotojo informaciją",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=401, description="Neprisijungęs")
 * )
 */

    public function as(Request $request)
    {
        return response()->json([
            'vartotojas' => $request->user(),
        ], 200);
    }
}
