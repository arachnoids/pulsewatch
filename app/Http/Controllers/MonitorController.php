<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function index()
    {
        $monitors = Monitor::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('monitors.index', compact('monitors'));
    }

    public function create()
    {
        return view('monitors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'interval_seconds' => 'required|integer|min:30|max:3600',
            'timeout_seconds' => 'required|integer|min:1|max:60',
        ]);

        $validated['user_id'] = auth()->id();

        Monitor::create($validated);

        return redirect()->route('monitors.index')
            ->with('success', 'Monitor berhasil ditambahkan.');
    }

    public function show(Monitor $monitor)
    {
        abort_if($monitor->user_id !== auth()->id(), 403);
        return view('monitors.show', compact('monitor'));
    }

    public function edit(Monitor $monitor)
    {
        abort_if($monitor->user_id !== auth()->id(), 403);
        return view('monitors.edit', compact('monitor'));
    }

    public function update(Request $request, Monitor $monitor)
    {
        abort_if($monitor->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'interval_seconds' => 'required|integer|min:30|max:3600',
            'timeout_seconds' => 'required|integer|min:1|max:60',
        ]);

        $monitor->update($validated);

        return redirect()->route('monitors.index')
            ->with('success', 'Monitor berhasil diupdate.');
    }

    public function destroy(Monitor $monitor)
    {
        abort_if($monitor->user_id !== auth()->id(), 403);
        $monitor->delete();

        return redirect()->route('monitors.index')
            ->with('success', 'Monitor berhasil dihapus.');
    }
}