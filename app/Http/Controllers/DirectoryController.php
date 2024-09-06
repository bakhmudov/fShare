<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $directory = Directory::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id
        ]);

        return response()->json($directory);
    }

    public function update(Request $request, $id)
    {
        $directory = Directory::findOrFail($id);
        $directory->update(['name' => $request->name]);
        return response()->json($directory);
    }

    public function destroy($id)
    {
        $directory = Directory::findOrFail($id);
        $directory->delete();
        return response()->json(['message' => 'Directory deleted']);
    }
}
