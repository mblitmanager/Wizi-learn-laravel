@extends('admin.layout')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">

            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Création d'un
                                partenaire</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('partenaires.index') }}" type="button" class="btn btn-sm btn-primary"><i
                                class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <div class="card">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card-body p-4 border rounded">
                    <div class="px-4 py-3"
                        style="box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;">
                        <form action="{{ route('partenaires.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="identifiant" class="form-label">Identifiant</label>
                                <input type="text" name="identifiant" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" name="adresse" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" name="ville" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="departement" class="form-label">Département</label>
                                <input type="text" name="departement" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="code_postal" class="form-label">Code postal</label>
                                <input type="text" name="code_postal" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <input type="text" name="type" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Contacts (jusqu'à 3)</label>
                                @for ($i = 0; $i < 3; $i++)
                                    <div class="border rounded p-3 mb-2">
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <input type="text" name="contacts[{{ $i }}][nom]"
                                                    class="form-control" placeholder="Nom">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="contacts[{{ $i }}][prenom]"
                                                    class="form-control" placeholder="Prénom">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="contacts[{{ $i }}][fonction]"
                                                    class="form-control" placeholder="Fonction">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="email" name="contacts[{{ $i }}][email]"
                                                    class="form-control" placeholder="Email">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <input type="text" name="contacts[{{ $i }}][tel]"
                                                    class="form-control" placeholder="Téléphone">
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="mb-3">
                                <label for="stagiaires" class="form-label">Stagiaires associés</label>
                                <select name="stagiaires[]" class="form-control select2" multiple>
                                    @foreach ($stagiaires as $stagiaire)
                                        <option value="{{ $stagiaire->id }}">{{ $stagiaire->prenom }}
                                            {{ $stagiaire->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Créer</button>
                            <a href="{{ route('partenaires.index') }}" class="btn btn-secondary">Annuler</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Stagiaires associés",
                allowClear: true
            });
        });
    </script>
@endsection
