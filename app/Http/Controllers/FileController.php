<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file',
            'directory_id' => 'required|exists:directories,id'
        ]);

        $directory = Directory::find($request->directory_id);

        foreach ($request->file('files') as $file) {
            $path = $file->store('uploads');
            File::create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'directory_id' => $directory->id,
                'user_id' => auth()->id()
            ]);
        }

        return response()->json(['message' => 'File uploaded successfully']);
    }

    public function show($id)
    {
        $file = File::findOrFail($id);
        return response()->json([
            'name' => $file->name,
            'size' => $file->size,
            'uploaded_at' => $file->created_at,
        ]);
    }

    public function update(Request $request, $id)
    {
        $file = File::findOrFail($id);
        $file->update(['name' => $request->name]);
        return response()->json($file);
    }

    public function destroy($id)
    {
        $file = File::findOrFail($id);
        Storage::delete($file->path);
        $file->delete();
        return response()->json(['message' => 'File deleted']);
    }

    // Для изменения статуса файла
    public function togglePublic($id)
    {
        $file = File::findOrFail($id);
        $file->update(['is_public' => !$file->is_public]);

        return response()->json([
            'message' => $file->is_public ? 'Фай в публичном доступе' : 'Файл скрыт из публичного доступа',
            'is_public' => $file->is_public,
        ]);
    }

    // Для отображения занятого пространства
    public function usedSpace()
    {
        $totalSize = File::whereHas('directory', function ($query) {
            $query->where('user_id', auth()->id());
        })->sum('size');

        return response()->json([
            'used_space' => $totalSize, // размер в байтах
        ]);
    }

    // Ссылка на скачивание для авторизованных
    public function generateDownloadLink($id)
    {
        $file = File::findOrFail($id);

        if (!$file->is_public) {
            return response()->json(['message' => 'File is not public'], 403);
        }

        $token = Str::random(60);
        $file->update(['download_token' => $token]);

        $downloadLink = url("/api/files/download/{$token}");

        return response()->json([
            'download_link' => $downloadLink
        ]);
    }

    // Ссылка на скачивание для неавторизованных
    public function download($token)
    {
        $file = File::where('download_token', $token)->firstOrFail();

        return response()->download(storage_path('app/' . $file->path));
    }

}
