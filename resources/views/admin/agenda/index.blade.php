@extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-0">Mon Agenda</h4>
                            <p class="text-muted mb-0">Visualisez et synchronisez vos événements Google Calendar</p>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'administrateur')
                                <button id="syncAllBtn" class="btn btn-primary d-flex align-items-center">
                                    <i class='bx bx-refresh me-2'></i> Synchroniser tout
                                </button>
                            @endif
                            <button id="connectGoogleBtn" class="btn btn-outline-danger d-flex align-items-center">
                                <i class='bx bxl-google me-2'></i> Connecter Google
                            </button>
                        </div>
                    </div>

                    @if($calendars->isEmpty())
                        <div class="alert alert-info border-0 bg-light-info alert-dismissible fade show py-2">
                            <div class="d-flex align-items-center">
                                <div class="fs-3 text-info"><i class='bx bx-info-circle'></i></div>
                                <div class="ms-3">
                                    <div class="text-info">Vous n'avez pas encore connecté de calendrier Google. Cliquez sur "Connecter Google" pour commencer.</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="calendar" style="min-height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>

<style>
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
    }
    .fc .fc-button-primary {
        background-color: #4361ee;
        border-color: #4361ee;
    }
    .fc .fc-button-primary:hover {
        background-color: #3f37c9;
        border-color: #3f37c9;
    }
    .fc-event {
        cursor: pointer;
        padding: 2px 5px;
        border-radius: 4px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'fr',
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour'
            },
            events: '{{ route("agenda.events") }}',
            eventClick: function(info) {
                // Show event details (optional)
                alert('Événement: ' + info.event.title + '\nDescription: ' + (info.event.extendedProps.description || 'N/A'));
            }
        });
        calendar.render();

        // Handle Google Auth
        const client = google.accounts.oauth2.initCodeClient({
            client_id: '{{ config('services.google.client_id') ?? env('GOOGLE_CLIENT_ID') }}',
            scope: 'https://www.googleapis.com/auth/calendar.readonly',
            ux_mode: 'popup',
            callback: (response) => {
                if (response.code) {
                    handleSync(response.code);
                }
            },
        });

        document.getElementById('connectGoogleBtn').onclick = () => {
            client.requestCode();
        };

        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'administrateur')
        document.getElementById('syncAllBtn').onclick = () => {
            handleSync();
        };
        @endif

        async function handleSync(authCode = null) {
            const btn = authCode ? document.getElementById('connectGoogleBtn') : document.getElementById('syncAllBtn');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Synchronisation...';

            try {
                // We call the Node.js API
                // Assuming the token is shared or we use a secret
                const response = await fetch('http://127.0.0.1:3000/api/agendas/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        // In a real app, we'd need the JWT from the logged-in user
                        // For admin 'sync all', we might need a special header if Node.js requires it
                        'Authorization': 'Bearer ' + '{{ $token }}'
                    },
                    body: JSON.stringify({ authCode })
                });

                const result = await response.json();
                
                if (response.ok) {
                    alert('Synchronisation réussie !');
                    calendar.refetchEvents();
                } else {
                    alert('Erreur: ' + (result.message || 'Échec de la synchronisation'));
                }
            } catch (error) {
                console.error('Sync error:', error);
                alert('Une erreur est survenue lors de la synchronisation.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    });
</script>
@endsection
