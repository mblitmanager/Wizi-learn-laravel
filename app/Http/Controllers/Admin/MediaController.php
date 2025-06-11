<?php

namespace App\Http\Controllers\Admin;

use App\Events\MediaEvent;
use App\Events\TestNotification;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaRequest;
use App\Models\Formation;
use App\Services\MediaAdminService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\User;
use App\Http\Controllers\UserController;
use App\Models\Media;

class MediaController extends Controller
{
    protected $mediaService;
    protected $notificationService;
    protected $userController;

    public function __construct(
        MediaAdminService $mediaService,
        NotificationService $notificationService,
        UserController $userController
    ) {
        $this->mediaService = $mediaService;
        $this->notificationService = $notificationService;
        $this->userController = $userController;
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

    public function store(MediaRequest $request)
    {
        $validated = $request->validated();
        $sourceType = $request->input('source_type', 'file');
        if ($sourceType === 'file' && $request->hasFile('url')) {
            $file = $request->file('url');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/medias'), $fileName);
            $validated['url'] = 'uploads/medias/' . $fileName;
        } elseif ($sourceType === 'url' && $request->filled('url')) {
            $validated['url'] = $request->input('url');
        }
        $media = $this->mediaService->create($validated);

        // Déclencher un event après la création du média
        event(new MediaEvent($media));

        // Créer un événement pour EventController
        \App\Models\Event::create([
            'title' => 'Nouveau média',
            'message' => 'Un nouveau média a été ajouté : ' . ($media->titre ?? ''),
            'topic' => 'media',
            'data' => [
                'media_id' => $media->id,
                'media_title' => $media->titre ?? '',
                'media_url' => $media->url ?? '',
            ],
            'status' => 'pending',
            'created_at' => now(),
        ]);

        // // Notification à tous les stagiaires
        // $users = \App\Models\User::where('role', 'stagiaire')->get();
        // foreach ($users as $user) {
        //     $this->notificationService->notifyMediaCreated($user->id, $media->titre ?? '', $media->id);
        // }

        // Envoyer une notification pour le nouveau média
        $this->userController->notifyMediaCreated($media);


        return redirect()->route('medias.index')
            ->with('success', 'Le media a été créé avec succès.');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function update(MediaRequest $request, string $id)
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('media'), $filename);
            $validated['file_path'] = 'media/' . $filename;
            $validated['file_type'] = $file->getClientMimeType();
        }

        $media = Media::findOrFail($id);
        $media->update($validated);

        // Envoyer une notification pour la mise à jour du média
        $this->userController->notifyMediaCreated($media);

        return redirect()->route('media.index')
            ->with('success', 'Le média a été mis à jour avec succès.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $media = $this->mediaService->show($id);
        return view('admin.media.show', compact('media'));
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $media = $this->mediaService->show($id);
        if ($media) {
            // Supprimer le fichier physique si c'est un upload local
            if ($media->url && !filter_var($media->url, FILTER_VALIDATE_URL) && file_exists(public_path($media->url))) {
                @unlink(public_path($media->url));
            }
            $this->mediaService->delete($id);
        }
        return redirect()->route('medias.index')->with('success', 'Le média a été supprimé avec succès.');
    }

    public function stream(Request $request, $filename)
    {
        $path = public_path("uploads/medias/{$filename}");

        if (!file_exists($path)) {
            abort(404);
        }

        $size = filesize($path);
        $file = fopen($path, "rb");

        $start = 0;
        $end = $size - 1;

        header("Content-Type: video/mp4");
        header("Accept-Ranges: bytes");

        if ($request->headers->has('Range')) {
            // Exemple : bytes=1000-
            $range = $request->header('Range');
            [$start, $end] = explode('-', str_replace('bytes=', '', $range)) + [null, null];

            $start = intval($start);
            $end = $end ? intval($end) : $size - 1;

            fseek($file, $start);

            header("Content-Range: bytes $start-$end/$size");
            header("Content-Length: " . ($end - $start + 1));
            http_response_code(206); // Partial Content
        } else {
            header("Content-Length: $size");
        }

        $response = new StreamedResponse(function () use ($file, $start, $end) {
            $buffer = 1024 * 8; // 8 KB
            $position = $start;

            while (!feof($file) && $position <= $end) {
                $bytesToRead = min($buffer, $end - $position + 1);
                echo fread($file, $bytesToRead);
                flush();
                $position += $bytesToRead;
            }

            fclose($file);
        });

        return $response;
    }
}
