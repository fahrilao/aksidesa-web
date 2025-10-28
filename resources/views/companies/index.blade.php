@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Manajemen /</span> Desa
                </h4>
            </div>
        </div>

        <!-- Companies List Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Desa</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
                    <i class="icon-base ti tabler-plus me-2"></i>
                    Tambah Desa
                </button>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="icon-base ti tabler-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchCompanies" placeholder="Cari nama atau kode desa...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Companies Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kode</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Status</th>
                                <th>Jumlah User</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="companiesTableBody">
                            <!-- Companies will be loaded here via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center" id="companiesPagination">
                        <!-- Pagination will be loaded here via JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Create Company Modal -->
    <div class="modal fade" id="createCompanyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Desa Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createCompanyForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="createName" class="form-label">Nama Desa</label>
                                <input type="text" class="form-control" id="createName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="createCode" class="form-label">Kode Desa</label>
                                <input type="text" class="form-control" id="createCode" name="code" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="createEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="createEmail" name="email">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="createPhone" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="createPhone" name="phone">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="createWebsite" class="form-label">Website</label>
                                <input type="url" class="form-control" id="createWebsite" name="website" placeholder="https://example.com">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="createAddress" class="form-label">Alamat</label>
                                <textarea class="form-control" id="createAddress" name="address" rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="createDescription" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="createDescription" name="description" rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="createIsActive" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="createIsActive">
                                        Desa Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Company Modal -->
    <div class="modal fade" id="editCompanyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Desa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCompanyForm">
                    <input type="hidden" id="editCompanyId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="editName" class="form-label">Nama Desa</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="editCode" class="form-label">Kode Desa</label>
                                <input type="text" class="form-control" id="editCode" name="code" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editEmail" name="email">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="editPhone" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="editPhone" name="phone">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="editWebsite" class="form-label">Website</label>
                                <input type="url" class="form-control" id="editWebsite" name="website" placeholder="https://example.com">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="editAddress" class="form-label">Alamat</label>
                                <textarea class="form-control" id="editAddress" name="address" rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="editDescription" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editIsActive" name="is_active" value="1">
                                    <label class="form-check-label" for="editIsActive">
                                        Desa Aktif
                                    </label>
                                </div>
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
    <div class="modal fade" id="deleteCompanyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus desa <strong id="deleteCompanyName"></strong>?</p>
                    <p class="text-danger small">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCompany">
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let deleteCompanyId = null;

    // Load companies on page load
    loadCompanies();

    // Search functionality
    document.getElementById('searchCompanies').addEventListener('input', function() {
        currentPage = 1;
        loadCompanies();
    });

    // Filter functionality
    document.getElementById('filterStatus').addEventListener('change', function() {
        currentPage = 1;
        loadCompanies();
    });

    // Create company form
    document.getElementById('createCompanyForm').addEventListener('submit', handleCreateCompany);

    // Edit company form
    document.getElementById('editCompanyForm').addEventListener('submit', handleEditCompany);

    // Delete confirmation
    document.getElementById('confirmDeleteCompany').addEventListener('click', handleDeleteCompany);

    function loadCompanies() {
        const search = document.getElementById('searchCompanies').value;
        const status = document.getElementById('filterStatus').value;
        
        const params = new URLSearchParams({
            page: currentPage,
            with_users: 1
        });

        if (search) params.append('search', search);
        if (status !== '') params.append('active', status);

        fetch(`/companies?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderCompaniesTable(data.data.data);
                renderPagination(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading companies:', error);
            showToast('Terjadi kesalahan saat memuat data desa', 'error');
        });
    }

    function renderCompaniesTable(companies) {
        const tbody = document.getElementById('companiesTableBody');
        
        if (companies.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="icon-base ti tabler-building-store fs-1 mb-2 d-block"></i>
                            Tidak ada data desa
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = companies.map(company => `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                ${company.name.charAt(0).toUpperCase()}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">${company.name}</h6>
                            ${company.website ? `<small class="text-muted">${company.website}</small>` : ''}
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-label-info">${company.code}</span></td>
                <td>${company.email || '-'}</td>
                <td>${company.phone || '-'}</td>
                <td>
                    <span class="badge ${company.is_active ? 'bg-label-success' : 'bg-label-secondary'}">
                        ${company.is_active ? 'Aktif' : 'Tidak Aktif'}
                    </span>
                </td>
                <td>
                    <span class="badge bg-label-primary">${company.users_count || 0} User</span>
                </td>
                <td>
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="icon-base ti tabler-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);" onclick="viewCompany(${company.id})">
                                <i class="icon-base ti tabler-eye me-2"></i> Lihat
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="editCompany(${company.id})">
                                <i class="icon-base ti tabler-pencil me-2"></i> Edit
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="toggleCompanyStatus(${company.id})">
                                <i class="icon-base ti tabler-toggle-${company.is_active ? 'right' : 'left'} me-2"></i> 
                                ${company.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDeleteCompany(${company.id}, '${company.name}')">
                                <i class="icon-base ti tabler-trash me-2"></i> Hapus
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderPagination(paginationData) {
        const pagination = document.getElementById('companiesPagination');
        
        if (paginationData.last_page <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let paginationHtml = '';
        
        // Previous button
        if (paginationData.current_page > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" onclick="changePage(${paginationData.current_page - 1})">
                        <i class="icon-base ti tabler-chevron-left"></i>
                    </a>
                </li>
            `;
        }

        // Page numbers
        const startPage = Math.max(1, paginationData.current_page - 2);
        const endPage = Math.min(paginationData.last_page, paginationData.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === paginationData.current_page ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0);" onclick="changePage(${i})">${i}</a>
                </li>
            `;
        }

        // Next button
        if (paginationData.current_page < paginationData.last_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" onclick="changePage(${paginationData.current_page + 1})">
                        <i class="icon-base ti tabler-chevron-right"></i>
                    </a>
                </li>
            `;
        }

        pagination.innerHTML = paginationHtml;
    }

    function changePage(page) {
        currentPage = page;
        loadCompanies();
    }

    function handleCreateCompany(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');

        // Convert FormData to JSON object
        const jsonData = {};
        for (let [key, value] of formData.entries()) {
            if (key === 'is_active') {
                jsonData[key] = value === '1';
            } else {
                jsonData[key] = value;
            }
        }

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch('/companies', {
            method: 'POST',
            body: JSON.stringify(jsonData),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Desa berhasil ditambahkan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('createCompanyModal')).hide();
                form.reset();
                loadCompanies();
            } else {
                if (data.errors) {
                    showFormErrors(form, data.errors);
                } else {
                    showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error creating company:', error);
            showToast('Terjadi kesalahan saat menambah desa', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function handleEditCompany(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const companyId = document.getElementById('editCompanyId').value;
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');

        // Convert FormData to JSON object
        const jsonData = {};
        for (let [key, value] of formData.entries()) {
            if (key === 'is_active') {
                jsonData[key] = document.getElementById('editIsActive').checked;
            } else if (key !== 'id') {
                jsonData[key] = value;
            }
        }

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/companies/${companyId}`, {
            method: 'PUT',
            body: JSON.stringify(jsonData),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Desa berhasil diupdate', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editCompanyModal')).hide();
                loadCompanies();
            } else {
                if (data.errors) {
                    showFormErrors(form, data.errors);
                } else {
                    showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error updating company:', error);
            showToast('Terjadi kesalahan saat mengupdate desa', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function editCompany(id) {
        fetch(`/companies/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const company = data.data;
                
                // Populate form fields
                document.getElementById('editCompanyId').value = company.id;
                document.getElementById('editName').value = company.name || '';
                document.getElementById('editCode').value = company.code || '';
                document.getElementById('editEmail').value = company.email || '';
                document.getElementById('editPhone').value = company.phone || '';
                document.getElementById('editWebsite').value = company.website || '';
                document.getElementById('editAddress').value = company.address || '';
                document.getElementById('editDescription').value = company.description || '';
                document.getElementById('editIsActive').checked = company.is_active;
                
                // Show modal
                new bootstrap.Modal(document.getElementById('editCompanyModal')).show();
            } else {
                showToast('Gagal memuat data desa', 'error');
            }
        })
        .catch(error => {
            console.error('Error fetching company:', error);
            showToast('Terjadi kesalahan saat memuat data desa', 'error');
        });
    }

    function viewCompany(id) {
        window.location.href = `/companies/${id}`;
    }

    function toggleCompanyStatus(id) {
        fetch(`/companies/${id}/toggle-status`, {
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
                loadCompanies();
            } else {
                showToast(data.message || 'Gagal mengubah status desa', 'error');
            }
        })
        .catch(error => {
            console.error('Error toggling company status:', error);
            showToast('Terjadi kesalahan saat mengubah status desa', 'error');
        });
    }

    function confirmDeleteCompany(id, name) {
        deleteCompanyId = id;
        document.getElementById('deleteCompanyName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteCompanyModal')).show();
    }

    function handleDeleteCompany() {
        if (!deleteCompanyId) return;

        const submitBtn = document.getElementById('confirmDeleteCompany');
        const spinner = submitBtn.querySelector('.spinner-border');

        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/companies/${deleteCompanyId}`, {
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
                bootstrap.Modal.getInstance(document.getElementById('deleteCompanyModal')).hide();
                loadCompanies();
            } else {
                showToast(data.message || 'Gagal menghapus desa', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting company:', error);
            showToast('Terjadi kesalahan saat menghapus desa', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            deleteCompanyId = null;
        });
    }

    // Utility functions
    function clearFormErrors(form) {
        const errorElements = form.querySelectorAll('.is-invalid');
        errorElements.forEach(element => {
            element.classList.remove('is-invalid');
        });
        
        const feedbackElements = form.querySelectorAll('.invalid-feedback');
        feedbackElements.forEach(element => {
            element.textContent = '';
        });
    }

    function showFormErrors(form, errors) {
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = errors[field][0];
                }
            }
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
    window.editCompany = editCompany;
    window.viewCompany = viewCompany;
    window.toggleCompanyStatus = toggleCompanyStatus;
    window.confirmDeleteCompany = confirmDeleteCompany;
    window.changePage = changePage;
});
</script>
@endsection
