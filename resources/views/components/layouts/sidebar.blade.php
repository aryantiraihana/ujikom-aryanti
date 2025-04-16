<div>
    <div class="sidebar border-end" style="min-height: 100vh;">
        <div class="sidebar-header border-bottom">
            <div class="sidebar-brand">E-Market</div>
        </div>
        <ul class="sidebar-nav">
            <li class="nav-title">Menu</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="nav-icon cil-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('product') ? 'active' : '' }}" href="{{ route('product') }}">
                    <i class="nav-icon cil-inbox"></i> Produk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('purchase') ? 'active' : '' }}" href="{{ route('purchase') }}">
                    <i class="nav-icon cil-money"></i> Penjualan
                </a>
            </li>
            @if (Auth::user()->role == 'admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user') ? 'active' : '' }}" href="{{ route('user') }}">
                        <i class="nav-icon cil-people"></i> User
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
