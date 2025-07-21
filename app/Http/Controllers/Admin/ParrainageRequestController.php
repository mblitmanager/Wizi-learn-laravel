<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParrainageRequest;
use Illuminate\Http\Request;

class ParrainageRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ParrainageRequest::with(['parrain', 'filleul', 'catalogueFormation']);
        if ($request->filled('parrain')) {
            $query->whereHas('parrain', function($q) use ($request) {
                $q->where('prenom', 'like', '%' . $request->parrain . '%');
            });
        }
        if ($request->filled('filleul')) {
            $query->whereHas('filleul', function($q) use ($request) {
                $q->where('prenom', 'like', '%' . $request->filleul . '%');
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
        return view('admin.parrainage_requests.index', compact('requests'));
    }

    public function updateStatus($id)
    {
        $request = ParrainageRequest::findOrFail($id);
        $request->status = request('status');
        $request->save();
        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    public function exportCsv(Request $request)
    {
        $requests = ParrainageRequest::with(['parrain', 'filleul', 'catalogueFormation'])->get();
        $csv = "ID,Parrain,Filleul,Formation,Statut,Date\n";
        foreach ($requests as $req) {
            $csv .= "{$req->id},\"{$req->parrain?->prenom}\",\"{$req->filleul?->prenom}\",\"{$req->catalogueFormation?->titre}\",{$req->status},{$req->created_at}\n";
        }
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=parrainage_requests.csv');
    }
} 