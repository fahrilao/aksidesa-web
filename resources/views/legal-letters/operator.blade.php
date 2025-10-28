@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Surat Legal /</span> Manajemen Desa
                </h4>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-6">
            <div class="col-lg-3 col-md-6 col-12 mb-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="content-left">
                                <span class="text-heading">Total Surat</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="totalLetters">0</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ti tabler-file-text"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="content-left">
                                <span class="text-heading">Ditugaskan</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="assignedLetters">0</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="icon-base ti tabler-check"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="content-left">
                                <span class="text-heading">Belum Ditugaskan</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="unassignedLetters">0</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="icon-base ti tabler-clock"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="content-left">
                                <span class="text-heading">Aktif</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="activeLetters">0</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="icon-base ti tabler-toggle-right"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legal Letters List Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Surat Legal</h5>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="icon-base ti tabler-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchLegalLetters" placeholder="Cari judul atau deskripsi...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterAssignment">
                            <option value="">Semua Status</option>
                            <option value="assigned">Ditugaskan</option>
                            <option value="unassigned">Belum Ditugaskan</option>
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
                                <th>Judul Surat</th>
                                <th>Pembuat</th>
                                <th>Status Penugasan</th>
                                <th>Status Aktif</th>
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

    <!-- Assign Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tugaskan Surat Legal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignForm">
                    <input type="hidden" id="assignLegalLetterId" name="legal_letter_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="assignStatus" class="form-label">Status Awal</label>
                                <select class="form-select" id="assignStatus" name="status">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="assignNotes" class="form-label">Catatan</label>
                                <textarea class="form-control" id="assignNotes" name="notes" rows="3" placeholder="Tambahkan catatan (opsional)"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                            Tugaskan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Surat Legal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm">
                    <input type="hidden" id="updateLegalLetterId" name="legal_letter_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="updateStatus" class="form-label">Status</label>
                                <select class="form-select" id="updateStatus" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="updateNotes" class="form-label">Catatan</label>
                                <textarea class="form-control" id="updateNotes" name="notes" rows="3" placeholder="Tambahkan catatan (opsional)"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;

    // Load legal letters on page load
    loadLegalLetters();

    // Search functionality
    document.getElementById('searchLegalLetters').addEventListener('input', function() {
        currentPage = 1;
        loadLegalLetters();
    });

    // Filter functionality
    document.getElementById('filterAssignment').addEventListener('change', function() {
        currentPage = 1;
        loadLegalLetters();
    });

    // Sort functionality
    document.getElementById('sortBy').addEventListener('change', function() {
        currentPage = 1;
        loadLegalLetters();
    });

    // Assign form
    document.getElementById('assignForm').addEventListener('submit', handleAssign);

    // Update status form
    document.getElementById('updateStatusForm').addEventListener('submit', handleUpdateStatus);

    function loadLegalLetters() {
        const search = document.getElementById('searchLegalLetters').value;
        const assignment = document.getElementById('filterAssignment').value;
        const sortBy = document.getElementById('sortBy').value;
        
        const params = new URLSearchParams({
            page: currentPage,
            sort_by: sortBy,
            sort_order: 'desc'
        });

        if (search) params.append('search', search);
        if (assignment) params.append('assignment_status', assignment);

        fetch(`/legal-letters/operator?${params}`, {
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
                updateStatistics(data.data.data);
            }
        })
        .catch(error => {
            console.error('Error loading legal letters:', error);
            showToast('Terjadi kesalahan saat memuat data surat legal', 'error');
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
            const assignmentClass = letter.is_assigned ? 'bg-label-success' : 'bg-label-warning';
            const assignmentText = letter.is_assigned ? 'Ditugaskan' : 'Belum Ditugaskan';
            
            let statusBadge = '-';
            if (letter.is_assigned) {
                const statusClass = letter.assignment_status === 'active' ? 'bg-label-success' : 'bg-label-secondary';
                const statusText = letter.assignment_status === 'active' ? 'Aktif' : 'Tidak Aktif';
                statusBadge = `<span class="badge ${statusClass}">${statusText}</span>`;
            }

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
                                <small class="text-muted">${letter.description.length > 50 ? letter.description.substring(0, 50) + '...' : letter.description}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-label-info">${letter.creator ? letter.creator.name : 'Unknown'}</span>
                    </td>
                    <td>
                        <span class="badge ${assignmentClass}">${assignmentText}</span>
                    </td>
                    <td>${statusBadge}</td>
                    <td>${createdDate}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="icon-base ti tabler-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="/legal-letters/${letter.id}">
                                    <i class="icon-base ti tabler-eye me-2"></i> Lihat Detail
                                </a>
                                ${!letter.is_assigned ? `
                                <a class="dropdown-item" href="javascript:void(0);" onclick="assignLetter(${letter.id})">
                                    <i class="icon-base ti tabler-plus me-2"></i> Tugaskan
                                </a>
                                ` : `
                                <a class="dropdown-item" href="javascript:void(0);" onclick="updateStatus(${letter.id}, '${letter.assignment_status}', '${letter.assignment_notes || ''}')">
                                    <i class="icon-base ti tabler-edit me-2"></i> Update Status
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="unassignLetter(${letter.id})">
                                    <i class="icon-base ti tabler-x me-2"></i> Batalkan Penugasan
                                </a>
                                `}
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updateStatistics(legalLetters) {
        const total = legalLetters.length;
        const assigned = legalLetters.filter(letter => letter.is_assigned).length;
        const unassigned = total - assigned;
        const active = legalLetters.filter(letter => letter.is_assigned && letter.assignment_status === 'active').length;
        
        document.getElementById('totalLetters').textContent = total;
        document.getElementById('assignedLetters').textContent = assigned;
        document.getElementById('unassignedLetters').textContent = unassigned;
        document.getElementById('activeLetters').textContent = active;
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

    function assignLetter(legalLetterId) {
        document.getElementById('assignLegalLetterId').value = legalLetterId;
        document.getElementById('assignStatus').value = 'active';
        document.getElementById('assignNotes').value = '';
        
        new bootstrap.Modal(document.getElementById('assignModal')).show();
    }

    function handleAssign(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const legalLetterId = document.getElementById('assignLegalLetterId').value;
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');

        const jsonData = {
            status: formData.get('status'),
            notes: formData.get('notes')
        };

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/legal-letters/${legalLetterId}/assign-company`, {
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
                showToast('Surat legal berhasil ditugaskan', 'success');
                bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();
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
            console.error('Error assigning legal letter:', error);
            showToast('Terjadi kesalahan saat menugaskan surat legal', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function updateStatus(legalLetterId, currentStatus, currentNotes) {
        document.getElementById('updateLegalLetterId').value = legalLetterId;
        document.getElementById('updateStatus').value = currentStatus;
        document.getElementById('updateNotes').value = currentNotes;
        
        new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
    }

    function handleUpdateStatus(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const legalLetterId = document.getElementById('updateLegalLetterId').value;
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');

        const jsonData = {
            status: formData.get('status'),
            notes: formData.get('notes')
        };

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/legal-letters/${legalLetterId}/companies/{{ auth()->user()->company_id }}/status`, {
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
                showToast('Status surat legal berhasil diupdate', 'success');
                bootstrap.Modal.getInstance(document.getElementById('updateStatusModal')).hide();
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
            console.error('Error updating status:', error);
            showToast('Terjadi kesalahan saat mengupdate status', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function unassignLetter(legalLetterId) {
        if (!confirm('Apakah Anda yakin ingin membatalkan penugasan surat legal ini?')) {
            return;
        }

        fetch(`/legal-letters/${legalLetterId}/unassign-company`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Penugasan surat legal berhasil dibatalkan', 'success');
                loadLegalLetters();
            } else {
                showToast(data.message || 'Gagal membatalkan penugasan', 'error');
            }
        })
        .catch(error => {
            console.error('Error unassigning legal letter:', error);
            showToast('Terjadi kesalahan saat membatalkan penugasan', 'error');
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
    window.assignLetter = assignLetter;
    window.updateStatus = updateStatus;
    window.unassignLetter = unassignLetter;
    window.changePage = changePage;
});
</script>
@endsection
