<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InscriptionRequest;
use Illuminate\Http\Request;

class InscriptionRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = InscriptionRequest::with(['stagiaire', 'catalogueFormation']);
        if ($request->filled('stagiaire')) {
            $query->whereHas('stagiaire', function($q) use ($request) {
                $q->where('prenom', 'like', '%' . $request->stagiaire . '%');
            });
        }
        if ($request->filled('formation')) {
            $query->whereHas('catalogueFormation', function($q) use ($request) {
                $q->where('titre', 'like', '%' . $request->formation . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $sort = $request->get('sort', 'created_at');
        $dir = $request->get('dir', 'desc');
        $requests = $query->orderBy($sort, $dir)->paginate(20);
        return view('admin.inscription_requests.index', compact('requests'));
    }

    public function updateStatus($id)
    {
        $request = InscriptionRequest::findOrFail($id);
        $request->status = request('status');
        $request->save();
        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    public function exportCsv(Request $request)
    {
        $requests = InscriptionRequest::with(['stagiaire', 'catalogueFormation'])->get();
        $csv = "ID,Stagiaire,Formation,Statut,Date\n";
        foreach ($requests as $req) {
            $csv .= "{$req->id},\"{$req->stagiaire?->prenom}\",\"{$req->catalogueFormation?->titre}\",{$req->status},{$req->created_at}\n";
        }
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=inscription_requests.csv');
    }
} 