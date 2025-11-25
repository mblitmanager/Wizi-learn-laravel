@extends('admin.layout')
@section('title', 'Détails du partenaire')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-building me-2"></i>Détails du partenaire
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('partenaires.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Partenaires
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $partenaire->identifiant }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('partenaires.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                        <a href="{{ route('partenaires.edit', $partenaire->id) }}" class="btn btn-warning ms-2">
                            <i class="bx bx-edit me-1"></i> Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Carte principale des informations -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            @if ($partenaire->logo)
                                <img src="{{ asset($partenaire->logo) }}" class="rounded-circle shadow" width="120"
                                    height="120" alt="Logo" style="object-fit: cover">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                    style="width:120px;height:120px;font-weight:bold;font-size:24px;">
                                    {{ strtoupper(substr($partenaire->identifiant, 0, 2)) }}
                                </div>
                            @endif
                            <h3 class="mt-3 mb-2 text-dark fw-bold">{{ $partenaire->identifiant }}</h3>
                            <span class="badge bg-primary px-3 py-2 fs-6">{{ ucfirst($partenaire->type) }}</span>
                        </div>

                        <!-- Informations du partenaire -->
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-info-circle me-2"></i>Informations générales
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Adresse :</div>
                                            <div class="col-sm-8">{{ $partenaire->adresse }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Ville :</div>
                                            <div class="col-sm-8">{{ $partenaire->ville }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Département :</div>
                                            <div class="col-sm-8">{{ $partenaire->departement }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Code postal :</div>
                                            <div class="col-sm-8">{{ $partenaire->code_postal }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Contacts -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-group me-2"></i>Contacts
                            <span class="badge bg-info text-dark ms-2">
                                @php
                                    $contactsCount = 0;
                                    if (!empty($partenaire->contacts)) {
                                        $contactsData = is_array($partenaire->contacts)
                                            ? $partenaire->contacts
                                            : json_decode($partenaire->contacts, true);
                                        $contactsCount = count($contactsData);
                                    }
                                @endphp
                                {{ $contactsCount }}
                            </span>
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $contacts = [];
                            if (!empty($partenaire->contacts)) {
                                $contactsData = is_array($partenaire->contacts)
                                    ? $partenaire->contacts
                                    : json_decode($partenaire->contacts, true);

                                // Si c'est un tableau avec des contacts fusionnés (ancien format)
    if (isset($contactsData[0]['nom']) && strpos($contactsData[0]['nom'], "\n") !== false) {
        // Reconstruire les contacts correctement
        $noms = explode("\n", $contactsData[0]['nom'] ?? '');
        $prenoms = explode("\n", $contactsData[0]['prenom'] ?? '');
        $fonctions = explode("\n", $contactsData[0]['fonction'] ?? '');

        $emails = $contactsData[0]['emails'] ?? [];
        $telephones = $contactsData[0]['telephones'] ?? [];

        for ($i = 0; $i < max(count($noms), count($prenoms), count($fonctions)); $i++) {
            $contact = [
                'nom' => trim($noms[$i] ?? ''),
                'prenom' => trim($prenoms[$i] ?? ''),
                'fonction' => trim($fonctions[$i] ?? ''),
                'email' => $emails[$i] ?? '',
                'tel' => $telephones[$i] ?? '',
            ];

            if (!empty($contact['nom']) || !empty($contact['prenom'])) {
                $contacts[] = $contact;
            }
        }
    } else {
        // Format normal - traiter chaque contact individuellement
        foreach ($contactsData as $contact) {
            // Gérer l'ancien format (email/tel) et le nouveau format (emails/telephones)
                                        $processedContact = [
                                            'nom' => $contact['nom'] ?? '',
                                            'prenom' => $contact['prenom'] ?? '',
                                            'fonction' => $contact['fonction'] ?? '',
                                        ];

                                        // Gérer les emails (ancien et nouveau format)
                                        if (isset($contact['emails']) && is_array($contact['emails'])) {
                                            $processedContact['email'] = implode(
                                                ', ',
                                                array_filter($contact['emails']),
                                            );
                                        } else {
                                            $processedContact['email'] = $contact['email'] ?? '';
                                        }

                                        // Gérer les téléphones (ancien et nouveau format)
                                        if (isset($contact['telephones']) && is_array($contact['telephones'])) {
                                            $processedContact['tel'] = implode(
                                                ', ',
                                                array_filter($contact['telephones']),
                                            );
                                        } else {
                                            $processedContact['tel'] = $contact['tel'] ?? ($contact['telephone'] ?? '');
                                        }

                                        $contacts[] = $processedContact;
                                    }
                                }
                            }
                        @endphp

                        @if (count($contacts))
                            <div class="row">
                                @foreach ($contacts as $index => $c)
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card border shadow-sm h-100">
                                            <div class="card-header bg-white py-3">
                                                <h6 class="mb-0 fw-semibold text-dark">
                                                    <i class="bx bx-user-circle me-2"></i>
                                                    Contact {{ $index + 1 }}
                                                    @if (!empty($c['fonction']))
                                                        <span
                                                            class="badge bg-light text-dark ms-2 small">{{ $c['fonction'] }}</span>
                                                    @endif
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @if (!empty($c['nom']) || !empty($c['prenom']))
                                                    <div class="mb-3">
                                                        <div class="fw-bold text-dark fs-6">
                                                            {{ $c['prenom'] }} {{ $c['nom'] }}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($c['email']))
                                                    <div class="mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bx bx-envelope text-primary me-2"></i>
                                                            <div class="small">
                                                                @foreach (explode(',', $c['email']) as $email)
                                                                    @if (!empty(trim($email)))
                                                                        <div>
                                                                            <a href="mailto:{{ trim($email) }}"
                                                                                class="text-decoration-none text-dark">
                                                                                {{ trim($email) }}
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($c['tel']))
                                                    <div class="mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bx bx-phone text-success me-2"></i>
                                                            <div class="small">
                                                                @foreach (explode(',', $c['tel']) as $tel)
                                                                    @if (!empty(trim($tel)))
                                                                        <div>
                                                                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', trim($tel)) }}"
                                                                                class="text-decoration-none text-dark">
                                                                                {{ trim($tel) }}
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-user-x fs-1 text-muted mb-3"></i>
                                <p class="text-muted mb-0">Aucun contact enregistré</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Section Stagiaires -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-user me-2"></i>Stagiaires associés
                            <span class="badge bg-info text-dark ms-2">{{ $partenaire->stagiaires->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($partenaire->stagiaires->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">Nom & Prénom</th>
                                            <th class="border-0">Email</th>
                                            <th class="border-0">Téléphone</th>
                                            <th class="border-0">Ville</th>
                                            <th class="border-0 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($partenaire->stagiaires as $stagiaire)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark">
                                                        {{ strtoupper($stagiaire->user->name ?? $stagiaire->nom) }}
                                                        {{ $stagiaire->prenom }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($stagiaire->user->email ?? false)
                                                        <a href="mailto:{{ $stagiaire->user->email }}"
                                                            class="text-decoration-none text-dark">
                                                            {{ $stagiaire->user->email }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($stagiaire->telephone)
                                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $stagiaire->telephone) }}"
                                                            class="text-decoration-none text-dark">
                                                            {{ $stagiaire->telephone }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $stagiaire->ville ?? '-' }}</td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('stagiaires.show', $stagiaire->id) }}"
                                                            class="btn btn-info text-white">
                                                            <i class="bx bx-show me-1"></i>Voir
                                                        </a>
                                                        <a href="{{ route('stagiaires.edit', $stagiaire->id) }}"
                                                            class="btn btn-warning">
                                                            <i class="bx bx-edit me-1"></i>Modifier
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-user-plus fs-1 text-muted mb-3"></i>
                                <p class="text-muted mb-0">Aucun stagiaire associé à ce partenaire</p>
                                <a href="{{ route('partenaires.edit', $partenaire->id) }}" class="btn btn-primary mt-3">
                                    <i class="bx bx-link me-1"></i>Associer des stagiaires
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

<style>
    .card {
        border-radius: 12px;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .badge {
        border-radius: 6px;
        font-weight: 500;
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.04) !important;
    }

    /* Style pour les cartes de contact */
    .card .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }
</style>
