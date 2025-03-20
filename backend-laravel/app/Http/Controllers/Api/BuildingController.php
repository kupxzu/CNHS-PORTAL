<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Buildings",
 *     description="API Endpoints for Building Management"
 * )
 */
class BuildingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/buildings",
     *     summary="Get all buildings",
     *     tags={"Buildings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of buildings",
     *         @OA\JsonContent(
     *             @OA\Property(property="buildings", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="building_name", type="string", example="Main Building"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Buildings retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $buildings = Building::all();
        return response()->json([
            'buildings' => $buildings,
            'message' => 'Buildings retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/buildings",
     *     summary="Create a new building",
     *     tags={"Buildings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"building_name"},
     *             @OA\Property(property="building_name", type="string", example="Science Building")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Building created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="building", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="building_name", type="string", example="Science Building"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Building created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_name' => 'required|string|max:255|unique:buildings'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $building = Building::create([
            'building_name' => $request->building_name
        ]);

        return response()->json([
            'building' => $building,
            'message' => 'Building created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/buildings/{id}",
     *     summary="Get a specific building",
     *     tags={"Buildings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Building ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Building details with its rooms",
     *         @OA\JsonContent(
     *             @OA\Property(property="building", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="building_name", type="string", example="Main Building"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="rooms", type="array", @OA\Items(type="object"))
     *             ),
     *             @OA\Property(property="message", type="string", example="Building retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Building not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Building not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $building = Building::with('rooms')->find($id);
        
        if (!$building) {
            return response()->json([
                'message' => 'Building not found'
            ], 404);
        }

        return response()->json([
            'building' => $building,
            'message' => 'Building retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/buildings/{id}",
     *     summary="Update a building",
     *     tags={"Buildings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Building ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"building_name"},
     *             @OA\Property(property="building_name", type="string", example="Updated Building Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Building updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="building", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="building_name", type="string", example="Updated Building Name"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Building updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Building not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Building not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $building = Building::find($id);
        
        if (!$building) {
            return response()->json([
                'message' => 'Building not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'building_name' => 'required|string|max:255|unique:buildings,building_name,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $building->update([
            'building_name' => $request->building_name
        ]);

        return response()->json([
            'building' => $building,
            'message' => 'Building updated successfully'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/buildings/{id}",
     *     summary="Delete a building",
     *     tags={"Buildings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Building ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Building deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Building deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Building not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Building not found")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $building = Building::find($id);
        
        if (!$building) {
            return response()->json([
                'message' => 'Building not found'
            ], 404);
        }

        $building->delete();

        return response()->json([
            'message' => 'Building deleted successfully'
        ], 200);
    }
}