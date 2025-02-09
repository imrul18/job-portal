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
    $job = $request->input('job');
    $uploadsPath = storage_path('app/public/uploads');
    $baseUrl = asset('storage/uploads');

    if (!File::exists($uploadsPath)) {
        return response()->json(['error' => 'Directory not found'], 404);
    }

    // Recursive function to scan directory and format results
    function getFolderFiles($path, $basePath, $baseUrl, $job = null)
    {
        $result = [];

        foreach (File::directories($path) as $folder) {
            $folderName = basename($folder);
            $files = array_map(function ($file) use ($basePath, $baseUrl, $folderName) {
                $relativePath = str_replace($basePath . '/', '', $file->getPathname());
                $obj = [
                    'name' => $file->getFilename(),
                    'path' => $relativePath,
                    'url' => $baseUrl . '/' . $relativePath,
                    'delete' => route('uploads.delete', ['path' => $relativePath]),
                ];
                if ($folderName != 'selected') {
                    $obj['mark_as_selected'] = route('uploads.mark-as-selected', ['path' => $relativePath]);
                }
                return $obj;
            }, File::files($folder));

            if (empty($job)) {
                $result[] = [
                    'folder_name' => $folderName,
                    'files' => $files,
                ];
            } else {
                if ($job == $folderName) {
                    $result[] = [
                        'folder_name' => $folderName,
                        'files' => $files,
                    ];
                }
            }
        }

        return $result;
    }

    $folders = getFolderFiles($uploadsPath, $uploadsPath, $baseUrl, $job);

    return response()->json(['files' => $folders]);
})->name('uploads.list');

Route::get('/delete/{path}', function (Request $request, $path) {
    $uploadsPath = storage_path('app/public/uploads');
    $filePath = $uploadsPath . '/' . $path;

    if (File::exists($filePath)) {
        File::delete($filePath);
    }
    return redirect(route('uploads.list'));
})->where('path', '.*')->name('uploads.delete');

// mark as selected should be take that file and move it to a new folder called selected
Route::get('/mark-as-seleted/{path}', function (Request $request, $path) {
    $uploadsPath = storage_path('app/public/uploads');
    $selectedPath = $uploadsPath . '/selected';
    $filePath = $uploadsPath . '/' . $path;

    if (!File::exists($selectedPath)) {
        File::makeDirectory($selectedPath);
    }

    if (File::exists($filePath)) {
        $newPath = $selectedPath . '/' . basename($filePath);
        File::move($filePath, $newPath);
    }
    return redirect(route('uploads.list'));
})->where('path', '.*')->name('uploads.mark-as-selected');
