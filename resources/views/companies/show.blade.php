@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Manajemen / Desa /</span> Detail
                </h4>
            </div>
        </div>

        <div class="row">
            <!-- Company Profile Card -->
            <div class="col-xl-4 col-lg-5 col-md-5">
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="company-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <div class="avatar avatar-xl mb-4">
                                    <span class="avatar-initial rounded-circle bg-label-primary fs-2" id="companyAvatar">
                                        <!-- Company initial will be set via JavaScript -->
                                    </span>
                                </div>
                                <div class="company-info text-center">
                                    <h4 class="mb-2" id="companyName">Loading...</h4>
                                    <span class="badge bg-label-info rounded-pill" id="companyCode">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around flex-wrap mt-4 pt-4 border-top">
                            <div class="d-flex align-items-center me-5 mt-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="icon-base ti tabler-users icon-md"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0" id="totalUsers">0</h5>
                                    <span>Total User</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="icon-base ti tabler-file-text icon-md"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0" id="totalRequests">0</h5>
                                    <span>Permintaan Surat</span>
                                </div>
                            </div>
                        </div>
                        <h5 class="pb-4 border-bottom mb-4 mt-6">Detail</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6">
                                <li class="mb-2">
                                    <span class="h6">Kode:</span>
                                    <span id="companyCodeDetail">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Email:</span>
                                    <span id="companyEmail">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Telepon:</span>
                                    <span id="companyPhone">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Website:</span>
                                    <span id="companyWebsite">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Status:</span>
                                    <span class="badge rounded-pill" id="companyStatus">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Dibuat:</span>
                                    <span id="companyCreatedDate">Loading...</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary me-4" onclick="editCompany()">
                                    <i class="icon-base ti tabler-pencil me-2"></i>
                                    Edit
                                </button>
                                <button class="btn btn-outline-warning me-4" onclick="toggleStatus()">
                                    <i class="icon-base ti tabler-toggle-left me-2" id="toggleIcon"></i>
                                    <span id="toggleText">Toggle Status</span>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteCompany()">
                                    <i class="icon-base ti tabler-trash me-2"></i>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Information and Users -->
            <div class="col-xl-8 col-lg-7 col-md-7">
                <!-- Company Details Card -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Desa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Alamat</h6>
                                <p id="companyAddress" class="mb-0">Loading...</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Deskripsi</h6>
                                <p id="companyDescription" class="mb-0">Loading...</p>
                            </div>
                        </div>
                        
                        <!-- API Key Section -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted">API Key</h6>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="apiKeyDisplay" readonly>
                                            <button class="btn btn-outline-secondary" type="button" onclick="toggleApiKeyVisibility()">
                                                <i class="icon-base ti tabler-eye" id="apiKeyToggleIcon"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted" id="apiKeyInfo">Loading...</small>
                                    </div>
                                    <div class="ms-3">
                                        <button class="btn btn-success btn-sm me-2" onclick="generateApiKey()" id="generateApiKeyBtn">
                                            <i class="icon-base ti tabler-key me-1"></i>
                                            Generate
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="revokeApiKey()" id="revokeApiKeyBtn">
                                            <i class="icon-base ti tabler-trash me-1"></i>
                                            Revoke
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users List Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Daftar User</h5>
                        <div>
                            <select class="form-select form-select-sm" id="filterUserRole" style="width: auto;">
                                <option value="">Semua Role</option>
                                <option value="Operator">Operator</option>
                                <option value="RW">RW</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Bergabung</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Users will be loaded here via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companyId = {{ $company->id }};
    let companyData = null;

    // Load company data on page load
    loadCompanyData();
    loadCompanyUsers();

    // Filter users by role
    document.getElementById('filterUserRole').addEventListener('change', function() {
        loadCompanyUsers();
    });

    function loadCompanyData() {
        fetch(`/companies/${companyId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                companyData = data.data;
                populateCompanyData(companyData);
            } else {
                showToast('Gagal memuat data desa', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading company data:', error);
            showToast('Terjadi kesalahan saat memuat data desa', 'error');
        });
    }

    function populateCompanyData(company) {
        // Basic info
        document.getElementById('companyAvatar').textContent = company.name.charAt(0).toUpperCase();
        document.getElementById('companyName').textContent = company.name;
        document.getElementById('companyCode').textContent = company.code;
        
        // Details
        document.getElementById('companyCodeDetail').textContent = company.code;
        document.getElementById('companyEmail').textContent = company.email || '-';
        document.getElementById('companyPhone').textContent = company.phone || '-';
        
        // Website with link
        const websiteElement = document.getElementById('companyWebsite');
        if (company.website) {
            websiteElement.innerHTML = `<a href="${company.website}" target="_blank" class="text-primary">${company.website}</a>`;
        } else {
            websiteElement.textContent = '-';
        }
        
        // Status
        const statusElement = document.getElementById('companyStatus');
        if (company.is_active) {
            statusElement.className = 'badge bg-label-success rounded-pill';
            statusElement.textContent = 'Aktif';
        } else {
            statusElement.className = 'badge bg-label-secondary rounded-pill';
            statusElement.textContent = 'Tidak Aktif';
        }
        
        // Created date
        const createdDate = new Date(company.created_at).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('companyCreatedDate').textContent = createdDate;
        
        // Address and description
        document.getElementById('companyAddress').textContent = company.address || 'Tidak ada alamat';
        document.getElementById('companyDescription').textContent = company.description || 'Tidak ada deskripsi';
        
        // API Key
        updateApiKeyDisplay(company);
        
        // Toggle button
        updateToggleButton(company.is_active);
    }

    function updateApiKeyDisplay(company) {
        const apiKeyDisplay = document.getElementById('apiKeyDisplay');
        const apiKeyInfo = document.getElementById('apiKeyInfo');
        const generateBtn = document.getElementById('generateApiKeyBtn');
        const revokeBtn = document.getElementById('revokeApiKeyBtn');
        
        if (company.api_key) {
            apiKeyDisplay.value = company.api_key;
            const createdDate = new Date(company.api_key_created_at).toLocaleDateString('id-ID');
            const lastUsed = company.api_key_last_used_at 
                ? new Date(company.api_key_last_used_at).toLocaleDateString('id-ID')
                : 'Belum pernah digunakan';
            apiKeyInfo.textContent = `Dibuat: ${createdDate} | Terakhir digunakan: ${lastUsed}`;
            generateBtn.textContent = 'Regenerate';
            revokeBtn.disabled = false;
        } else {
            apiKeyDisplay.value = 'Tidak ada API key';
            apiKeyInfo.textContent = 'API key belum dibuat';
            generateBtn.textContent = 'Generate';
            revokeBtn.disabled = true;
        }
    }

    function updateToggleButton(isActive) {
        const toggleIcon = document.getElementById('toggleIcon');
        const toggleText = document.getElementById('toggleText');
        
        if (isActive) {
            toggleIcon.className = 'icon-base ti tabler-toggle-right me-2';
            toggleText.textContent = 'Nonaktifkan';
        } else {
            toggleIcon.className = 'icon-base ti tabler-toggle-left me-2';
            toggleText.textContent = 'Aktifkan';
        }
    }

    function loadCompanyUsers() {
        const roleFilter = document.getElementById('filterUserRole').value;
        const params = new URLSearchParams();
        
        if (roleFilter) {
            params.append('role', roleFilter);
        }

        fetch(`/companies/${companyId}/users?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderUsersTable(data.data);
                document.getElementById('totalUsers').textContent = data.data.length;
            }
        })
        .catch(error => {
            console.error('Error loading company users:', error);
        });
    }

    function renderUsersTable(users) {
        const tbody = document.getElementById('usersTableBody');
        
        if (users.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="text-muted">
                            <i class="icon-base ti tabler-users fs-1 mb-2 d-block"></i>
                            Tidak ada user di desa ini
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = users.map(user => {
            const joinDate = new Date(user.created_at).toLocaleDateString('id-ID');
            return `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    ${user.name.charAt(0).toUpperCase()}
                                </span>
                            </div>
                            <h6 class="mb-0">${user.name}</h6>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td><span class="badge bg-label-info">${user.role}</span></td>
                    <td>${joinDate}</td>
                    <td>
                        <a href="/users/${user.id}" class="btn btn-sm btn-outline-primary">
                            <i class="icon-base ti tabler-eye me-1"></i>
                            Lihat
                        </a>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function toggleApiKeyVisibility() {
        const apiKeyDisplay = document.getElementById('apiKeyDisplay');
        const toggleIcon = document.getElementById('apiKeyToggleIcon');
        
        if (apiKeyDisplay.type === 'password') {
            apiKeyDisplay.type = 'text';
            toggleIcon.className = 'icon-base ti tabler-eye-off';
        } else {
            apiKeyDisplay.type = 'password';
            toggleIcon.className = 'icon-base ti tabler-eye';
        }
    }

    function generateApiKey() {
        if (!confirm('Apakah Anda yakin ingin generate API key baru? API key lama akan tidak valid.')) {
            return;
        }

        const generateBtn = document.getElementById('generateApiKeyBtn');
        const isRegenerate = companyData.api_key ? true : false;
        const endpoint = isRegenerate 
            ? `/companies/${companyId}/api-key/regenerate`
            : `/companies/${companyId}/api-key/generate`;
        
        generateBtn.disabled = true;

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('API key berhasil di-generate', 'success');
                loadCompanyData(); // Reload to get updated API key info
            } else {
                showToast(data.message || 'Gagal generate API key', 'error');
            }
        })
        .catch(error => {
            console.error('Error generating API key:', error);
            showToast('Terjadi kesalahan saat generate API key', 'error');
        })
        .finally(() => {
            generateBtn.disabled = false;
        });
    }

    function revokeApiKey() {
        if (!confirm('Apakah Anda yakin ingin revoke API key? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        const revokeBtn = document.getElementById('revokeApiKeyBtn');
        revokeBtn.disabled = true;

        fetch(`/companies/${companyId}/api-key`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('API key berhasil di-revoke', 'success');
                loadCompanyData(); // Reload to get updated API key info
            } else {
                showToast(data.message || 'Gagal revoke API key', 'error');
            }
        })
        .catch(error => {
            console.error('Error revoking API key:', error);
            showToast('Terjadi kesalahan saat revoke API key', 'error');
        })
        .finally(() => {
            revokeBtn.disabled = false;
        });
    }

    function editCompany() {
        window.location.href = `/companies/${companyId}/edit`;
    }

    function toggleStatus() {
        const action = companyData.is_active ? 'nonaktifkan' : 'aktifkan';
        if (!confirm(`Apakah Anda yakin ingin ${action} desa ini?`)) {
            return;
        }

        fetch(`/companies/${companyId}/toggle-status`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Status desa berhasil diubah', 'success');
                loadCompanyData(); // Reload to get updated status
            } else {
                showToast(data.message || 'Gagal mengubah status desa', 'error');
            }
        })
        .catch(error => {
            console.error('Error toggling company status:', error);
            showToast('Terjadi kesalahan saat mengubah status desa', 'error');
        });
    }

    function deleteCompany() {
        if (!confirm('Apakah Anda yakin ingin menghapus desa ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        fetch(`/companies/${companyId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Desa berhasil dihapus', 'success');
                setTimeout(() => {
                    window.location.href = '/companies';
                }, 1500);
            } else {
                showToast(data.message || 'Gagal menghapus desa', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting company:', error);
            showToast('Terjadi kesalahan saat menghapus desa', 'error');
        });
    }

    function showToast(message, type = 'info') {
        // Simple toast implementation - matches the user views implementation
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

    // Make functions globally available
    window.toggleApiKeyVisibility = toggleApiKeyVisibility;
    window.generateApiKey = generateApiKey;
    window.revokeApiKey = revokeApiKey;
    window.editCompany = editCompany;
    window.toggleStatus = toggleStatus;
    window.deleteCompany = deleteCompany;
});
</script>
@endsection
