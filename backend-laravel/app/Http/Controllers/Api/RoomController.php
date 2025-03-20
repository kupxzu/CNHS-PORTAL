<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Rooms",
 *     description="API Endpoints for Room Management"
 * )
 */
class RoomController extends Controller
{
    /**
     * @OA\Get(
     *     path="/rooms",
     *     summary="Get all rooms",
     *     tags={"Rooms"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of rooms with their buildings",
     *         @OA\JsonContent(
     *             @OA\Property(property="rooms", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="room_name", type="string", example="Science Lab"),
     *                     @OA\Property(property="room_number", type="string", example="101"),
     *                     @OA\Property(property="building_id", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="building", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="building_name", type="string", example="Science Building"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Rooms retrieved successfully")
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
        $rooms = Room::with('building')->get();
        return response()->json([
            'rooms' => $rooms,
            'message' => 'Rooms retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/rooms",
     *     summary="Create a new room",
     *     tags={"Rooms"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"room_name", "room_number", "building_id"},
     *             @OA\Property(property="room_name", type="string", example="Computer Lab"),
     *             @OA\Property(property="room_number", type="string", example="201"),
     *             @OA\Property(property="building_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Room created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="room", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="room_name", type="string", example="Computer Lab"),
     *                 @OA\Property(property="room_number", type="string", example="201"),
     *                 @OA\Property(property="building_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Room created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Building not found"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_name' => 'required|string|max:255',
            'room_number' => 'required|string|max:50',
            'building_id' => 'required|integer|exists:buildings,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if building exists
        $building = Building::find($request->building_id);
        if (!$building) {
            return response()->json([
                'message' => 'Building not found'
            ], 404);
        }

        $room = Room::create([
            'room_name' => $request->room_name,
            'room_number' => $request->room_number,
            'building_id' => $request->building_id
        ]);

        return response()->json([
            'room' => $room,
            'message' => 'Room created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/rooms/{id}",
     *     summary="Get a specific room",
     *     tags={"Rooms"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room details with its building",
     *         @OA\JsonContent(
     *             @OA\Property(property="room", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="room_name", type="string", example="Science Lab"),
     *                 @OA\Property(property="room_number", type="string", example="101"),
     *                 @OA\Property(property="building_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="building", type="object")
     *             ),
     *             @OA\Property(property="message", type="string", example="Room retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     )
     * )
     */
    public function show($id)
    {
        $room = Room::with('building')->find($id);
        
        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        return response()->json([
            'room' => $room,
            'message' => 'Room retrieved successfully'
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/rooms/{id}",
     *     summary="Update a room",
     *     tags={"Rooms"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"room_name", "room_number", "building_id"},
     *             @OA\Property(property="room_name", type="string", example="Updated Room Name"),
     *             @OA\Property(property="room_number", type="string", example="301"),
     *             @OA\Property(property="building_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="room", type="object"),
     *             @OA\Property(property="message", type="string", example="Room updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        
        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'room_name' => 'required|string|max:255',
            'room_number' => 'required|string|max:50',
            'building_id' => 'required|integer|exists:buildings,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room->update([
            'room_name' => $request->room_name,
            'room_number' => $request->room_number,
            'building_id' => $request->building_id
        ]);

        return response()->json([
            'room' => $room,
            'message' => 'Room updated successfully'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/rooms/{id}",
     *     summary="Delete a room",
     *     tags={"Rooms"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Room deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $room = Room::find($id);
        
        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully'
        ], 200);
    }
}