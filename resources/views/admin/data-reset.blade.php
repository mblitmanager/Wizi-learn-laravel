@extends('admin.layout')

@section('title', 'Réinitialisation des données')

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Tableau de bord</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Réinitialisation des données</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 mx-auto">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Avertissement important</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger" role="alert">
                    <h6 class="alert-heading"><i class="fas fa-radiation me-2"></i>Action critique</h6>
                    <p>Cette opération va supprimer définitivement les données sélectionnées. Veuillez vérifier soigneusement votre sélection avant de continuer.</p>
                    <hr>
                    <p class="mb-0"><i class="fas fa-ban me-1"></i> Cette action est irréversible et ne peut pas être annulée.</p>
                </div>

                <form id="resetForm">
                    <h5 class="mb-3"><i class="fas fa-list-check me-2"></i>Sélection des données à réinitialiser</h5>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Progression et statistiques</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input data-checkbox" type="checkbox" value="classements" id="classements">
                                <label class="form-check-label" for="classements">
                                    <strong>Classements</strong> <span class="badge bg-light text-dark border">classements</span>
                                    <br><small class="text-muted">Supprime tous les scores et rangs des stagiaires</small>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input data-checkbox" type="checkbox" value="progressions" id="progressions">
                                <label class="form-check-label" for="progressions">
                                    <strong>Progression</strong> <span class="badge bg-light text-dark border">progressions</span>
                                    <br><small class="text-muted">Réinitialise l'avancement dans les leçons et modules</small>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input data-checkbox" type="checkbox" value="achievements" id="achievements">
                                <label class="form-check-label" for="achievements">
                                    <strong>Succès</strong> <span class="badge bg-light text-dark border">user_achievements</span>
                                    <br><small class="text-muted">Efface les succès débloqués par les utilisateurs</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input data-checkbox" type="checkbox" value="quiz_statistics" id="quiz_statistics">
                                <label class="form-check-label" for="quiz_statistics">
                                    <strong>Stats Globales Quiz</strong> <span class="badge bg-light text-dark border">quiz_statistics</span>
                                    <br><small class="text-muted">Réinitialise les moyennes et performances agrégées des quiz</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Détails des Participations</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <h6 class="small fw-bold text-uppercase mb-3">Quiz</h6>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="quiz_participations" id="quiz_participations">
                                        <label class="form-check-label" for="quiz_participations">
                                            <strong>Participations</strong> <span class="badge bg-light text-dark border">quiz_participations</span>
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="quiz_participation_answers" id="quiz_participation_answers">
                                        <label class="form-check-label" for="quiz_participation_answers">
                                            <strong>Réponses</strong> <span class="badge bg-light text-dark border">quiz_participation_answers</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4">
                                    <h6 class="small fw-bold text-uppercase mb-3">Formations</h6>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="participations" id="participations">
                                        <label class="form-check-label" for="participations">
                                            <strong>Participations</strong> <span class="badge bg-light text-dark border">participations</span>
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="participation_answers" id="participation_answers">
                                        <label class="form-check-label" for="participation_answers">
                                            <strong>Réponses</strong> <span class="badge bg-light text-dark border">participation_answers</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input data-checkbox" type="checkbox" value="media_stagiaire" id="media_stagiaire">
                                <label class="form-check-label" for="media_stagiaire">
                                    <strong>Historique Vidéo</strong> <span class="badge bg-light text-dark border">media_stagiaire</span>
                                    <br><small class="text-muted">Efface les données de visionnage des vidéos</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Logs, Sessions et Demandes</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="user_activity_log" id="user_activity_log">
                                        <label class="form-check-label" for="user_activity_log">
                                            <strong>Logs d'activité</strong> <span class="badge bg-light text-dark border">user_activity_log</span>
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="login_histories" id="login_histories">
                                        <label class="form-check-label" for="login_histories">
                                            <strong>Historique Login</strong> <span class="badge bg-light text-dark border">login_histories</span>
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="notification_history" id="notification_history">
                                        <label class="form-check-label" for="notification_history">
                                            <strong>Logs Notifications</strong> <span class="badge bg-light text-dark border">notification_history</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="user_app_usages" id="user_app_usages">
                                        <label class="form-check-label" for="user_app_usages">
                                            <strong>Usage Mobile</strong> <span class="badge bg-light text-dark border">user_app_usages</span>
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="user_client_sessions" id="user_client_sessions">
                                        <label class="form-check-label" for="user_client_sessions">
                                            <strong>Sessions Clients</strong> <span class="badge bg-light text-dark border">user_client_sessions</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input data-checkbox" type="checkbox" value="demande_inscriptions" id="demande_inscriptions">
                                        <label class="form-check-label" for="demande_inscriptions">
                                            <strong>Demandes Formation</strong> <span class="badge bg-light text-dark border">demande_inscriptions</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-check-double me-2"></i>Confirmation finale</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    <strong>Sélectionner toutes les données</strong>
                                </label>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmation" required>
                                <label class="form-check-label text-danger" for="confirmation">
                                    <strong>Je comprends que cette action est irréversible et je confirme vouloir réinitialiser les données sélectionnées</strong>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg" id="submitBtn" disabled>
                            <i class="fas fa-trash-alt me-2"></i>Réinitialiser les données sélectionnées
                        </button>
                    </div>
                </form>

                <div id="resultContainer" class="mt-4" style="display: none;">
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle me-2"></i>Données supprimées avec succès</h6>
                        <div id="resultDetails"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetForm');
    const selectAll = document.getElementById('selectAll');
    const confirmation = document.getElementById('confirmation');
    const submitBtn = document.getElementById('submitBtn');
    const checkboxes = document.querySelectorAll('.data-checkbox');

    // Select all functionality
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSubmitButton();
    });

    // Update select all state when individual checkboxes change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            selectAll.checked = [...checkboxes].every(c => c.checked);
            updateSubmitButton();
        });
    });

    // Update submit button state
    confirmation.addEventListener('change', updateSubmitButton);

    function updateSubmitButton() {
        const anyChecked = [...checkboxes].some(cb => cb.checked);
        submitBtn.disabled = !(anyChecked && confirmation.checked);
    }

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const selectedData = [...checkboxes]
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedData.length === 0) {
            alert('Veuillez sélectionner au moins un type de données');
            return;
        }

        if (!confirm('ATTENTION: Êtes-vous absolument certain de vouloir supprimer ces données ? Cette action est IRRÉVERSIBLE !')) {
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Suppression en cours...';

        try {
            const response = await fetch('{{ route("admin.data-reset.post") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    dataTypes: selectedData,
                    confirmation: true
                })
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById('resultContainer').style.display = 'block';
                let detailsHtml = '<ul class="mb-0">';
                for (const [key, value] of Object.entries(result.data.deleted)) {
                    detailsHtml += `<li><strong>${key}</strong>: ${value} enregistrements supprimés</li>`;
                }
                detailsHtml += '</ul>';
                document.getElementById('resultDetails').innerHTML = detailsHtml;

                // Reset form
                form.reset();
                updateSubmitButton();
            } else {
                alert('Erreur: ' + (result.message || 'Échec de la suppression'));
            }
        } catch (error) {
            alert('Erreur de connexion: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Réinitialiser les données sélectionnées';
        }
    });
});
</script>
@endsection
