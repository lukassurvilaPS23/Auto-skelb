<?php

namespace App\Http\Controllers;

use App\Models\AutoRenginys;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Auto renginiai",
 *   description="Auto renginių CRUD ir XML eksportas"
 * )
 */

class AutoRenginiaiController extends Controller
{
    /**
 * @OA\Get(
 *   path="/api/auto-renginiai",
 *   tags={"Auto renginiai"},
 *   summary="Gauti auto renginių sąrašą",
 *   @OA\Parameter(name="miestas", in="query", required=false, @OA\Schema(type="string")),
 *   @OA\Parameter(name="statusas", in="query", required=false, @OA\Schema(type="string")),
 *   @OA\Response(response=200, description="OK")
 * )
 */

    // GET /api/auto-renginiai
    public function index()
    {
        return response()->json([
            'auto_renginiai' => AutoRenginys::orderBy('pradzios_data')->get()
        ]);
    }

/**
 * @OA\Get(
 *   path="/api/auto-renginiai/{autoRenginys}",
 *   tags={"Auto renginiai"},
 *   summary="Gauti vieną auto renginį pagal ID",
 *   @OA\Parameter(
 *     name="autoRenginys",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="integer"),
 *     example=1
 *   ),
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=404, description="Nerasta")
 * )
 */

    // GET /api/auto-renginiai/{id}
    public function show(AutoRenginys $autoRenginys)
    {
        return response()->json([
            'auto_renginys' => $autoRenginys
        ]);
    }

    /**
 * @OA\Post(
 *   path="/api/auto-renginiai",
 *   tags={"Auto renginiai"},
 *   summary="Sukurti auto renginį (organizatorius/administratorius)",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"pavadinimas","pradzios_data","miestas"},
 *       @OA\Property(property="pavadinimas", type="string", example="Žiemos driftas"),
 *       @OA\Property(property="aprasymas", type="string", example="Susitikimas ir driftas"),
 *       @OA\Property(property="pradzios_data", type="string", example="2025-12-30 18:00:00"),
 *       @OA\Property(property="pabaigos_data", type="string", example="2025-12-30 21:00:00"),
 *       @OA\Property(property="miestas", type="string", example="Vilnius"),
 *       @OA\Property(property="adresas", type="string", example="Ozo g. 25")
 *     )
 *   ),
 *   @OA\Response(response=201, description="Sukurta"),
 *   @OA\Response(response=401, description="Neprisijungęs"),
 *   @OA\Response(response=403, description="Neturi teisių"),
 *   @OA\Response(response=422, description="Validacijos klaida")
 * )
 */

    // POST /api/auto-renginiai
    public function store(Request $request)
    {
        if (!$request->user()->hasAnyRole(['organizatorius', 'administratorius'])) {
            return response()->json(['zinute' => 'Neturite teisių.'], 403);
        }

        $data = $request->validate([
            'pavadinimas' => 'required|string|max:255',
            'aprasymas' => 'nullable|string',
            'pradzios_data' => 'required|date',
            'pabaigos_data' => 'nullable|date',
            'miestas' => 'required|string',
            'adresas' => 'nullable|string',
        ]);

        $data['organizatorius_id'] = $request->user()->id;

        $autoRenginys = AutoRenginys::create($data);

        return response()->json([
            'zinute' => 'Auto renginys sukurtas',
            'auto_renginys' => $autoRenginys
        ], 201);
    }

    /**
 * @OA\Put(
 *   path="/api/auto-renginiai/{autoRenginys}",
 *   tags={"Auto renginiai"},
 *   summary="Atnaujinti auto renginį (savininkas arba administratorius)",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(
 *     name="autoRenginys",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="integer"),
 *     example=1
 *   ),
 *   @OA\RequestBody(
 *     required=false,
 *     @OA\JsonContent(
 *       @OA\Property(property="pavadinimas", type="string", example="Atnaujintas pavadinimas"),
 *       @OA\Property(property="miestas", type="string", example="Kaunas"),
 *       @OA\Property(property="statusas", type="string", example="aktyvus")
 *     )
 *   ),
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=401, description="Neprisijungęs"),
 *   @OA\Response(response=403, description="Neturi teisių"),
 *   @OA\Response(response=404, description="Nerasta"),
 *   @OA\Response(response=422, description="Validacijos klaida")
 * )
 */

    // PUT /api/auto-renginiai/{id}
    public function update(Request $request, AutoRenginys $autoRenginys)
    {
        $user = $request->user();

        if ($user->id !== $autoRenginys->organizatorius_id && !$user->hasRole('administratorius')) {
            return response()->json(['zinute' => 'Neturite teisių.'], 403);
        }

        $autoRenginys->update(
            $request->only(['pavadinimas', 'aprasymas', 'miestas', 'adresas', 'statusas'])
        );

        return response()->json([
            'zinute' => 'Auto renginys atnaujintas',
            'auto_renginys' => $autoRenginys
        ]);
    }

    /**
 * @OA\Delete(
 *   path="/api/auto-renginiai/{autoRenginys}",
 *   tags={"Auto renginiai"},
 *   summary="Ištrinti auto renginį (savininkas arba administratorius)",
 *   security={{"bearerAuth":{}}},
 *   @OA\Parameter(
 *     name="autoRenginys",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="integer"),
 *     example=1
 *   ),
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=401, description="Neprisijungęs"),
 *   @OA\Response(response=403, description="Neturi teisių"),
 *   @OA\Response(response=404, description="Nerasta")
 * )
 */

    // DELETE /api/auto-renginiai/{id}
    public function destroy(Request $request, AutoRenginys $autoRenginys)
    {
        $user = $request->user();

        if ($user->id !== $autoRenginys->organizatorius_id && !$user->hasRole('administratorius')) {
            return response()->json(['zinute' => 'Neturite teisių.'], 403);
        }

        $autoRenginys->delete();

        return response()->json([
            'zinute' => 'Auto renginys ištrintas'
        ]);
    }

    /**
 * @OA\Get(
 *   path="/api/auto-renginiai/export.xml",
 *   tags={"Auto renginiai"},
 *   summary="Eksportuoti auto renginius į XML",
 *   @OA\Response(
 *     response=200,
 *     description="XML",
 *     @OA\MediaType(mediaType="application/xml")
 *   )
 * )
 */

    // GET /api/auto-renginiai/export.xml
    public function exportXml()
    {

        $renginiai = \App\Models\AutoRenginys::orderBy('pradzios_data')->get();


        $xml = new \SimpleXMLElement('<auto_renginiai/>');


        foreach ($renginiai as $r) {
            $item = $xml->addChild('auto_renginys');
            $item->addChild('id', (string)$r->id);
            $item->addChild('pavadinimas', htmlspecialchars($r->pavadinimas));
            $item->addChild('miestas', htmlspecialchars($r->miestas));
            $item->addChild('pradzios_data', (string)$r->pradzios_data);
            $item->addChild('pabaigos_data', (string)($r->pabaigos_data ?? ''));
            $item->addChild('statusas', htmlspecialchars($r->statusas));
        }

    return response($xml->asXML(), 200)
        ->header('Content-Type', 'application/xml; charset=UTF-8');
}

}
