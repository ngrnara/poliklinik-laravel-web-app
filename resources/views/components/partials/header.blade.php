<nav class="main-header navbar navbar-expand navbar-light bg-transparent border-bottom">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item d-flex align-items-center">
            <span class="text-muted small mr-3">
                <i class="fas fa-user-md mr-1"></i>
                {{ Auth::user()->nama }} ({{ Auth::user()->role }})
            </span>
        </li>
    </ul>
</nav>
