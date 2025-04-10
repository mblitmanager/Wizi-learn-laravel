@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Gestion stagiaire</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('stagiaires.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                        class="fadeIn animated bx bx-log-out"></i> Retour</a>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <div class="main-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-column align-items-center text-center">
                                            <div class="mt-3">
                                                <h4>{{ $stagiaire->user->name }}</h4>
                                                <p class="text-secondary mb-1">{{ $stagiaire->user->email }}</p>
                                                <p class="text-muted font-size-sm">Bay Area, San Francisco, CA</p>
                                                <button class="btn btn-primary">Follow</button>
                                                <button class="btn btn-outline-primary">Message</button>
                                            </div>
                                        </div>
                                        <hr class="my-4">

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Full Name</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control" value="John Doe">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Email</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control" value="john@example.com">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Phone</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control" value="(239) 816-9029">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Mobile</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control" value="(320) 380-4539">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Address</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control" value="Bay Area, San Francisco, CA">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="button" class="btn btn-primary px-4" value="Save Changes">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="d-flex align-items-center mb-3">Project Status</h5>
                                                <p>Web Design</p>
                                                <div class="progress mb-3" style="height: 5px">
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                        style="width: 80%" aria-valuenow="80" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <p>Website Markup</p>
                                                <div class="progress mb-3" style="height: 5px">
                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                        style="width: 72%" aria-valuenow="72" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <p>One Page</p>
                                                <div class="progress mb-3" style="height: 5px">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: 89%" aria-valuenow="89" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <p>Mobile Template</p>
                                                <div class="progress mb-3" style="height: 5px">
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: 55%" aria-valuenow="55" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <p>Backend API</p>
                                                <div class="progress" style="height: 5px">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 66%"
                                                        aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
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