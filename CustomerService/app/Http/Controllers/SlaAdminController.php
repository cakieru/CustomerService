<?php

namespace App\Http\Controllers;

use App\Models\SlaTarget;
use App\Models\SlaWeeklyCompliance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate([
            'targets' => 'required|array',
            'targets.*.target_time' => 'nullable|string|max:50',
            'targets.*.status' => ['nullable', Rule::in(['On Track', 'At Risk', 'Breached'])],
            'targets.*.badge_color' => 'nullable|string|max:100',
            'targets.*.badge_text_color' => 'nullable|string|max:100',
            'targets.*.progress_color' => 'nullable|string|max:100',
        ]);

        if ($request->has('targets')) {
            foreach ($request->targets as $id => $data) {
                $updateData = array_filter($data, fn($value) => $value !== null);
                if (!empty($updateData)) {
                    SlaTarget::where('id', $id)->update($updateData);
                }
            }
        }

        return redirect()->back()->with('success', 'SLA targets updated successfully!');
    }
}