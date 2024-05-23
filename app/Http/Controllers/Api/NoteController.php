<?php

namespace App\Http\Controllers\Api;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\PersonalAccessToken;

class NoteController extends Controller
{

    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        $user = $accessToken->tokenable;

        $notes = Note::where('userid', $user->id)->get();

        return response()->json([
            'status' => true,
            'data' => $notes
        ], 200);
        // return response()->json(Note::all(), 200);
    }
    public function create(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'categories' => 'nullable'
        ]);
        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = $accessToken->tokenable;

        // $note = Note::create($request->all());
        $note = Note::create([
            'description' => $request->description,
            'userid' => $user->id,
            'categories' => $request->categories
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Note Created Successfully!!',
            'data' => $note,
        ], 200);
    }

    public function show($id)
    {
        $note = Note::find($id);
        if (!$note) {
            return response()->json([
                'status' => false,
                'message' => 'Note not found'
            ], 404);
        }

        return response()->json($note, 200);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'categories' => 'nullable'
        ]);
        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
        $user = $accessToken->tokenable;
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'status' => false,
                'message' => 'Note not found'
            ], 404);
        }

        if ($note->userid !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => "Unauthorized"
            ], 401);
        }


        // $note->update($request->all());
        $note->update([
            'description' => $request->description,
            'categories' => $request->categories
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Note Updated Successfully!!',
            'data' => $note,
        ], 200);
    }
    public function destroy(Request $request, $id)
    {
        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',

            ], 401);
        }

        $user = $accessToken->tokenable;

        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'status' => false,
                'message' => 'Note not found',
            ], 404);
        }

        if ($note->userid !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => 'hello'
            ], 401);
        }

        $note->delete();

        return response()->json([
            'status' => true,
            'message' => 'Note Deleted Successfully!!',
        ], 200);
    }
}
