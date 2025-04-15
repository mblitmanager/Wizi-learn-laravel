@extends('admin.layout')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Gestion Question</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('quiz.show', $question->quiz_id) }}" type="button" class="btn btn-sm btn-primary px-4"> <i
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
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <h3>Question : </h3>{{ $question->text }}
                </li>
            </ul>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="text-wizi">
                    Liste des reponses
                </h4>
                @foreach ($question->reponses as $row)
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center"> {{$row->text}}
                            @if ($row->is_correct == 1)
                                <span class="badge bg-success rounded-pill">Correct</span>
                            @else
                                <span class="badge bg-danger rounded-pill">Incorrect</span>
                            @endif
                        </li>
                    </ul>
                @endforeach

            </div>
        </div>
    </div>
@endsection