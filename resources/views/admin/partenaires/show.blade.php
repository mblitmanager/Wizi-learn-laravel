@extends('admin.layout')
@section('title', 'Détails du partenaire')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center ">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('partenaires.index') }}"><i
                                        class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active text-uppercase " aria-current="page">Détails du partenaire
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('partenaires.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                                class="fadeIn animated bx bx-log-out"></i> Retour</a>
                        <a href="{{ route('partenaires.edit', $partenaire->id) }}" type="button"
                            class="btn btn-sm btn-warning px-4 ms-2"> <i class="bx bx-edit"></i> Modifier</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="main-body">
                            <div class="text-center mb-4">
                                @if ($partenaire->logo)
                                    <img src="{{ asset($partenaire->logo) }}" class="rounded shadow" width="120"
                                        height="120" alt="Logo" style="object-fit: contain">
                                @endif
                                <h3 class="mt-3 mb-1">{{ $partenaire->identifiant }}</h3>
                                <span class="badge bg-info text-dark px-3 py-1">{{ ucfirst($partenaire->type) }}</span>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4">Adresse :</label>
                                <div class="col-sm-8">{{ $partenaire->adresse }}</div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4">Ville :</label>
                                <div class="col-sm-8">{{ $partenaire->ville }}</div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4">Département :</label>
                                <div class="col-sm-8">{{ $partenaire->departement }}</div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4">Code postal :</label>
                                <div class="col-sm-8">{{ $partenaire->code_postal }}</div>
                            </div>
                            <hr>
                            <div class="container my-4">
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header text-dark">
                                        <h5 class="mb-0"><i class="bx bx-user me-2"></i> Contacts</h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $contacts = [];
                                            if (!empty($partenaire->contacts)) {
                                                $contactsData = is_array($partenaire->contacts)
                                                    ? $partenaire->contacts
                                                    : json_decode($partenaire->contacts, true);

                                                // Si c'est un tableau avec des contacts fusionnés (ancien format)
    if (
        isset($contactsData[0]['nom']) &&
        strpos($contactsData[0]['nom'], "\n") !== false
    ) {
        // Reconstruire les contacts correctement
        $noms = explode("\n", $contactsData[0]['nom'] ?? '');
        $prenoms = explode("\n", $contactsData[0]['prenom'] ?? '');
        $fonctions = explode("\n", $contactsData[0]['fonction'] ?? '');

        $emails = $contactsData[0]['emails'] ?? [];
        $telephones = $contactsData[0]['telephones'] ?? [];

        for (
            $i = 0;
            $i < max(count($noms), count($prenoms), count($fonctions));
            $i++
        ) {
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
                                                        if (
                                                            isset($contact['telephones']) &&
                                                            is_array($contact['telephones'])
                                                        ) {
                                                            $processedContact['tel'] = implode(
                                                                ', ',
                                                                array_filter($contact['telephones']),
                                                            );
                                                        } else {
                                                            $processedContact['tel'] =
                                                                $contact['tel'] ?? ($contact['telephone'] ?? '');
                                                        }

                                                        $contacts[] = $processedContact;
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if (count($contacts))
                                            <div class="row">
                                                @foreach ($contacts as $c)
                                                    <div class="col-md-4 mb-3">
                                                        <div class="border rounded p-3 h-100">
                                                            <div class="fw-bold">
                                                                {{ trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) }}
                                                            </div>

                                                            @if (!empty($c['fonction']))
                                                                <div class="text-muted small">{{ $c['fonction'] }}</div>
                                                            @endif

                                                            @if (!empty($c['email']))
                                                                <div class="mt-2">
                                                                    <i class="bx bx-at"></i>
                                                                    @foreach (explode(',', $c['email']) as $email)
                                                                        @if (!empty(trim($email)))
                                                                            <div class="d-inline-block">
                                                                                <a href="mailto:{{ trim($email) }}"
                                                                                    class="text-decoration-none">
                                                                                    {{ trim($email) }}
                                                                                </a>
                                                                            </div>
                                                                            @if (!$loop->last)
                                                                                ,
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @endif

                                                            @if (!empty($c['tel']))
                                                                <div class="mt-2">
                                                                    <i class="bx bx-phone"></i>
                                                                    @foreach (explode(',', $c['tel']) as $tel)
                                                                        @if (!empty(trim($tel)))
                                                                            <div class="d-inline-block">
                                                                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', trim($tel)) }}"
                                                                                    class="text-decoration-none">
                                                                                    {{ trim($tel) }}
                                                                                </a>
                                                                            </div>
                                                                            @if (!$loop->last)
                                                                                ,
                                                                            @endif
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">Aucun contact</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="container my-4">
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header text-dark">
                                        <h5 class="mb-0"><i class="bx bx-group me-2"></i> Stagiaires Associés</h5>
                                    </div>
                                    <div class="card-body" style="box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;">
                                        @if ($partenaire->stagiaires->isNotEmpty())
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Prénom</th>
                                                        <th>Email</th>
                                                        <th>Téléphone</th>
                                                        <th>Ville</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($partenaire->stagiaires as $stagiaire)
                                                        <tr>
                                                            <td>{{ strtoupper($stagiaire->user->name ?? $stagiaire->nom) }}
                                                            </td>
                                                            <td>{{ $stagiaire->prenom }}</td>
                                                            <td>{{ $stagiaire->user->email ?? '-' }}</td>
                                                            <td>{{ $stagiaire->telephone ?? '-' }}</td>
                                                            <td>{{ $stagiaire->ville }}</td>
                                                            <td>
                                                                <a href="{{ route('stagiaires.show', $stagiaire->id) }}"
                                                                    class="btn btn-sm btn-info">Voir</a>
                                                                <a href="{{ route('stagiaires.edit', $stagiaire->id) }}"
                                                                    class="btn btn-sm btn-warning">Modifier</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="alert alert-warning mt-4">
                                                <strong>Aucun stagiaire associé à ce partenaire.</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
