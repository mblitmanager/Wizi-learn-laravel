@extends('admin.layout')

@section('title', 'Réinitialisation des données')

@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Réinitialisation
                                des données</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">

                        {{-- <a href="{{ route('parametre.create') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                                class="fadeIn animated bx bx-plus"></i> Nouveau utilisateur</a> --}}
                    </div>
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success border-0 bg-success alert-dismissible fade show">
                <div class="text-white"> {{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                <div class="text-white"> {{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="alert alert-warning">
                                <h4 class="alert-heading">Attention !</h4>
                                <p>Veuillez sélectionner les données que vous souhaitez réinitialiser :</p>
                                <form action="{{ route('admin.parametre.reset-data') }}" method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir réinitialiser les données sélectionnées ? Cette action est irréversible.');">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                    value="classements" id="resetClassements">
                                                <label class="form-check-label" for="resetClassements">
                                                    Classements
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                    value="participations" id="resetParticipations">
                                                <label class="form-check-label" for="resetParticipations">
                                                    Participations aux quiz
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                    value="reponses" id="resetReponses">
                                                <label class="form-check-label" for="resetReponses">
                                                    Réponses aux quiz
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                    value="quiz_history" id="resetQuizHistory">
                                                <label class="form-check-label" for="resetQuizHistory">
                                                    Historique des quiz joués
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                    value="progression" id="resetProgression">
                                                <label class="form-check-label" for="resetProgression">
                                                    Progression des stagiaires
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                    value="achievements" id="resetAchievements">
                                                <label class="form-check-label" for="resetAchievements">
                                                    Succès (achievements) et statistiques associées
                                                </label>
                                                <div class="form-text">
                                                    Efface les succès débloqués par les stagiaires et réinitialise les
                                                    statistiques liées (compteurs, récapitulatifs).
                                                </div>
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
