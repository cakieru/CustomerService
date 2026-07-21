<?php

namespace App\Http\Controllers;

use App\Models\SlaTarget;
use App\Models\SlaWeeklyCompliance;
use Illuminate\Http\Request;

class SlaAdminController extends Controller
{
    public function index()
    {
        $targets = SlaTarget::orderBy('sort_order')->get();
        $weekly = SlaWeeklyCompliance::orderBy('sort_order')->get();
        return view('admin.sla', compact('targets', 'weekly'));
    }

    public function update(Request $request)
    {
        // DEBUG: Check kung may data
        // dd($request->all());
        
        if ($request->has('targets')) {
            foreach ($request->targets as $id => $data) {
                SlaTarget::where('id', $id)->update($data);
            }
        }
        
        return redirect()->back()->with('success', 'Data updated successfully!');
    }
}