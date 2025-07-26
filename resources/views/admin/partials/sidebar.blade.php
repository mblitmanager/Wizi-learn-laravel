<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('assets/images/logowizi.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-home-circle'></i>
                </div>
                <div class="menu-title">Tableau de bord</div>
            </a>
            <ul>
                <li> <a href="{{ route('dashboard') }}"><i class="bx bx-right-arrow-alt"></i>Tableau de bord</a>
                </li>
                <li> <a href="{{ route('dashboard.activity') }}"><i class="bx bx-right-arrow-alt"></i>Activité des
                        utilisateurs</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.achievements.index') }}">
                        <i class="fas fa-trophy me-2"></i> Gestion des Succès
                    </a>
                </li>
            </ul>
            {{-- <ul>
                <li class="nav-item"><i class="bx bx-right-arrow-alt"></i>
                    <a href="{{ route('admin.achievements.index') }}">
                        <i class="fas fa-trophy me-2"></i> Gestion des Succès
                    </a>
                </li>
            </ul> --}}
        </li>
        <li>
            <a href="{{ route('stagiaires.index') }}">
                <div class="parent-icon"><i class='lni lni-users'></i>
                </div>
                <div class="menu-title">Stagiaire</div>
            </a>
        </li>
        <li>
            <a href="{{ route('quiz.index') }}">
                <div class="parent-icon"><i class='fadeIn animated bx bx-brain'></i>
                </div>
                <div class="menu-title">Quiz</div>
            </a>

        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='fadeIn animated bx bx-phone-outgoing'></i>
                </div>
                <div class="menu-title">Contact</div>
            </a>
            <ul>
                <li> <a href="{{ route('partenaires.index') }}"><i class="bx bx-right-arrow-alt"></i>Partenaire</a>
                </li>
                <li> <a href="{{ route('formateur.index') }}"><i class="bx bx-right-arrow-alt"></i>Formateur</a>
                </li>
                <li> <a href="{{ route('pole_relation_clients.index') }}"><i class="bx bx-right-arrow-alt"></i>Pole
                        relation client</a>
                </li>
                <li> <a href="{{ route('commercials.index') }}"><i class="bx bx-right-arrow-alt"></i>Commercial</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ route('classement.index') }}">
                <div class="parent-icon"><i class='fadeIn animated bx bx-list-ol'></i>
                </div>
                <div class="menu-title">Classement</div>
            </a>

        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-library"></i>
                </div>
                <div class="menu-title">Formation</div>
            </a>
            <ul>
                <li> <a href="{{ route('catalogue_formation.index') }}"><i class="bx bx-right-arrow-alt"></i>Catalogue
                        formation</a>
                </li>
                <li> <a href="{{ route('formations.index') }}"><i class="bx bx-right-arrow-alt"></i>Domaine
                        formation</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ route('parrainage.index') }}">
                <div class="parent-icon"><i class='fadeIn animated bx bx-git-branch'></i>
                </div>
                <div class="menu-title">Parrainage</div>
            </a>

        </li>
        <li>
            <a href="{{ route('medias.index') }}">
                <div class="parent-icon"><i class='fadeIn animated bx bx-play-circle'></i>
                </div>
                <div class="menu-title">Media</div>
            </a>

        </li>
        <li>
            <a href="javascript:;">
                <div class="parent-icon"><i class='fadeIn animated bx bx-target-lock'></i>
                </div>
                <div class="menu-title">Défis</div>
            </a>

        </li>
        <li>
            <a href="javascript:;">
                <div class="parent-icon"><i class='fadeIn animated bx bx-calendar'></i>
                </div>
                <div class="menu-title">Planning</div>
            </a>

        </li>
        <li>
            <a href="{{ route('admin.parametre.reset-data') }}">
                <div class="parent-icon"><i class='fadeIn animated bx bx-line-chart'></i>
                </div>
                <div class="menu-title">Statistiques</div>
            </a>

        </li>

        <li>
            <a href="{{ route('parametre.index') }}">
                <div class="parent-icon"><i class='bx bx-cog bx-spin'></i>
                </div>
                <div class="menu-title">Paramètres</div>
            </a>

        </li>
        {{-- @include('admin.partials.sidebar') --}}


    </ul>
    <!--end navigation-->
</div>
