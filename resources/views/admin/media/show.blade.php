@extends('admin.layout')

@section('content')
   <div class="container">
       <div class="shadow-lg border-0 px-2 py-2 mb-3">
           <div class="page-breadcrumb d-none d-sm-flex align-items-center">
               <div class="ps-3">
                   <nav aria-label="breadcrumb">
                       <ol class="breadcrumb mb-0 p-0">
                           <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                           <li class="breadcrumb-item"><a href="{{ route('medias.index') }}">Détails d'un média</a></li>

                       </ol>
                   </nav>
               </div>
               <div class="ms-auto">
                   <div class="btn-group">
                       <a href="{{ route('medias.index') }}" class="btn btn-sm btn-primary"><i
                               class="bx bx-chevron-left-circle"></i> Retour</a>
                   </div>
               </div>
           </div>
       </div>
       <div class="row justify-content-center">
           <div class="col-md-12">
               <div class="card shadow rounded-4">
                   <div class="card-body p-4">

                       <p class="text-muted mb-4">{!! $media->description !!}  </p>
                       <hr>
                       <div class="mb-4 d-flex justify-content-center">
                           @if (Str::startsWith($media->type, 'image'))
                               <img src="{{ asset($media->url) }}" alt="{{ $media->titre }}" class="img-fluid rounded shadow">
                           @elseif (Str::startsWith($media->type, 'video'))
                               <div class="card p-4 shadow-lg border-0" style="max-width: 900px; margin: auto; background: linear-gradient(135deg, #ffb923, #fcde99);
); border-radius: 20px;">
                                   <div class="position-relative" style="overflow: hidden; border-radius: 15px;">
                                       <video controls autoplay muted playsinline style="width: 100%; height: auto; display: block; border-radius: 15px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
                                           <source src="{{ asset($media->url) }}" type="video/mp4">
                                           Votre navigateur ne supporte pas la lecture de vidéos.
                                       </video>
                                   </div>
                                   <h4 class="text-center mt-4" style="font-weight: bold; color: #333;">{{ $media->titre }}</h4>
                               </div>

                           @elseif (Str::startsWith($media->type, 'audio'))
                               <div class="col-md-8">
                                   <div class="card p-5 shadow-lg border-0" style="margin: auto; background: linear-gradient(135deg, #fbdfa1, #ffb923); border-radius: 10px;">
                                       <div class="text-center mb-4">
                                           <i class="bi bi-music-note-beamed" style="font-size: 4rem; color: #6c63ff;"></i>
                                       </div>
                                       <h4 class="text-center mb-3" style="font-weight: bold; color: #333;">{{ $media->titre }}</h4>
                                       <audio controls style="width: 100%; border-radius: 10px; background: #fff; padding: 10px;">
                                           <source src="{{ asset($media->url) }}" type="audio/mpeg">
                                           Votre navigateur ne supporte pas la lecture d'audio.
                                       </audio>
                                   </div>
                               </div>

                           @else
                               <a href="{{ asset($media->url) }}" target="_blank" class="btn btn-outline-primary">
                                   Télécharger le média
                               </a>
                           @endif
                       </div>



                       <ul class="list-group list-group-flush mb-3">
                           <li class="list-group-item"><strong>Type :</strong> {{ $media->type }}</li>
                           <li class="list-group-item"><strong>Formation liée :</strong>
                               {{ $media->formation->titre ?? 'Aucune' }}</li>
                       </ul>

                       <div class="text-end">
                           <a href="{{ route('medias.edit', $media->id) }}" class="btn btn-warning"><i class="bx bx-edit"></i>
                               Modifier</a>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
@endsection
