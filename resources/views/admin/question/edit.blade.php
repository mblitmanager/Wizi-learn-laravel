@extends('admin.layout')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Modifier la question</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('quiz.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                        class="fadeIn animated bx bx-log-out"></i> Retour</a>
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
                <div class="col-md-8 mx-auto">
                    <form action="{{ route('question.update', $question->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div>
                            <label>Texte de la question</label>
                            <input class="form-control" type="text" name="text" value="{{ $question->text }}" required>
                        </div>

                        <div>
                            <label>Type</label>
                            <input class="form-control" type="text" name="type" value="{{ $question->type }}" required>
                        </div>

                        <div>
                            <label>Points</label>
                            <input class="form-control" type="number" name="points" value="{{ $question->points }}"
                                required>
                        </div>

                        <div>
                            <label>Explication</label>
                            <textarea class="form-control" name="explication">{{ $question->explication }}</textarea>
                        </div>

                        <div>
                            <label>Astuce</label>
                            <textarea class="form-control" name="astuce">{{ $question->astuce }}</textarea>
                        </div>

                        <div>
                            <label>Media URL</label>
                            <input class="form-control" type="file" name="media_url" value="{{ $question->media_url }}">
                        </div>
                        <hr>
                       <div class="mt-2 py-2">
                        <h3>Réponses</h3>

                            @foreach($question->reponses as $reponse)
                            <div class="border p-3 mb-3 rounded bg-light">
                                <div class="mb-3">
                                    <label class="form-label">Réponse</label>
                                    <input 
                                        class="form-control" 
                                        type="text" 
                                        name="reponses[{{ $reponse->id }}][text]" 
                                        value="{{ $reponse->text }}" 
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label d-block">Correcte ?</label>
                                    
                                    <!-- Trick pour forcer l'envoi de la case même si décochée -->
                                    <input type="hidden" name="reponses[{{ $reponse->id }}][is_correct]" value="0">

                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="reponses[{{ $reponse->id }}][is_correct]" 
                                        value="1" 
                                        {{ $reponse->is_correct ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach


                       </div>
                        <button class="btn btn-sm btn-primary" type="submit">Mettre à jour</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection