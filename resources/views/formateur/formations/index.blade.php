@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-graduation-cap mr-2"></i>Mes Formations Assignées
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light">
                            {{ $formations->count() }} formation(s)
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if($formations->count() > 0)
                    <div class="row">
                        @foreach($formations as $formation)
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="card formation-card shadow-sm border-0">
                                <div class="card-header bg-white pb-0 border-bottom-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="formation-icon">
                                            <i class="fas fa-book-open text-primary fa-2x"></i>
                                        </div>
                                        <div class="formation-status">
                                            <span class="badge badge-{{ $formation->statut ? 'success' : 'secondary' }} badge-pill">
                                                {{ $formation->statut ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body pt-0">
                                    <h5 class="card-title text-dark font-weight-bold mb-2 formation-title">
                                        {{ $formation->titre }}
                                    </h5>
                                    
                                    @if($formation->description)
                                    <p class="card-text text-muted formation-description">
                                        {!! Str::limit(strip_tags($formation->description), 120) !!}
                                    </p>
                                    @endif
                                    
                                    <div class="formation-meta mb-3">
                                        @if($formation->duree)
                                        <div class="meta-item d-flex align-items-center mb-1">
                                            <i class="fas fa-clock text-info mr-2"></i>
                                            <small class="text-muted">{{ $formation->duree }} heures</small>
                                        </div>
                                        @endif
                                        
                                        @if($formation->certification)
                                        <div class="meta-item d-flex align-items-center mb-1">
                                            <i class="fas fa-award text-warning mr-2"></i>
                                            <small class="text-muted">Certifiante</small>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="formation-stats">
                                        <div class="progress mb-2" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ min(($formation->stagiaires_count / 10) * 100, 100) }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fas fa-users mr-1"></i>
                                                {{ $formation->stagiaires_count }} stagiaire(s)
                                            </span>
                                            <small class="text-muted">
                                                @if($formation->stagiaires_count == 0)
                                                    Aucun inscrit
                                                @elseif($formation->stagiaires_count <= 3)
                                                    Faible affluence
                                                @elseif($formation->stagiaires_count <= 7)
                                                    Bonne affluence
                                                @else
                                                    Forte affluence
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-white border-top-0 pt-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('formateur.formations.show', $formation->id) }}" 
                                           class="btn btn-primary btn-sm rounded-pill px-3">
                                            <i class="fas fa-eye mr-1"></i> Voir détails
                                        </a>
                                        
                                        @if($formation->cursus_pdf)
                                        <a href="{{ $formation->cursus_pdf_url }}" 
                                           target="_blank" 
                                           class="btn btn-outline-info btn-sm rounded-pill px-3"
                                           title="Télécharger le cursus">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Statistiques globales -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-center mb-4">
                                        <i class="fas fa-chart-pie text-primary mr-2"></i>Vue d'ensemble
                                    </h5>
                                    <div class="row text-center">
                                        <div class="col-md-3 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-primary font-weight-bold h4">
                                                    {{ $formations->count() }}
                                                </div>
                                                <div class="stat-label text-muted">Formations totales</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-success font-weight-bold h4">
                                                    {{ $formations->where('statut', true)->count() }}
                                                </div>
                                                <div class="stat-label text-muted">Formations actives</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-info font-weight-bold h4">
                                                    {{ $formations->sum('stagiaires_count') }}
                                                </div>
                                                <div class="stat-label text-muted">Stagiaires total</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-warning font-weight-bold h4">
                                                    {{ $formations->where('certification', true)->count() }}
                                                </div>
                                                <div class="stat-label text-muted">Formations certifiantes</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- État vide -->
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-book-open fa-4x text-muted mb-4"></i>
                            <h3 class="text-muted">Aucune formation assignée</h3>
                            <p class="text-muted mb-4">Vous n'avez pas encore de formations assignées à votre compte.</p>
                            <div class="empty-state-actions">
                                <button class="btn btn-primary" disabled>
                                    <i class="fas fa-sync-alt mr-2"></i>Actualiser
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                @if($formations->count() > 0)
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-info-circle mr-1"></i>
                                Dernière mise à jour : {{ now()->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                        <div>
                            <small class="text-muted">
                                Affichage de {{ $formations->count() }} formation(s)
                            </small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.formation-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.formation-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.formation-card .card-header {
    padding: 1.5rem 1.5rem 0;
}

.formation-card .card-body {
    padding: 1rem 1.5rem;
}

.formation-card .card-footer {
    padding: 0 1.5rem 1.5rem;
}

.formation-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white !important;
}

.formation-icon i {
    color: white !important;
}

.formation-title {
    font-size: 1.1rem;
    line-height: 1.4;
    min-height: 3rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.formation-description {
    font-size: 0.9rem;
    line-height: 1.5;
    min-height: 4rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.formation-meta .meta-item {
    font-size: 0.85rem;
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    transition: width 1s ease-in-out;
}

.stat-item {
    padding: 1rem;
}

.stat-number {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.empty-state {
    max-width: 400px;
    margin: 0 auto;
}

.empty-state i {
    opacity: 0.5;
}

.badge-pill {
    border-radius: 50px;
    padding: 0.5em 0.8em;
}

.btn-rounded {
    border-radius: 50px;
}

/* Animation au chargement */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.formation-card {
    animation: fadeInUp 0.5s ease-out;
}

.formation-card:nth-child(1) { animation-delay: 0.1s; }
.formation-card:nth-child(2) { animation-delay: 0.2s; }
.formation-card:nth-child(3) { animation-delay: 0.3s; }
.formation-card:nth-child(4) { animation-delay: 0.4s; }
.formation-card:nth-child(5) { animation-delay: 0.5s; }
.formation-card:nth-child(6) { animation-delay: 0.6s; }

/* Responsive */
@media (max-width: 768px) {
    .formation-card {
        margin-bottom: 1.5rem;
    }
    
    .formation-title {
        font-size: 1rem;
        min-height: auto;
    }
    
    .formation-description {
        min-height: auto;
        -webkit-line-clamp: 2;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Animation des barres de progression
    $('.progress-bar').each(function() {
        var width = $(this).attr('style');
        if (width) {
            $(this).css('width', '0%').animate({
                width: width
            }, 1500, 'easeOutQuart');
        }
    });
    
    // Tooltip pour les boutons
    $('[title]').tooltip();
    
    // Effet de hover amélioré
    $('.formation-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );
});
</script>
@endpush