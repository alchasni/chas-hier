<aside class="main-sidebar dark-mode-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ url(auth()->user()->foto ?? '') }}" class="img-circle img-profil" alt="User Image">
            </div>
            <div class="pull-left info">
                <p class="user-name">{{ auth()->user()->name }}</p>
                <p class="user-level">
                    @if(auth()->user()->level == 1)
                        <span class="l1">Super Admin</span>
                    @elseif(auth()->user()->level == 2)
                        <span class="l2">Admin</span>
                    @elseif(auth()->user()->level == 3)
                        <span class="l3">Cashier</span>
                    @else
                        <span class="l4">Guest</span>
                    @endif
                </p>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-home"></i> <span>Home</span>
                </a>
            </li>
            @if (auth()->user()->level < 3)
                <li class="header">SALES</li>
                <li>
                    <a href="{{ route('category.index') }}">
                        <i class="fa fa-tags"></i> <span>Category</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('product.index') }}">
                        <i class="fa fa-dropbox"></i> <span>Product</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('guest.index') }}">
                        <i class="fa fa-id-card"></i> <span>Member</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('transaction.index') }}">
                        <i class="fa fa-money"></i> <span>Transaction</span>
                    </a>
                </li>
                <li class="header">SYSTEM</li>
                <li>
                    <a href="{{ route('laporan.index') }}">
                        <i class="fa fa-file-pdf-o"></i> <span>Export</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.index') }}">
                        <i class="fa fa-users"></i> <span>User</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('transaction.index') }}">
                        <i class="fa fa-cart-arrow-down"></i> <span>Transaksi Aktif</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('transaction.new') }}">
                        <i class="fa fa-cart-arrow-down"></i> <span>New Transaction</span>
                    </a>
                </li>
            @endif
        </ul>
    </section>
</aside>
