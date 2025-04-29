@extends('admin.layout')
@section('content')
    <div class="container">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center ">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Gestion des
                                Question</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('quiz.show', $question->quiz_id) }}" type="button"
                            class="btn btn-sm btn-primary px-4"> <i class="fadeIn animated bx bx-log-out"></i> Retour</a>
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

            <div class="card  border-light mb-4">
                <div class="card-body">
                    <h3 class="card-title text-dark">
                        <i class="fas fa-question-circle me-2"></i> Question :
                    </h3>
                    <p class="card-text text-muted">{{ $question->text }}</p>
                </div>
            </div>

            <!-- Card for Answer List -->
            <div class="card border-light">
                <div class="card-body">
                    <h4 class="text-dark mb-4">
                        <i class="fas fa-check-circle me-2"></i> Liste des réponses
                    </h4>
                    <ul class="list-group list-group-flush">
                        @foreach ($question->reponses as $row)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <!-- Icon and Answer Text -->
                                    <i
                                        class="fas {{ $row->is_correct ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-3"></i>
                                    <span class="text-muted">{{ $row->text }}</span>
                                </div>
                                <!-- Badge for Correctness -->
                                <span class="badge rounded-pill {{ $row->is_correct ? 'bg-success' : 'bg-danger' }}">
                                    {{ $row->is_correct == 1 ? 'Correct' : 'Incorrect' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>



    </div>
@endsection
@section('scripts')
@endsection
