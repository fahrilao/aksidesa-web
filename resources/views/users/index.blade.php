@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Manajemen /</span> Pengguna
                </h4>
            </div>
        </div>

        <!-- Users List Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Pengguna</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="icon-base ti tabler-plus me-2"></i>
                    Tambah Pengguna
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
                            <input type="text" class="form-control" id="searchUsers" placeholder="Cari nama atau email...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterRole">
                            <option value="">Semua Role</option>
                            <option value="Administrator">Administrator</option>
                            <option value="Operator">Operator</option>
                            <option value="RW">RW</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterCompany">
                            <option value="">Semua Desa</option>
                        </select>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Desa</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- Users will be loaded here via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center" id="usersPagination">
                        <!-- Pagination will be loaded here via JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createUserForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="createName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="createName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="createEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="createEmail" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="createPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="createPassword" name="password" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="createPasswordConfirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="createPasswordConfirmation" name="password_confirmation" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="createRole" class="form-label">Role</label>
                                <select class="form-select" id="createRole" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="Administrator">Administrator</option>
                                    <option value="Operator">Operator</option>
                                    <option value="RW">RW</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4" id="createCompanyField">
                                <label for="createCompany" class="form-label">Desa</label>
                                <select class="form-select" id="createCompany" name="company_id">
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
                            Simpan
                        </button>
                    </div>
                </form>
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
    let currentPage = 1;
    let currentUserId = null;

    // Load initial data
    loadUsers();
    loadCompanies();

    // Search functionality
    document.getElementById('searchUsers').addEventListener('input', debounce(function() {
        currentPage = 1;
        loadUsers();
    }, 300));

    // Filter functionality
    document.getElementById('filterRole').addEventListener('change', function() {
        currentPage = 1;
        loadUsers();
    });

    document.getElementById('filterCompany').addEventListener('change', function() {
        currentPage = 1;
        loadUsers();
    });

    // Role change handlers for company field visibility
    document.getElementById('createRole').addEventListener('change', function() {
        toggleCompanyField('create', this.value);
    });

    document.getElementById('editRole').addEventListener('change', function() {
        toggleCompanyField('edit', this.value);
    });

    // Form submissions
    document.getElementById('createUserForm').addEventListener('submit', handleCreateUser);
    document.getElementById('editUserForm').addEventListener('submit', handleEditUser);
    document.getElementById('confirmDeleteUser').addEventListener('click', handleDeleteUser);

    function loadUsers() {
        const search = document.getElementById('searchUsers').value;
        const role = document.getElementById('filterRole').value;
        const company = document.getElementById('filterCompany').value;

        const params = new URLSearchParams({
            page: currentPage,
            ...(search && { search }),
            ...(role && { role }),
            ...(company && { company_id: company })
        });

        fetch(`/users?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderUsersTable(data.data.data);
                renderPagination(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            showToast('Error loading users', 'error');
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
                populateCompanySelects(companies);
            }
        })
        .catch(error => {
            console.error('Error loading companies:', error);
        });
    }

    function populateCompanySelects(companies) {
        const selects = ['filterCompany', 'createCompany', 'editCompany'];
        
        selects.forEach(selectId => {
            const select = document.getElementById(selectId);
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
        });
    }

    function renderUsersTable(users) {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '';

        users.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                ${user.name.charAt(0).toUpperCase()}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">${user.name}</h6>
                        </div>
                    </div>
                </td>
                <td>${user.email}</td>
                <td>
                    <span class="badge bg-label-${getRoleBadgeColor(user.role)} rounded-pill">
                        ${user.role}
                    </span>
                </td>
                <td>${user.company ? `${user.company.name} (${user.company.code})` : '-'}</td>
                <td>${new Date(user.created_at).toLocaleDateString('id-ID')}</td>
                <td>
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="icon-base ti tabler-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="editUser(${user.id})">
                                <i class="icon-base ti tabler-pencil me-2"></i> Edit
                            </a>
                            <a class="dropdown-item" href="#" onclick="deleteUser(${user.id}, '${user.name}')">
                                <i class="icon-base ti tabler-trash me-2"></i> Hapus
                            </a>
                        </div>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderPagination(paginationData) {
        const pagination = document.getElementById('usersPagination');
        pagination.innerHTML = '';

        if (paginationData.last_page <= 1) return;

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${paginationData.current_page === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${paginationData.current_page - 1})">Previous</a>`;
        pagination.appendChild(prevLi);

        // Page numbers
        for (let i = 1; i <= paginationData.last_page; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === paginationData.current_page ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
            pagination.appendChild(li);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${paginationData.current_page === paginationData.last_page ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${paginationData.current_page + 1})">Next</a>`;
        pagination.appendChild(nextLi);
    }

    function toggleCompanyField(prefix, role) {
        const companyField = document.getElementById(`${prefix}CompanyField`);
        const companySelect = document.getElementById(`${prefix}Company`);
        
        if (role === 'Administrator') {
            companyField.style.display = 'none';
            companySelect.removeAttribute('required');
            companySelect.value = '';
        } else {
            companyField.style.display = 'block';
            companySelect.setAttribute('required', 'required');
        }
    }

    function handleCreateUser(e) {
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

        fetch('/users', {
            method: 'POST',
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
                showToast('Pengguna berhasil ditambahkan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('createUserModal')).hide();
                form.reset();
                loadUsers();
            } else {
                if (data.errors) {
                    showFormErrors(form, data.errors);
                } else {
                    showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error creating user:', error);
            showToast('Terjadi kesalahan saat menambah pengguna', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function handleEditUser(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const userId = document.getElementById('editUserId').value;
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');

        // Convert FormData to JSON object
        const jsonData = {};
        for (let [key, value] of formData.entries()) {
            jsonData[key] = value;
        }

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/users/${userId}`, {
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
                showToast('Pengguna berhasil diupdate', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                loadUsers();
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

        fetch(`/users/${currentUserId}`, {
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
                bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();
                loadUsers();
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
    window.changePage = function(page) {
        currentPage = page;
        loadUsers();
    };

    window.editUser = function(userId) {
        fetch(`/users/${userId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.data;
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editName').value = user.name;
                document.getElementById('editEmail').value = user.email;
                document.getElementById('editRole').value = user.role;
                document.getElementById('editCompany').value = user.company_id || '';
                
                toggleCompanyField('edit', user.role);
                
                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            }
        })
        .catch(error => {
            console.error('Error loading user:', error);
            showToast('Terjadi kesalahan saat memuat data pengguna', 'error');
        });
    };

    window.deleteUser = function(userId, userName) {
        currentUserId = userId;
        document.getElementById('deleteUserName').textContent = userName;
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

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
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
        // Simple toast implementation - you can replace with your preferred toast library
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
