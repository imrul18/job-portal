<?php

use App\Http\Controllers\ApplyListController;
use App\Http\Controllers\AuthController;
use App\Models\ApplyList;
use App\Models\CareerJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin'])->name('postlogin');

Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('applications', [ApplyListController::class, 'index'])->name('applications.index');

    Route::post('applications/update/status', [ApplyListController::class, 'statusUpdate'])->name('applications.update.status');
});

Route::get('/career/{slug}', function (Request $request, $slug) {
    $job = CareerJob::where('slug', $slug)->first();
    if (!$job) {
        abort(404);
    }
    return view('career', ['job' => $job]);
})->name('uploads.index');

Route::post('/career', function (Request $request) {
    if (empty($request->input('phone')) && empty($request->input('email'))) {
        return redirect()->back()->withInput()->with('error', 'Provide at least one contact information');
    }

    $slug = $request->input('slug');
    $job = CareerJob::where('slug', $slug)->first();

    if (!$job) {
        return redirect()->back()->withInput()->with('error', 'Wrong job information');
    }

    if ($job->applyLists()->where('email', $request->input('email'))->orWhere('phone', $request->input('phone'))->exists()) {
        return redirect()->back()->withInput()->with('error', 'You have already applied for this job');
    }

    if ($request->hasFile('file')) {
        try {
            $file = $request->file('file');

            $originalName = $file->getClientOriginalName();
            $timestamp = now()->format('Y-m-d-H-i-s');
            $newFileName = $timestamp . '_' . $originalName;

            $file->storeAs('uploads', $newFileName, 'public');

            $job->applyLists()->create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'resume' => $newFileName,
                'status' => 'pending'
            ]);
            return redirect()->back()->with('success', 'Your application has been submitted');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', 'Failed to upload your resume');
        }
    } else {
        return redirect()->back()->withInput()->with('error', 'Please upload your resume');
    }
})->name('applications.store');
