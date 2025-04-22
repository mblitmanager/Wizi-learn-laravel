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

        $this->parametreService->update($id, $data);

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
