<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ParametreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParametreAdminController extends Controller
{
    protected $parametreService;

    public function __construct(ParametreService $parametreService)
    {
        $this->parametreService = $parametreService;
    }

    /**
     * Affiche la liste des paramètres/utilisateurs.
     */
    public function index()
    {
        $users = $this->parametreService->list();
        return view('admin.parametre.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create()
    {
        return view('admin.parametre.create');
    }

    /**
     * Enregistre un nouvel utilisateur/paramètre.
     */
    public function store(Request $request)
    {
        $data = $request->only(['name', 'email', 'password', 'role']);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique' => 'Cette adresse e-mail est deja utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 6 caractères.',
            'role.required' => 'Le role est obligatoire.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
            $data['image'] = 'uploads/users/' . $imageName;
        }

        $data['password'] = bcrypt($data['password']);
        $this->parametreService->create($data);

        return redirect()->route('parametre.index')->with('success', 'Utilisateur créé avec succès');
    }

    /**
     * Affiche les détails d’un utilisateur.
     */
    public function show($id)
    {
        $user = $this->parametreService->find($id);
        return view('admin.parametre.show', compact('user'));
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit($id)
    {
        $user = $this->parametreService->find($id);
        return view('admin.parametre.edit', compact('user'));
    }

    /**
     * Met à jour un utilisateur.
     */

    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'email', 'password', 'role']);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Champs supplémentaires partagés
            'prenom' => 'nullable|string|max:255',
            'civilite' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'ville' => 'nullable|string|max:255',
            'code_postal' => 'nullable|string|max:10',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique' => 'Cette adresse e-mail est deja utilisée.',
            'password.min' => 'Le mot de passe doit avoir au moins 6 caractères.',
            'role.required' => 'Le role est obligatoire.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
            $data['image'] = 'uploads/users/' . $imageName;
        }

        // Update user
        $user = $this->parametreService->update($id, $data);

        // Selon le rôle sélectionné, créer ou mettre à jour le bon modèle
        switch ($request->role) {
            case 'stagiaire':
                $stagiaireData = $request->only([
                    'civilite',
                    'prenom',
                    'telephone',
                    'adresse',
                    'date_naissance',
                    'ville',
                    'code_postal'
                ]);
                $stagiaireData['user_id'] = $user->id;
                $stagiaireData['statut'] = '1';
                \App\Models\Stagiaire::updateOrCreate(['user_id' => $user->id], $stagiaireData);
                break;

            case 'formateur':
                \App\Models\Formateur::updateOrCreate(
                    ['user_id' => $user->id],
                    ['prenom' => $request->prenom, 'role' => 'formateur']
                );
                break;

            case 'commercial':
                \App\Models\Commercial::updateOrCreate(
                    ['user_id' => $user->id],
                    ['prenom' => $request->prenom, 'role' => 'commercial']
                );
                break;

            case 'pole_relation_client':
                \App\Models\PoleRelationClient::updateOrCreate(
                    ['user_id' => $user->id],
                    ['prenom' => $request->prenom, 'role' => 'pole_relation_client']
                );
                break;

            default:
                // Optionnel : gérer les autres cas
                break;
        }

        return redirect()->route('parametre.index')->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy($id)
    {
        $this->parametreService->delete($id);
        return redirect()->route('parametre.index')->with('success', 'Utilisateur supprimé avec succès');
    }

    public function updateImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::findOrFail($id);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
            $user->image = 'uploads/users/' . $imageName;
            $user->save();
        }

        return redirect()->back()->with('success', 'Image mise à jour avec succès.');
    }
}
