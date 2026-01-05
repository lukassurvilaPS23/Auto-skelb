<?php

namespace App\Http\Controllers;

use App\Models\AutoRenginys;
use App\Models\RenginioRegistracija;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Registracijos",
 *   description="Registracija į auto renginius ir registracijos atšaukimas"
 * )
 */

class RenginiuRegistracijosController extends Controller
{

    /**
 * @OA\Post(
 *   path="/api/auto-renginiai/{autoRenginys}/registracija",
 *   tags={"Registracijos"},
 *   summary="Užsiregistruoti į auto renginį",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(
 *     name="autoRenginys",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="integer"),
 *     example=1
 *   ),
 *   @OA\Response(response=201, description="Registracija sėkminga"),
 *   @OA\Response(response=401, description="Neprisijungęs"),
 *   @OA\Response(response=404, description="Renginys nerastas"),
 *   @OA\Response(response=422, description="Negalima registruotis / validacija")
 * )
 */

    // POST /api/auto-renginiai/{id}/registracija
    public function registruotis(Request $request, AutoRenginys $autoRenginys)
    {
        $user = $request->user();

        // Organizatorius gali būti ir dalyvis, bet dažnai nenorima registruoti į savo renginį:
        // jei nenori šito ribojimo — ištrink šitą if bloką.
        if ($user->id === $autoRenginys->organizatorius_id) {
            return response()->json(['zinute' => 'Negalite registruotis į savo auto renginį.'], 422);
        }

        $registracija = RenginioRegistracija::firstOrCreate(
            [
                'auto_renginys_id' => $autoRenginys->id,
                'vartotojas_id' => $user->id,
            ],
            [
                'statusas' => 'patvirtinta',
            ]
        );

        return response()->json([
            'zinute' => 'Registracija sėkminga.',
            'registracija' => $registracija,
        ], 201);
    }

    /**
 * @OA\Delete(
 *   path="/api/auto-renginiai/{autoRenginys}/registracija",
 *   tags={"Registracijos"},
 *   summary="Atšaukti registraciją į auto renginį",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(
 *     name="autoRenginys",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="integer"),
 *     example=1
 *   ),
 *   @OA\Response(response=200, description="Registracija atšaukta"),
 *   @OA\Response(response=401, description="Neprisijungęs"),
 *   @OA\Response(response=404, description="Registracijos nerasta")
 * )
 */

    // DELETE /api/auto-renginiai/{id}/registracija
    public function atsisakyti(Request $request, AutoRenginys $autoRenginys)
    {
        $user = $request->user();

        $deleted = RenginioRegistracija::where('auto_renginys_id', $autoRenginys->id)
            ->where('vartotojas_id', $user->id)
            ->delete();

        if (!$deleted) {
            return response()->json(['zinute' => 'Registracijos nerasta.'], 404);
        }

        return response()->json(['zinute' => 'Registracija atšaukta.'], 200);
    }
}
