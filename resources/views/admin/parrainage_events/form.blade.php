@csrf
<div class="mb-3">
    <label class="form-label">Titre</label>
    <input type="text" name="titre" value="{{ old('titre', $parrainageEvent->titre ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Prix</label>
    <input type="number" step="0.01" name="prix" value="{{ old('prix', $parrainageEvent->prix ?? '') }}" class="form-control" required>
</div>
<div class="mb-3">
    <label class="form-label">Status</label>
    <select class="form-control" name="status" id="status">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Date Début</label>
    <input type="date" name="date_debut" value="{{ old('date_debut', $parrainageEvent->date_debut ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Date Fin</label>
    <input type="date" name="date_fin" value="{{ old('date_fin', $parrainageEvent->date_fin ?? '') }}" class="form-control" required>
</div>

<button type="submit" class="btn btn-success">Enregistrer</button>
