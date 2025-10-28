@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Manajemen /</span> Surat Legal
                </h4>
            </div>
        </div>

        <!-- Legal Letters List Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Surat Legal</h5>
                @if(auth()->user()->isAdministrator())
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLegalLetterModal">
                    <i class="icon-base ti tabler-plus me-2"></i>
                    Tambah Surat Legal
                </button>
                @endif
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="icon-base ti tabler-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchLegalLetters" placeholder="Cari judul atau deskripsi...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterCreator">
                            <option value="">Semua Pembuat</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="sortBy">
                            <option value="created_at">Tanggal Dibuat</option>
                            <option value="title">Judul</option>
                            <option value="updated_at">Terakhir Diupdate</option>
                        </select>
                    </div>
                </div>

                <!-- Legal Letters Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Pembuat</th>
                                <th>Desa Terkait</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="legalLettersTableBody">
                            <!-- Legal letters will be loaded here via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center" id="legalLettersPagination">
                        <!-- Pagination will be loaded here via JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdministrator())
    <!-- Create Legal Letter Modal -->
    <div class="modal fade" id="createLegalLetterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Surat Legal Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createLegalLetterForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="createTitle" class="form-label">Judul Surat Legal</label>
                                <input type="text" class="form-control" id="createTitle" name="title" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="createDescription" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="createDescription" name="description" rows="5" required></textarea>
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

    <!-- Edit Legal Letter Modal -->
    <div class="modal fade" id="editLegalLetterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Surat Legal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editLegalLetterForm">
                    <input type="hidden" id="editLegalLetterId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="editTitle" class="form-label">Judul Surat Legal</label>
                                <input type="text" class="form-control" id="editTitle" name="title" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="editDescription" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="editDescription" name="description" rows="5" required></textarea>
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
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteLegalLetterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus surat legal <strong id="deleteLegalLetterTitle"></strong>?</p>
                    <p class="text-danger small">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteLegalLetter">
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
    let deleteLegalLetterId = null;
    const isAdmin = {{ auth()->user()->isAdministrator() ? 'true' : 'false' }};

    // Load legal letters on page load
    loadLegalLetters();
    loadCreators();

    // Search functionality
    document.getElementById('searchLegalLetters').addEventListener('input', function() {
        currentPage = 1;
        loadLegalLetters();
    });

    // Filter functionality
    document.getElementById('filterCreator').addEventListener('change', function() {
        currentPage = 1;
        loadLegalLetters();
    });

    // Sort functionality
    document.getElementById('sortBy').addEventListener('change', function() {
        currentPage = 1;
        loadLegalLetters();
    });

    @if(auth()->user()->isAdministrator())
    // Create legal letter form
    document.getElementById('createLegalLetterForm').addEventListener('submit', handleCreateLegalLetter);

    // Edit legal letter form
    document.getElementById('editLegalLetterForm').addEventListener('submit', handleEditLegalLetter);
    @endif

    // Delete confirmation
    document.getElementById('confirmDeleteLegalLetter').addEventListener('click', handleDeleteLegalLetter);

    function loadLegalLetters() {
        const search = document.getElementById('searchLegalLetters').value;
        const creator = document.getElementById('filterCreator').value;
        const sortBy = document.getElementById('sortBy').value;
        
        const params = new URLSearchParams({
            page: currentPage,
            sort_by: sortBy,
            sort_order: 'desc'
        });

        if (search) params.append('search', search);
        if (creator) params.append('created_by', creator);

        fetch(`/legal-letters?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderLegalLettersTable(data.data.data);
                renderPagination(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading legal letters:', error);
            showToast('Terjadi kesalahan saat memuat data surat legal', 'error');
        });
    }

    function loadCreators() {
        fetch('/legal-letters-users', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const filterCreator = document.getElementById('filterCreator');
                data.data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    filterCreator.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading creators:', error);
        });
    }

    function renderLegalLettersTable(legalLetters) {
        const tbody = document.getElementById('legalLettersTableBody');
        
        if (legalLetters.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="icon-base ti tabler-file-text fs-1 mb-2 d-block"></i>
                            Tidak ada data surat legal
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = legalLetters.map(letter => {
            const createdDate = new Date(letter.created_at).toLocaleDateString('id-ID');
            const description = letter.description.length > 100 
                ? letter.description.substring(0, 100) + '...' 
                : letter.description;

            return `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    ${letter.title.charAt(0).toUpperCase()}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">${letter.title}</h6>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="text-muted" title="${letter.description}">${description}</span>
                    </td>
                    <td>
                        <span class="badge bg-label-info">${letter.creator ? letter.creator.name : 'Unknown'}</span>
                    </td>
                    <td>
                        <span class="badge bg-label-secondary" id="companies-count-${letter.id}">Loading...</span>
                    </td>
                    <td>${createdDate}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="icon-base ti tabler-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="viewLegalLetter(${letter.id})">
                                    <i class="icon-base ti tabler-eye me-2"></i> Lihat
                                </a>
                                ${isAdmin ? `
                                <a class="dropdown-item" href="javascript:void(0);" onclick="editLegalLetter(${letter.id})">
                                    <i class="icon-base ti tabler-pencil me-2"></i> Edit
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDeleteLegalLetter(${letter.id}, '${letter.title}')">
                                    <i class="icon-base ti tabler-trash me-2"></i> Hapus
                                </a>
                                ` : ''}
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Load company counts for each legal letter
        legalLetters.forEach(letter => {
            loadCompanyCount(letter.id);
        });
    }

    function loadCompanyCount(legalLetterId) {
        fetch(`/legal-letters/${legalLetterId}/companies`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const countElement = document.getElementById(`companies-count-${legalLetterId}`);
                if (countElement) {
                    const count = data.data.length;
                    countElement.textContent = `${count} Desa`;
                    countElement.className = count > 0 ? 'badge bg-label-success' : 'badge bg-label-secondary';
                }
            }
        })
        .catch(error => {
            const countElement = document.getElementById(`companies-count-${legalLetterId}`);
            if (countElement) {
                countElement.textContent = '0 Desa';
                countElement.className = 'badge bg-label-secondary';
            }
        });
    }

    function renderPagination(paginationData) {
        const pagination = document.getElementById('legalLettersPagination');
        
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
        loadLegalLetters();
    }

    @if(auth()->user()->isAdministrator())
    function handleCreateLegalLetter(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
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

        fetch('/legal-letters', {
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
                showToast('Surat legal berhasil ditambahkan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('createLegalLetterModal')).hide();
                form.reset();
                loadLegalLetters();
            } else {
                if (data.errors) {
                    showFormErrors(form, data.errors);
                } else {
                    showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error creating legal letter:', error);
            showToast('Terjadi kesalahan saat menambah surat legal', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function handleEditLegalLetter(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const legalLetterId = document.getElementById('editLegalLetterId').value;
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');

        // Convert FormData to JSON object
        const jsonData = {};
        for (let [key, value] of formData.entries()) {
            if (key !== 'id') {
                jsonData[key] = value;
            }
        }

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/legal-letters/${legalLetterId}`, {
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
                showToast('Surat legal berhasil diupdate', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editLegalLetterModal')).hide();
                loadLegalLetters();
            } else {
                if (data.errors) {
                    showFormErrors(form, data.errors);
                } else {
                    showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error updating legal letter:', error);
            showToast('Terjadi kesalahan saat mengupdate surat legal', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function editLegalLetter(id) {
        fetch(`/legal-letters/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const letter = data.data;
                
                // Populate form fields
                document.getElementById('editLegalLetterId').value = letter.id;
                document.getElementById('editTitle').value = letter.title || '';
                document.getElementById('editDescription').value = letter.description || '';
                
                // Show modal
                new bootstrap.Modal(document.getElementById('editLegalLetterModal')).show();
            } else {
                showToast('Gagal memuat data surat legal', 'error');
            }
        })
        .catch(error => {
            console.error('Error fetching legal letter:', error);
            showToast('Terjadi kesalahan saat memuat data surat legal', 'error');
        });
    }
    @endif

    function viewLegalLetter(id) {
        window.location.href = `/legal-letters/${id}`;
    }

    function confirmDeleteLegalLetter(id, title) {
        deleteLegalLetterId = id;
        document.getElementById('deleteLegalLetterTitle').textContent = title;
        new bootstrap.Modal(document.getElementById('deleteLegalLetterModal')).show();
    }

    function handleDeleteLegalLetter() {
        if (!deleteLegalLetterId) return;

        const submitBtn = document.getElementById('confirmDeleteLegalLetter');
        const spinner = submitBtn.querySelector('.spinner-border');

        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/legal-letters/${deleteLegalLetterId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Surat legal berhasil dihapus', 'success');
                bootstrap.Modal.getInstance(document.getElementById('deleteLegalLetterModal')).hide();
                loadLegalLetters();
            } else {
                showToast(data.message || 'Gagal menghapus surat legal', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting legal letter:', error);
            showToast('Terjadi kesalahan saat menghapus surat legal', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            deleteLegalLetterId = null;
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
    @if(auth()->user()->isAdministrator())
    window.editLegalLetter = editLegalLetter;
    @endif
    window.viewLegalLetter = viewLegalLetter;
    window.confirmDeleteLegalLetter = confirmDeleteLegalLetter;
    window.changePage = changePage;
});
</script>
@endsection
