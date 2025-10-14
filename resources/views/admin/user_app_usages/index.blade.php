@extends('admin.layout')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-semibold mb-4">Usages des applications mobiles</h1>

        <form method="GET" class="mb-4 flex gap-2 items-center">
            <select name="platform" class="border rounded px-2 py-1">
                <option value="">Toutes plateformes</option>
                <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android</option>
                <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS</option>
            </select>
            <button class="px-3 py-1 bg-blue-600 text-white rounded" type="submit">Filtrer</button>
            <a href="{{ route('admin.user_app_usages.export', ['platform' => request('platform')]) }}"
                class="px-3 py-1 bg-green-600 text-white rounded">Exporter CSV</a>
        </form>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full text-sm">
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
                            <td class="px-3 py-2">{{ optional($usage->first_used_at)->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="px-3 py-2">{{ optional($usage->last_used_at)->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $usage->app_version ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $usage->device_model ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $usage->os_version ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-gray-500">Aucun usage trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $usages->links() }}
        </div>
    </div>
@endsection
