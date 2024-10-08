<header class="main-header dark-mode-header">
    <!-- Logo -->
    <a href="index2.html" class="logo">
        @php
            $words = explode(' ', "NAMA");
            $word  = '';
            foreach ($words as $w) {
                $word .= $w[0];
            }
        @endphp
        <span class="logo-mini logo-text">{{ $word }}</span>
        <span class="logo-lg logo-text"><b>{{ "NAMA" }}</b></span>
    </a>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ url(auth()->user()->photo ?? '') }}" class="user-image img-profile" alt="User Image">
                        <span class="hidden-xs">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="{{ url(auth()->user()->photo ?? '') }}" class="img-circle img-profile" alt="User Image">
                            <p>{{ auth()->user()->name }} - {{ auth()->user()->email }}</p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('user.profile') }}" class="btn btn-profile">Profil</a>
                            </div>
                            <div class="pull-right">
                                <a href="#" class="btn btn-logout" onclick="$('#logout-form').submit()">Keluar</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<form action="{{ route('logout') }}" method="post" id="logout-form" style="display: none;">
    @csrf
</form>
