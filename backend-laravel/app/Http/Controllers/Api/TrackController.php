<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Tracks",
 *     description="API Endpoints for Track Management (only viewing and inserting)"
 * )
 */
class TrackController extends Controller
{
    /**
     * @OA\Get(
     *     path="/tracks",
     *     summary="Get all tracks",
     *     tags={"Tracks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of all tracks",
     *         @OA\JsonContent(
     *             @OA\Property(property="tracks", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="track_name", type="string", example="Academic"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Tracks retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        $tracks = Track::all();
        return response()->json([
            'tracks' => $tracks,
            'message' => 'Tracks retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/tracks",
     *     summary="Create a new track",
     *     tags={"Tracks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"track_name"},
     *             @OA\Property(property="track_name", type="string", example="Academic")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Track created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="track", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="track_name", type="string", example="Academic"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Track created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'track_name' => 'required|string|max:255|unique:tracks'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $track = Track::create([
            'track_name' => $request->track_name
        ]);

        return response()->json([
            'track' => $track,
            'message' => 'Track created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/tracks/{id}",
     *     summary="Get a specific track",
     *     tags={"Tracks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Track ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Track details",
     *         @OA\JsonContent(
     *             @OA\Property(property="track", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="track_name", type="string", example="Academic"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Track retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Track not found"
     *     )
     * )
     */
    public function show($id)
    {
        $track = Track::find($id);
        
        if (!$track) {
            return response()->json([
                'message' => 'Track not found'
            ], 404);
        }

        return response()->json([
            'track' => $track,
            'message' => 'Track retrieved successfully'
        ], 200);
    }

    /**
     * Initialize default tracks
     */
    public function initializeTracks()
    {
        $defaultTracks = [
            'Academic',
            'TVL',
            'Arts and Design',
            'Sports Track'
        ];

        $createdTracks = [];

        foreach ($defaultTracks as $trackName) {
            // Only create if it doesn't exist
            $track = Track::firstOrCreate(['track_name' => $trackName]);
            $createdTracks[] = $track;
        }

        return response()->json([
            'tracks' => $createdTracks,
            'message' => 'Default tracks initialized successfully'
        ], 200);
    }
}