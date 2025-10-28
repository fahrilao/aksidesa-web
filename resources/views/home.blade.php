@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Welcome Section -->
        <div class="row mb-6">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg me-4">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ti tabler-user icon-40px"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1">Selamat datang, {{ auth()->user()->name }}!</h4>
                                <p class="mb-0 text-muted">
                                    @if(auth()->user()->isAdministrator())
                                        Anda login sebagai Administrator. Kelola seluruh sistem permintaan surat legal.
                                    @elseif(auth()->user()->isOperator())
                                        Anda login sebagai Operator {{ auth()->user()->company->name ?? 'Desa' }}. Proses permintaan surat legal dari warga.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Total Permintaan</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2 fw-bold">{{ $stats['total_requests'] }}</h4>
                                </div>
                                <small class="mb-0">
                                    @if($stats['requests_today'] > 0)
                                        <span class="text-success mb-0">(+{{ $stats['requests_today'] }})</span>
                                        Permintaan baru hari ini
                                    @else
                                        <span class="text-muted mb-0">Tidak ada permintaan baru hari ini</span>
                                    @endif
                                </small>
                            </div>
                            <div class="avatar avatar-xl">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ti tabler-file-text icon-40px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Menunggu</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2 fw-bold">{{ $stats['pending_requests'] }}</h4>
                                </div>
                                <small class="mb-0">
                                    <span class="text-warning mb-0">Perlu ditindaklanjuti</span>
                                </small>
                            </div>
                            <div class="avatar avatar-xl">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="icon-base ti tabler-clock icon-40px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Sedang Diproses</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2 fw-bold">{{ $stats['processing_requests'] }}</h4>
                                </div>
                                <small class="mb-0">
                                    <span class="text-info mb-0">Dalam penanganan</span>
                                </small>
                            </div>
                            <div class="avatar avatar-xl">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="icon-base ti tabler-settings icon-40px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Selesai</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2 fw-bold">{{ $stats['completed_requests'] }}</h4>
                                </div>
                                <small class="mb-0">
                                    @if($stats['completed_today'] > 0)
                                        <span class="text-success mb-0">(+{{ $stats['completed_today'] }})</span>
                                        Selesai hari ini
                                    @else
                                        <span class="text-muted mb-0">{{ $stats['completed_this_month'] }} bulan ini</span>
                                    @endif
                                </small>
                            </div>
                            <div class="avatar avatar-xl">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="icon-base ti tabler-check icon-40px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdministrator())
        <!-- Admin Additional Stats -->
        <div class="row g-6 mb-6">
            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Total Desa</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2 fw-bold">{{ $stats['total_companies'] }}</h4>
                                </div>
                                <small class="mb-0">
                                    <span class="text-muted mb-0">Desa terdaftar</span>
                                </small>
                            </div>
                            <div class="avatar avatar-xl">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="icon-base ti tabler-building icon-40px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Total Operator</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2 fw-bold">{{ $stats['total_operators'] }}</h4>
                                </div>
                                <small class="mb-0">
                                    <span class="text-muted mb-0">Operator aktif</span>
                                </small>
                            </div>
                            <div class="avatar avatar-xl">
                                <span class="avatar-initial rounded bg-label-dark">
                                    <i class="icon-base ti tabler-users icon-40px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Total Warga (RW)</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2 fw-bold">{{ $stats['total_rw_users'] }}</h4>
                                </div>
                                <small class="mb-0">
                                    <span class="text-muted mb-0">Pengguna mobile app</span>
                                </small>
                            </div>
                            <div class="avatar avatar-xl">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ti tabler-user-circle icon-40px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Aksi Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @if(auth()->user()->isAdministrator())
                                <div class="col-sm-6 col-lg-3">
                                    <a href="{{ route('request-legal-letters.index') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                        <i class="icon-base ti tabler-file-text mb-2" style="font-size: 2rem;"></i>
                                        <span>Kelola Permintaan</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3">
                                        <i class="icon-base ti tabler-building mb-2" style="font-size: 2rem;"></i>
                                        <span>Kelola Desa</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-dark w-100 d-flex flex-column align-items-center py-3">
                                        <i class="icon-base ti tabler-users mb-2" style="font-size: 2rem;"></i>
                                        <span>Kelola Pengguna</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <a href="{{ route('legal-letters.index') }}" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3">
                                        <i class="icon-base ti tabler-file-check mb-2" style="font-size: 2rem;"></i>
                                        <span>Surat Legal</span>
                                    </a>
                                </div>
                            @elseif(auth()->user()->isOperator())
                                <div class="col-sm-6 col-lg-6">
                                    <a href="{{ route('request-legal-letters.index') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                        <i class="icon-base ti tabler-file-text mb-2" style="font-size: 2rem;"></i>
                                        <span>Proses Permintaan</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-lg-6">
                                    <a href="{{ route('legal-letters.operator') }}" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3">
                                        <i class="icon-base ti tabler-file-check mb-2" style="font-size: 2rem;"></i>
                                        <span>Surat Legal</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
