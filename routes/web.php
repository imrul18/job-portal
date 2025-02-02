<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;


Route::get('/', function () {
    return "This is the broken page";
});

Route::get('/career/{slug}', function (Request $request, $slug) {
    return view('welcome', ['slug' => $slug]);
})->name('uploads.index');

Route::post('/career', function (Request $request) {
    $slug = $request->input('slug');

    if ($request->hasFile('resume')) {
        try {
            $file = $request->file('resume');

            $originalName = $file->getClientOriginalName();
            $timestamp = now()->format('Y-m-d-H-i-s');
            $newFileName = $timestamp . '_' . $originalName;

            $file->storeAs('uploads/' . $slug, $newFileName, 'public');
        } catch (\Throwable $th) {
            return response()->json([
                'url' => route('uploads.index', ['slug' => $slug]),
                'success' => false
            ]);
        }

        return response()->json([
            'url' => route('uploads.index', ['slug' => $slug]),
            'success' => true
        ]);
    }

    return response()->json([
        'url' => route('uploads.index', ['slug' => $slug]),
        'success' => false
    ]);
})->name('uploads.store');


Route::get('/career-file-list', function (Request $request) {
    $uploadsPath = storage_path('app/public/uploads');
    $baseUrl = asset('storage/uploads');

    if (!File::exists($uploadsPath)) {
        return response()->json(['error' => 'Directory not found'], 404);
    }

    // Recursive function to scan directory and format results
    function getFolderFiles($path, $basePath, $baseUrl)
    {
        $result = [];

        foreach (File::directories($path) as $folder) {
            $folderName = basename($folder);
            $files = array_map(function ($file) use ($basePath, $baseUrl) {
                $relativePath = str_replace($basePath . '/', '', $file->getPathname());
                return [
                    'name' => $file->getFilename(),
                    'path' => $relativePath,
                    'url' => $baseUrl . '/' . $relativePath,
                ];
            }, File::files($folder));

            $result[] = [
                'folder_name' => $folderName,
                'files' => $files,
            ];
        }

        return $result;
    }

    $folders = getFolderFiles($uploadsPath, $uploadsPath, $baseUrl);

    return response()->json(['files' => $folders]);
});
