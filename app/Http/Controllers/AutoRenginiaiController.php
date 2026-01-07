<?php

namespace App\Http\Controllers;

use App\Models\AutoRenginys;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AutoRenginiaiController extends Controller
{
    /**
     * @OA\Tag(
     *   name="Auto renginiai",
     *   description="Auto renginių CRUD, registracijų peržiūra ir XML eksportas"
     * )
     */

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
    public function index(Request $request)
    {
        $q = AutoRenginys::query()->orderBy('pradzios_data');

        if ($request->filled('miestas')) {
            $q->where('miestas', 'like', '%' . $request->query('miestas') . '%');
        }

        if ($request->filled('statusas')) {
            $q->where('statusas', $request->query('statusas'));
        }

        return response()->json([
            'auto_renginiai' => $q->get()
        ], 200);
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
    public function show(AutoRenginys $autoRenginys)
    {
        return response()->json([
            'auto_renginys' => $autoRenginys
        ], 200);
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
    public function store(Request $request)
    {
        if (!$request->user()->hasAnyRole(['organizatorius', 'administratorius'])) {
            return response()->json(['zinute' => 'Neturite teisių.'], 403);
        }

        $data = $request->validate([
            'pavadinimas' => 'required|string|max:255',
            'aprasymas' => 'nullable|string',
            'pradzios_data' => 'required|date',
            'pabaigos_data' => 'nullable|date|after_or_equal:pradzios_data',
            'miestas' => 'required|string|max:255',
            'adresas' => 'nullable|string|max:255',
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
     *       @OA\Property(property="aprasymas", type="string", example="Atnaujintas aprašymas"),
     *       @OA\Property(property="miestas", type="string", example="Kaunas"),
     *       @OA\Property(property="adresas", type="string", example="Naujas adresas"),
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
    public function update(Request $request, AutoRenginys $autoRenginys)
    {
        $user = $request->user();

        if ((int)$user->id !== (int)$autoRenginys->organizatorius_id && !$user->hasRole('administratorius')) {
            return response()->json(['zinute' => 'Neturite teisių.'], 403);
        }

        $data = $request->validate([
            'pavadinimas' => 'sometimes|string|max:255',
            'aprasymas' => 'sometimes|nullable|string',
            'miestas' => 'sometimes|string|max:255',
            'adresas' => 'sometimes|nullable|string|max:255',
            'statusas' => 'sometimes|string|max:50',
        ]);

        $autoRenginys->update($data);

        return response()->json([
            'zinute' => 'Auto renginys atnaujintas',
            'auto_renginys' => $autoRenginys->fresh(),
        ], 200);
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
    public function destroy(Request $request, AutoRenginys $autoRenginys)
    {
        $user = $request->user();

        if ((int)$user->id !== (int)$autoRenginys->organizatorius_id && !$user->hasRole('administratorius')) {
            return response()->json(['zinute' => 'Neturite teisių.'], 403);
        }

        $autoRenginys->delete();

        return response()->json([
            'zinute' => 'Auto renginys ištrintas'
        ], 200);
    }

    /**
     * @OA\Get(
     *   path="/api/auto-renginiai/{autoRenginys}/registracijos",
     *   tags={"Auto renginiai"},
     *   summary="Peržiūrėti registracijas į konkretų renginį (organizatorius arba administratorius)",
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
     *   @OA\Response(response=404, description="Renginys nerastas")
     * )
     */
    public function registracijos(Request $request, AutoRenginys $autoRenginys)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['zinute' => 'Neprisijungęs'], 401);
        }

        $yraAdmin = $user->hasRole('administratorius');
        $yraSavininkas = (int)$autoRenginys->organizatorius_id === (int)$user->id;

        if (!$yraAdmin && !$yraSavininkas) {
            return response()->json(['zinute' => 'Neturite teisių.'], 403);
        }

        $registracijos = $autoRenginys->registracijos()
            ->with('vartotojas:id,name,email')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'sukurta' => optional($r->created_at)->toDateTimeString(),
                    'vartotojas' => [
                        'id' => $r->vartotojas?->id,
                        'vardas' => $r->vartotojas?->name,
                        'el_pastas' => $r->vartotojas?->email,
                    ],
                ];
            });

        return response()->json([
            'auto_renginys' => [
                'id' => $autoRenginys->id,
                'pavadinimas' => $autoRenginys->pavadinimas,
            ],
            'registracijos' => $registracijos,
        ], 200);
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
    public function exportXml()
    {
        $renginiai = AutoRenginys::orderBy('pradzios_data')->get();

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
