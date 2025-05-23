@extends('admin.layout')

@section('title', 'Réinitialisation des données')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-center">
                            <div><i class="bx bx-reset me-1 font-22 text-primary"></i></div>
                            <h5 class="mb-0 text-primary">Réinitialisation des données</h5>
                        </div>
                        <hr>
                        <div class="alert alert-warning">
                            <h4 class="alert-heading">Attention !</h4>
                            <p>Veuillez sélectionner les données que vous souhaitez réinitialiser :</p>
                            <form action="{{ route('admin.parametre.reset-data') }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir réinitialiser les données sélectionnées ? Cette action est irréversible.');">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="reset_data[]" value="classements" id="resetClassements">
                                            <label class="form-check-label" for="resetClassements">
                                                Classements
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="reset_data[]" value="participations" id="resetParticipations">
                                            <label class="form-check-label" for="resetParticipations">
                                                Participations aux quiz
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="reset_data[]" value="reponses" id="resetReponses">
                                            <label class="form-check-label" for="resetReponses">
                                                Réponses aux quiz
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="reset_data[]" value="progression" id="resetProgression">
                                            <label class="form-check-label" for="resetProgression">
                                                Progression des stagiaires
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="confirmReset" required>
                                            <label class="form-check-label" for="confirmReset">
                                                Je confirme vouloir réinitialiser les données sélectionnées
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bx bx-reset me-1"></i> Réinitialiser les données sélectionnées
                                        </button>
                                        <a href="{{ route('parametre.index') }}" class="btn btn-secondary">
                                            <i class="bx bx-x me-1"></i> Annuler
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
