<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaRequest;
use App\Models\Formation;
use App\Services\MediaAdminService;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaAdminService $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $media = $this->mediaService->list();
        return view('admin.media.index', compact('media'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formations = Formation::all();

        return view('admin.media.create', compact('formations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MediaRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('media', $filename, 'public');

            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getClientMimeType(); // image/jpeg, video/mp4, application/pdf
        }

        $this->mediaService->create($validated);

        return redirect()->route('medias.index')
            ->with('success', 'Le media a été créé avec succès.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $media = $this->mediaService->show($id);
        $formations = Formation::all();
        return view('admin.media.edit', compact('media', 'formations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MediaRequest $request, string $id)
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('media', $filename, 'public');

            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getClientMimeType();
        }

        $this->mediaService->update($id, $validated);

        return redirect()->route('medias.index')
            ->with('success', 'Le media a été mis à jour avec succès.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
