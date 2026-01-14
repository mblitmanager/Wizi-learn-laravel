<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // For selecting specific users if needed
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,stagiaires,formateurs,autres,specific_users',
            'scheduled_at' => 'nullable|date',
        ]);

        $announcement = new Announcement($validated);
        $announcement->created_by = auth()->id();
        $announcement->status = 'pending'; // Default status
        
        // If scheduled in the past or now, mark as sent (or handle via job)
        // For now, simple logic:
        if (empty($validated['scheduled_at'])) {
            $announcement->status = 'sent';
            $announcement->sent_at = now();
        }

        $announcement->save();

        return redirect()->route('announcements.index')->with('success', 'Annonce créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,stagiaires,formateurs,autres,specific_users',
            'scheduled_at' => 'nullable|date',
        ]);

        $announcement->fill($validated);
        
        if ($announcement->isDirty('scheduled_at') && $announcement->scheduled_at > now()) {
            $announcement->status = 'pending';
            $announcement->sent_at = null;
        }

        $announcement->save();

        return redirect()->route('announcements.index')->with('success', 'Annonce mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Annonce supprimée avec succès.');
    }
}
