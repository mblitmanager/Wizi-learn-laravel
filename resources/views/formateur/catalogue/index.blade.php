@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book mx-2"></i>Catalogue Complet des Formations
                    </h5>
                    <div class="card-tools">
                        <span class="badge bg-primary text-white">
                            {{ $formations->count() }} formation(s) disponible(s)
                        </span>
                    </div>
                </div>
                <div class="card-body">

                    @if($formations->count() > 0)
                    <div class="row" id="formationsContainer">
                        @foreach($formations as $formation)
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4 formation-item" 
                             data-title="{{ strtolower($formation->titre) }}"
                             data-status="{{ $formation->statut ? 'active' : 'inactive' }}"
                             data-certification="{{ $formation->certification ? 'yes' : 'no' }}">
                            <div class="card formation-card shadow-sm border-0 h-100">
                                <div class="card-header bg-white pb-0 border-bottom-0 position-relative">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="formation-icon">
                                            <i class="fas fa-graduation-cap text-info fa-2x"></i>
                                        </div>
                                        <div class="formation-badges">
                                            @if($formation->statut)
                                                <span class="badge badge-success badge-pill mb-1">Active</span>
                                            @else
                                                <span class="badge badge-secondary badge-pill mb-1">Inactive</span>
                                            @endif
                                            @if($formation->certification)
                                                <span class="badge badge-warning badge-pill">Certifiante</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Indicateur si la formation est assignée au formateur -->
                                    @if($formation->formateurs->contains(auth()->user()->formateur))
                                    <div class="position-absolute top-0 right-0 mt-2 mr-2">
                                        <span class="badge badge-primary" title="Vous êtes assigné à cette formation">
                                            <i class="fas fa-check-circle"></i> Assignée
                                        </span>
                                    </div>
                                    @endif
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
                                        
                                        @if($formation->tarif)
                                        <div class="meta-item d-flex align-items-center mb-1">
                                            <i class="fas fa-euro-sign text-success mr-2"></i>
                                            <small class="text-muted">{{ number_format($formation->tarif, 2, ',', ' ') }} €</small>
                                        </div>
                                        @endif
                                        
                                        @if($formation->prerequis)
                                        <div class="meta-item d-flex align-items-center mb-1">
                                            <i class="fas fa-list-alt text-warning mr-2"></i>
                                            <small class="text-muted">Prérequis: {{ Str::limit($formation->prerequis, 30) }}</small>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Statistiques pour le formateur connecté -->
                                    <div class="formation-stats">
                                        @php
                                            $mesStagiairesCount = $formation->stagiaires()
                                                ->whereHas('formateurs', function($q) {
                                                    $q->where('formateur_id', auth()->user()->formateur->id);
                                                })
                                                ->count();
                                        @endphp
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge badge-info badge-pill">
                                                <i class="fas fa-users mr-1"></i>
                                                {{ $mesStagiairesCount }} de vos stagiaires
                                            </span>
                                            <small class="text-muted">
                                                {{ $formation->stagiaires->count() }} au total
                                            </small>
                                        </div>
                                        
                                        @if($mesStagiairesCount > 0)
                                        <div class="progress mb-2" style="height: 6px;">
                                            <div class="progress-bar bg-info" 
                                                 style="width: {{ min(($mesStagiairesCount / max($formation->stagiaires->count(), 1)) * 100, 100) }}%"
                                                 title="{{ $mesStagiairesCount }} de vos stagiaires sur {{ $formation->stagiaires->count() }} total">
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                             
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Statistiques globales du catalogue -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-center mb-4">
                                        <i class="fas fa-chart-bar text-info mr-2"></i>Statistiques du Catalogue
                                    </h5>
                                    <div class="row text-center">
                                        <div class="col-md-2 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-info font-weight-bold h4">
                                                    {{ $formations->count() }}
                                                </div>
                                                <div class="stat-label text-muted">Total formations</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-success font-weight-bold h4">
                                                    {{ $formations->where('statut', true)->count() }}
                                                </div>
                                                <div class="stat-label text-muted">Actives</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-warning font-weight-bold h4">
                                                    {{ $formations->where('certification', true)->count() }}
                                                </div>
                                                <div class="stat-label text-muted">Certifiantes</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-primary font-weight-bold h4">
                                                    {{ $mesFormationsCount }}
                                                </div>
                                                <div class="stat-label text-muted">Mes formations</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-secondary font-weight-bold h4">
                                                    {{ $formations->sum('stagiaires_count') }}
                                                </div>
                                                <div class="stat-label text-muted">Stagiaires total</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <div class="stat-item">
                                                <div class="stat-number text-dark font-weight-bold h4">
                                                    {{ $totalStagiairesMesFormations }}
                                                </div>
                                                <div class="stat-label text-muted">Mes stagiaires</div>
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
                            <i class="fas fa-book fa-4x text-muted mb-4"></i>
                            <h3 class="text-muted">Aucune formation disponible</h3>
                            <p class="text-muted mb-4">Le catalogue de formations est actuellement vide.</p>
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
                                Catalogue mis à jour le : {{ now()->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                        <div>
                            <a href="/formateur/formations" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Voir mes formations
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour demande d'assignation -->
<div class="modal fade" id="requestAssignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-hand-paper mr-2"></i>Demande d'Assignation
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous demander à être assigné à la formation :</p>
                <p class="font-weight-bold" id="formationTitle"></p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle mr-1"></i>
                    Votre demande sera soumise à l'administrateur pour validation.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-info" id="confirmRequest">
                    <i class="fas fa-paper-plane mr-1"></i> Envoyer la demande
                </button>
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
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.formation-badges {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.formation-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #17a2b8, #138496);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white !important;
}

.formation-assigned {
    border-left: 4px solid #28a745;
}

.formation-not-assigned {
    border-left: 4px solid #6c757d;
}

/* Animation pour les filtres */
.formation-item {
    transition: all 0.3s ease;
}

.formation-item.hidden {
    display: none;
}

/* Styles responsives */
@media (max-width: 768px) {
    .formation-badges {
        flex-direction: row;
        gap: 5px;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let currentFormationId = null;
    
    // Filtrage en temps réel
    $('#searchInput, #filterStatus').on('input change', function() {
        filterFormations();
    });
    
    function filterFormations() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const statusFilter = $('#filterStatus').val();
        
        $('.formation-item').each(function() {
            const title = $(this).data('title');
            const status = $(this).data('status');
            const matchesSearch = title.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            
            if (matchesSearch && matchesStatus) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    }
    
    // Gestion des demandes d'assignation
    $('.request-assignment').on('click', function() {
        currentFormationId = $(this).data('formation-id');
        const formationTitle = $(this).data('formation-title');
        
        $('#formationTitle').text(formationTitle);
        $('#requestAssignmentModal').modal('show');
    });
    
    $('#confirmRequest').on('click', function() {
        if (!currentFormationId) return;
        
        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Envoi...');
        
        // Simulation d'envoi - À adapter avec votre logique backend
        setTimeout(() => {
            $('#requestAssignmentModal').modal('hide');
            showToast('success', 'Demande envoyée avec succès !');
            $btn.prop('disabled', false).html(originalText);
            
            // Mettre à jour l'interface
            $(`.request-assignment[data-formation-id="${currentFormationId}"]`)
                .replaceWith('<span class="badge badge-warning">Demande envoyée</span>');
                
            currentFormationId = null;
        }, 1500);
    });
    
    function showToast(type, message) {
        // Implémentez votre système de toast ici
        alert(message); // Solution temporaire
    }
    
    // Animation des barres de progression
    $('.progress-bar').each(function() {
        const width = $(this).attr('style');
        if (width) {
            $(this).css('width', '0%').animate({
                width: width
            }, 1000, 'easeOutQuart');
        }
    });
});
</script>
@endpush