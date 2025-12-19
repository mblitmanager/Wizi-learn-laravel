<?php

namespace App\Http\Controllers\Admin;

use App\Events\TestNotification;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaRequest;
use App\Models\Formation;
use App\Services\MediaAdminService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class MediaController extends Controller
{
    protected $mediaService;
    protected $notificationService;

    public function __construct(MediaAdminService $mediaService, NotificationService $notificationService)
    {
        $this->mediaService = $mediaService;
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Build query with optional filters: formation, type, category
        $query = \App\Models\Media::with('formation');

        if ($request->filled('formation')) {
            $query->where('formation_id', $request->formation);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('categorie', $request->category);
        }

        $media = $query->orderBy('ordre', 'asc')->paginate(15)->appends($request->query());

        // Data for filters
        $formations = Formation::select('id', 'titre')->get();
        $types = ['image', 'video', 'audio', 'document'];
        $categories = \App\Models\Media::select('categorie')->distinct()->pluck('categorie');

        return view('admin.media.index', compact('media', 'formations', 'types', 'categories'));
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

        // Notification uniquement aux stagiaires rattachés à la formation du média
        if ($media && $media->formation_id) {
            $catalogueIds = \App\Models\CatalogueFormation::where('formation_id', $media->formation_id)->pluck('id');
            $stagiaires = \App\Models\Stagiaire::whereHas('catalogue_formations', function ($q) use ($catalogueIds) {
                $q->whereIn('catalogue_formation_id', $catalogueIds);
            })->with('user')->get();

            foreach ($stagiaires as $stagiaire) {
                if ($stagiaire->user) {
                    $title = "\"{$media->titre}\" ajouté";
                    $formation = \App\Models\Formation::find($media->formation_id);
                    $formationTitre = $formation ? $formation->titre : '';
                    $body = "Une nouvelle vidéo,  \"{$media->titre}\", a été ajouté pour la formation \"{$formationTitre}\".";
                    $iconUrl = url('media/wizi.png');
                    $data = [
                        'type' => 'media',
                        'media_id' => (string)$media->id, // bien présent
                        'icon' => $iconUrl
                    ];
                    $this->notificationService->sendFcmToUser(
                        $stagiaire->user,
                        $title,
                        $body,
                        $data
                    );
                    \App\Models\Notification::create([
                        'user_id' => $stagiaire->user->id,
                        'type' => $data['type'],
                        'title' => $title,
                        'message' => $body,
                        'data' => $data, // data contient bien media_id
                        'read' => false,
                    ]);
                }
            }
        }

        return redirect()->route('medias.index')
            ->with('success', 'Le media a été créé avec succès.');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function update(Request $request, string $id)
    {
        // Valider les données
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => [
                'nullable',
                'file',
                'max:102400', // 100 Mo
                'mimes:jpg,jpeg,png,gif,mp4,avi,mov,pdf,mp3,wav,ogg'
            ],
            'url_text' => [
                'nullable',
                'url'
            ],
            'type' => 'required|string|in:video,document,image,audio',
            'categorie' => 'required|string|in:tutoriel,astuce',
            'duree' => 'nullable|integer|min:1',
            'ordre' => 'nullable|integer|min:0',
            'formation_id' => 'required|exists:formations,id',
            'source_type' => 'required|in:file,url',
        ]);

        $media = $this->mediaService->show($id);
        $sourceType = $request->input('source_type', 'file');

        // Gestion de l'URL selon le type de source
        if ($sourceType === 'file') {
            if ($request->hasFile('file')) {
                // Nouveau fichier uploadé
                $file = $request->file('file');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/medias'), $fileName);
                $validated['url'] = 'uploads/medias/' . $fileName;
            } else {
                // Aucun nouveau fichier, conserver l'ancien
                $validated['url'] = $media->url;
            }
        } elseif ($sourceType === 'url' && $request->filled('url_text')) {
            // URL fournie
            $validated['url'] = $request->input('url_text');
        }

        // Nettoyer les données avant mise à jour
        unset($validated['file']);
        unset($validated['url_text']);

        $this->mediaService->update($id, $validated);

        // Reste du code pour les notifications...
        $media = $this->mediaService->show($id);
        if ($media && $media->formation_id) {
            $catalogueIds = \App\Models\CatalogueFormation::where('formation_id', $media->formation_id)->pluck('id');
            $stagiaires = \App\Models\Stagiaire::whereHas('catalogue_formations', function ($q) use ($catalogueIds) {
                $q->whereIn('catalogue_formation_id', $catalogueIds);
            })->with('user')->get();

            foreach ($stagiaires as $stagiaire) {
                if ($stagiaire->user) {
                    $title = 'Média mis à jour';
                    $body = "Le média \"{$media->titre}\" a été mis à jour.";
                    $iconUrl = url('media/wizi.png');
                    $data = [
                        'type' => 'media',
                        'media_id' => (string)$media->id,
                        'icon' => $iconUrl
                    ];
                    $this->notificationService->sendFcmToUser(
                        $stagiaire->user,
                        $title,
                        $body,
                        $data
                    );
                    \App\Models\Notification::create([
                        'user_id' => $stagiaire->user->id,
                        'type' => $data['type'],
                        'title' => $title,
                        'message' => $body,
                        'data' => $data,
                        'read' => false,
                    ]);
                }
            }
        }

        return redirect()->route('medias.index')
            ->with('success', 'Le media a été mis à jour avec succès.');
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
        try {
            $media = $this->mediaService->show($id);
            if ($media) {
                // Supprimer le fichier physique si c'est un upload local
                if ($media->url && !filter_var($media->url, FILTER_VALIDATE_URL) && file_exists(public_path($media->url))) {
                    @unlink(public_path($media->url));
                }
                $this->mediaService->delete($id);
            }
            return redirect()->route('medias.index')->with('success', 'Le média a été supprimé avec succès.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors de la suppression du média : ' . $e->getMessage());
            return redirect()->route('medias.index')->with('error', 'Impossible de supprimer le média. Il est probablement lié à d\'autres éléments (quiz, historique, etc.).');
        }
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
