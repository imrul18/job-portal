<?php

namespace App\Http\Controllers;

use App\Models\ApplyList;
use App\Models\CareerJob;
use Illuminate\Http\Request;

class ApplyListController extends Controller
{
    public function index(Request $request)
    {
        $data = ApplyList::query();
        if (!empty($request->status)) $data->where('status', $request->status);
        if (!empty($request->job)) $data->where('job_id', $request->job);

        $data = $data->paginate(10);

        $jobs = CareerJob::all();

        return view('applications.index', compact('data', 'jobs'));
    }

    public function statusUpdate(Request $request)
    {
        $data = ApplyList::find($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => true]);
    }
}
