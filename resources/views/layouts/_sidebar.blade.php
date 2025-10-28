            <aside id="layout-menu" class="layout-menu menu-vertical menu">
                <div class="app-brand demo ">
                    <a href="index.html" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <span class="text-primary">
                                <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                                        fill="currentColor" />
                                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                                        fill="#161616" />
                                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                                        fill="#161616" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold ms-3">{{ config('app.name') }}</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
                        <i class="icon-base ti tabler-x d-block d-xl-none"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Page -->
                    <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <a href="{{ route('home') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-smart-home"></i>
                            <div data-i18n="Home">Beranda</div>
                        </a>
                    </li>

                    @if(auth()->user()->isAdministrator())  
                        <!-- User Management -->
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Manajemen</span>
                        </li>
                        <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-users"></i>
                                <div data-i18n="Users">Pengguna</div>
                            </a>
                        </li>

                        <li class="menu-item {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                            <a href="{{ route('companies.index') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-building"></i>
                                <div data-i18n="Companies">Kelola Desa</div>
                            </a>
                        </li>
                        
                        <!-- Legal Letters -->
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Surat Legal</span>
                        </li>
                        <li class="menu-item {{ request()->routeIs('legal-letters.*') ? 'active' : '' }}">
                            <a href="{{ route('legal-letters.index') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-text"></i>
                                <div data-i18n="LegalLetters">Surat Legal</div>
                            </a>
                        </li>

                        <li class="menu-item {{ request()->routeIs('request-legal-letters.*') ? 'active' : '' }}">
                            <a href="{{ route('request-legal-letters.index') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-clipboard-list"></i>
                                <div data-i18n="RequestLegalLetters">Permintaan Surat</div>
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->isOperator())
                        <!-- Operator Legal Letters -->
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Surat Legal Desa</span>
                        </li>
                        <li class="menu-item {{ request()->routeIs('legal-letters.operator') ? 'active' : '' }}">
                            <a href="{{ route('legal-letters.operator') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-file-text"></i>
                                <div data-i18n="OperatorLegalLetters">Surat Legal</div>
                            </a>
                        </li>

                        <li class="menu-item {{ request()->routeIs('request-legal-letters.*') ? 'active' : '' }}">
                            <a href="{{ route('request-legal-letters.index') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-clipboard-list"></i>
                                <div data-i18n="RequestLegalLetters">Permintaan Surat</div>
                                <div class="badge badge-center rounded-pill bg-warning w-px-20 h-px-20 ms-auto" id="sidebar-operator-requests-count">
                                    <span class="visually-hidden">New requests count</span>
                                </div>
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->role === 'RW')
                        <!-- RW User Menu -->
                        <li class="menu-header small text-uppercase">
                            <span class="menu-header-text">Permintaan Surat</span>
                        </li>
                        <li class="menu-item {{ request()->routeIs('request-legal-letters.*') ? 'active' : '' }}">
                            <a href="{{ route('request-legal-letters.index') }}" class="menu-link">
                                <i class="menu-icon icon-base ti tabler-clipboard-list"></i>
                                <div data-i18n="RequestLegalLetters">Permintaan Surat</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </aside>

            <div class="menu-mobile-toggler d-xl-none rounded-1">
                <a href="javascript:void(0);"
                    class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                    <i class="ti tabler-menu icon-base"></i>
                    <i class="ti tabler-chevron-right icon-base"></i>
                </a>
            </div>
