@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Manajemen / Pengguna /</span> Detail
                </h4>
            </div>
        </div>

        <div class="row">
            <!-- User Profile Card -->
            <div class="col-xl-4 col-lg-5 col-md-5">
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <div class="avatar avatar-xl mb-4">
                                    <span class="avatar-initial rounded-circle bg-label-primary fs-2" id="userAvatar">
                                        <!-- User initial will be set via JavaScript -->
                                    </span>
                                </div>
                                <div class="user-info text-center">
                                    <h4 class="mb-2" id="userName">Loading...</h4>
                                    <span class="badge bg-label-secondary rounded-pill" id="userRole">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around flex-wrap mt-4 pt-4 border-top">
                            <div class="d-flex align-items-center me-5 mt-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="icon-base ti tabler-check icon-md"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0" id="userTasksCount">0</h5>
                                    <span>Tugas Selesai</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="icon-base ti tabler-briefcase icon-md"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0" id="userProjectsCount">0</h5>
                                    <span>Proyek Aktif</span>
                                </div>
                            </div>
                        </div>
                        <h5 class="pb-4 border-bottom mb-4 mt-6">Detail</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6">
                                <li class="mb-2">
                                    <span class="h6">Email:</span>
                                    <span id="userEmail">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Role:</span>
                                    <span id="userRoleDetail">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Desa:</span>
                                    <span id="userCompany">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Status:</span>
                                    <span class="badge bg-label-success rounded-pill" id="userStatus">Aktif</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Bergabung:</span>
                                    <span id="userJoinDate">Loading...</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary me-4" onclick="editUser()">
                                    <i class="icon-base ti tabler-pencil me-2"></i>
                                    Edit
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteUser()">
                                    <i class="icon-base ti tabler-trash me-2"></i>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Activity and Information -->
            <div class="col-xl-8 col-lg-7 col-md-7">
                <!-- Activity Overview -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4 mb-4">
                            <div class="col-sm-6 col-xl-3">
                                <div class="d-flex align-items-start">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-primary rounded">
                                            <i class="icon-base ti tabler-file-text"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="mb-0" id="totalRequests">0</h5>
                                        <span class="text-muted">Total Permintaan</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="d-flex align-items-start">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-warning rounded">
                                            <i class="icon-base ti tabler-clock"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="mb-0" id="pendingRequests">0</h5>
                                        <span class="text-muted">Menunggu</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="d-flex align-items-start">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-info rounded">
                                            <i class="icon-base ti tabler-progress"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="mb-0" id="processingRequests">0</h5>
                                        <span class="text-muted">Diproses</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <div class="d-flex align-items-start">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-success rounded">
                                            <i class="icon-base ti tabler-check"></i>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="mb-0" id="completedRequests">0</h5>
                                        <span class="text-muted">Selesai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card mb-6">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Riwayat Aktivitas</h5>
                        <small class="text-muted">30 hari terakhir</small>
                    </div>
                    <div class="card-body">
                        <ul class="timeline mb-0" id="userActivities">
                            <!-- Activities will be loaded here -->
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-primary"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">Memuat aktivitas...</h6>
                                        <small class="text-muted">Loading...</small>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- User Permissions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Hak Akses & Izin</h5>
                    </div>
                    <div class="card-body">
                        <div class="row" id="userPermissions">
                            <!-- Permissions will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="editName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="editPassword" class="form-label">Password Baru (Kosongkan jika tidak diubah)</label>
                                <input type="password" class="form-control" id="editPassword" name="password">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="editPasswordConfirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="editPasswordConfirmation" name="password_confirmation">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="editRole" class="form-label">Role</label>
                                <select class="form-select" id="editRole" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="Administrator">Administrator</option>
                                    <option value="Operator">Operator</option>
                                    <option value="RW">RW</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4" id="editCompanyField">
                                <label for="editCompany" class="form-label">Desa</label>
                                <select class="form-select" id="editCompany" name="company_id">
                                    <option value="">Pilih Desa</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pengguna <strong id="deleteUserName"></strong>?</p>
                    <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteUser">
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userId = {{ $user->id ?? 'null' }};
    
    if (!userId) {
        showToast('User ID tidak ditemukan', 'error');
        window.location.href = '/users';
        return;
    }

    loadUserData();
    loadUserActivities();
    loadCompanies();

    // Form submission
    document.getElementById('editUserForm').addEventListener('submit', handleEditUser);
    document.getElementById('confirmDeleteUser').addEventListener('click', handleDeleteUser);

    // Role change handler
    document.getElementById('editRole').addEventListener('change', function() {
        toggleCompanyField(this.value);
    });

    function loadUserData() {
        fetch(`/users/${userId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUserData(data.data);
            } else {
                showToast('Gagal memuat data pengguna', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading user:', error);
            showToast('Terjadi kesalahan saat memuat data pengguna', 'error');
        });
    }

    function displayUserData(user) {
        // Update avatar
        document.getElementById('userAvatar').textContent = user.name.charAt(0).toUpperCase();
        
        // Update basic info
        document.getElementById('userName').textContent = user.name;
        document.getElementById('userRole').textContent = user.role;
        document.getElementById('userRole').className = `badge bg-label-${getRoleBadgeColor(user.role)} rounded-pill`;
        
        // Update details
        document.getElementById('userEmail').textContent = user.email;
        document.getElementById('userRoleDetail').textContent = user.role;
        document.getElementById('userCompany').textContent = user.company ? `${user.company.name} (${user.company.code})` : 'Tidak ada';
        document.getElementById('userJoinDate').textContent = new Date(user.created_at).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Update form fields for editing
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editName').value = user.name;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editRole').value = user.role;
        document.getElementById('editCompany').value = user.company_id || '';
        document.getElementById('deleteUserName').textContent = user.name;
        
        toggleCompanyField(user.role);
        loadUserStats(user.id);
        loadUserPermissions(user.role);
    }

    function loadUserStats(userId) {
        // Mock data - replace with actual API calls
        document.getElementById('totalRequests').textContent = '24';
        document.getElementById('pendingRequests').textContent = '3';
        document.getElementById('processingRequests').textContent = '5';
        document.getElementById('completedRequests').textContent = '16';
        document.getElementById('userTasksCount').textContent = '16';
        document.getElementById('userProjectsCount').textContent = '8';
    }

    function loadUserActivities() {
        // Mock activities - replace with actual API call
        const activities = [
            {
                title: 'Menyelesaikan permintaan surat',
                description: 'Surat Keterangan Domisili untuk PT. ABC',
                time: '2 jam yang lalu',
                type: 'success'
            },
            {
                title: 'Memproses permintaan baru',
                description: 'Surat Izin Usaha untuk CV. XYZ',
                time: '4 jam yang lalu',
                type: 'info'
            },
            {
                title: 'Login ke sistem',
                description: 'Akses dari IP 192.168.1.100',
                time: '6 jam yang lalu',
                type: 'primary'
            }
        ];

        const container = document.getElementById('userActivities');
        container.innerHTML = '';

        activities.forEach(activity => {
            const li = document.createElement('li');
            li.className = 'timeline-item timeline-item-transparent';
            li.innerHTML = `
                <span class="timeline-point timeline-point-${activity.type}"></span>
                <div class="timeline-event">
                    <div class="timeline-header mb-3">
                        <h6 class="mb-0">${activity.title}</h6>
                        <small class="text-muted">${activity.time}</small>
                    </div>
                    <p class="mb-2">${activity.description}</p>
                </div>
            `;
            container.appendChild(li);
        });
    }

    function loadUserPermissions(role) {
        const permissions = {
            'Administrator': [
                { name: 'Kelola Pengguna', icon: 'tabler-users', granted: true },
                { name: 'Kelola Desa', icon: 'tabler-building', granted: true },
                { name: 'Kelola Surat Legal', icon: 'tabler-file-text', granted: true },
                { name: 'Kelola API Key', icon: 'tabler-key', granted: true },
                { name: 'Lihat Laporan', icon: 'tabler-chart-bar', granted: true },
                { name: 'Backup Data', icon: 'tabler-database', granted: true }
            ],
            'Operator': [
                { name: 'Lihat Pengguna', icon: 'tabler-users', granted: true },
                { name: 'Lihat Desa', icon: 'tabler-building', granted: true },
                { name: 'Kelola Surat Legal', icon: 'tabler-file-text', granted: true },
                { name: 'Kelola Permintaan', icon: 'tabler-clipboard-list', granted: true },
                { name: 'Lihat Laporan', icon: 'tabler-chart-bar', granted: true },
                { name: 'Backup Data', icon: 'tabler-database', granted: false }
            ],
            'RW': [
                { name: 'Buat Permintaan', icon: 'tabler-file-plus', granted: true },
                { name: 'Lihat Permintaan Sendiri', icon: 'tabler-eye', granted: true },
                { name: 'Kelola Pengguna', icon: 'tabler-users', granted: false },
                { name: 'Kelola Desa', icon: 'tabler-building', granted: false },
                { name: 'Lihat Laporan', icon: 'tabler-chart-bar', granted: false },
                { name: 'Backup Data', icon: 'tabler-database', granted: false }
            ]
        };

        const container = document.getElementById('userPermissions');
        container.innerHTML = '';

        (permissions[role] || []).forEach(permission => {
            const div = document.createElement('div');
            div.className = 'col-md-6 mb-4';
            div.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                        <div class="avatar-initial bg-label-${permission.granted ? 'success' : 'secondary'} rounded">
                            <i class="icon-base ti ${permission.icon}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">${permission.name}</h6>
                        <small class="text-${permission.granted ? 'success' : 'muted'}">
                            ${permission.granted ? 'Diizinkan' : 'Tidak diizinkan'}
                        </small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" ${permission.granted ? 'checked' : ''} disabled>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }

    function loadCompanies() {
        fetch('/companies', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const companies = data.data.data || data.data;
                populateCompanySelect(companies);
            }
        })
        .catch(error => {
            console.error('Error loading companies:', error);
        });
    }

    function populateCompanySelect(companies) {
        const select = document.getElementById('editCompany');
        const currentValue = select.value;
        
        // Clear existing options (except first)
        while (select.children.length > 1) {
            select.removeChild(select.lastChild);
        }
        
        companies.forEach(company => {
            const option = document.createElement('option');
            option.value = company.id;
            option.textContent = `${company.name} (${company.code})`;
            select.appendChild(option);
        });
        
        select.value = currentValue;
    }

    function toggleCompanyField(role) {
        const companyField = document.getElementById('editCompanyField');
        const companySelect = document.getElementById('editCompany');
        
        if (role === 'Administrator') {
            companyField.style.display = 'none';
            companySelect.removeAttribute('required');
            companySelect.value = '';
        } else {
            companyField.style.display = 'block';
            companySelect.setAttribute('required', 'required');
        }
    }

    function handleEditUser(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/users/${userId}`, {
            method: 'PUT',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Pengguna berhasil diupdate', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                loadUserData();
            } else {
                if (data.errors) {
                    showFormErrors(form, data.errors);
                } else {
                    showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error updating user:', error);
            showToast('Terjadi kesalahan saat mengupdate pengguna', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function handleDeleteUser() {
        const deleteBtn = document.getElementById('confirmDeleteUser');
        const spinner = deleteBtn.querySelector('.spinner-border');

        deleteBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Pengguna berhasil dihapus', 'success');
                window.location.href = '/users';
            } else {
                showToast(data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting user:', error);
            showToast('Terjadi kesalahan saat menghapus pengguna', 'error');
        })
        .finally(() => {
            deleteBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    // Global functions
    window.editUser = function() {
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    };

    window.deleteUser = function() {
        new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    };

    // Utility functions
    function getRoleBadgeColor(role) {
        const colors = {
            'Administrator': 'danger',
            'Operator': 'warning',
            'RW': 'info'
        };
        return colors[role] || 'secondary';
    }

    function clearFormErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    function showFormErrors(form, errors) {
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            const feedback = input?.parentElement.querySelector('.invalid-feedback');
            
            if (input && feedback) {
                input.classList.add('is-invalid');
                feedback.textContent = errors[field][0];
            }
        });
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }
});
</script>
@endpush
