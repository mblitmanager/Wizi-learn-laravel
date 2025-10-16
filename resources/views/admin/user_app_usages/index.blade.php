@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Usages des
                                applications mobiles
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="mb-4 d-flex gap-2 items-center">
                        <select name="platform" class="border rounded px-2 py-1 form-select">
                            <option value="">Toutes plateformes</option>
                            <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android
                            </option>
                            <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS</option>
                        </select>
                        <button class="px-3 py-1 bg-blue-600 text-white rounded btn btn-primary" type="submit">Filtrer</button>
                        <a href="{{ route('admin.user_app_usages.export', ['platform' => request('platform')]) }}"
                            class="px-3 py-1 btn btn-success text-white rounded">Exporter CSV</a>
                    </form>
                    <div class="shadow rounded">
                        <table class="table-responsive table-striped table">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-3 py-2">Utilisateur</th>
                                    <th class="px-3 py-2">Plateforme</th>
                                    <th class="px-3 py-2">Première utilisation</th>
                                    <th class="px-3 py-2">Dernière utilisation</th>
                                    <th class="px-3 py-2">Version app</th>
                                    <th class="px-3 py-2">Modèle</th>
                                    <th class="px-3 py-2">OS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($usages as $usage)
                                    <tr class="border-t">
                                        <td class="px-3 py-2">
                                            {{ $usage->user?->name ?? '—' }}<br>
                                            <span class="text-gray-500">ID: {{ $usage->user_id }}</span>
                                        </td>
                                        <td class="px-3 py-2 uppercase">{{ $usage->platform }}</td>
                                        <td class="px-3 py-2">
                                            {{ optional($usage->first_used_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                        <td class="px-3 py-2">
                                            {{ optional($usage->last_used_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ $usage->app_version ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ $usage->device_model ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ $usage->os_version ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-6 text-center text-gray-500">Aucun usage trouvé.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $usages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
