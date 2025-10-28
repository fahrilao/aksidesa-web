@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Surat Legal /</span> {{ $company->name }}
                </h4>
            </div>
        </div>

        <!-- Company Info Card -->
        <div class="row mb-6">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg me-4">
                                <span class="avatar-initial rounded-circle bg-label-primary fs-3">
                                    {{ $company->name[0] }}
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $company->name }}</h5>
                                <p class="mb-0 text-muted">Kode: {{ $company->code }}</p>
                                <span class="badge {{ $company->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                    {{ $company->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <span class="text-heading">Aktif</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="activeLetters">0</h4>
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
                                <span class="text-heading">Tidak Aktif</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="inactiveLetters">0</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="icon-base ti tabler-x"></i>
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
                                <span class="text-heading">Terakhir Update</span>
                                <div class="d-flex align-items-center my-1">
                                    <small class="text-muted" id="lastUpdated">-</small>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="icon-base ti tabler-clock"></i>
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
                <h5 class="card-title mb-0">Surat Legal Terkait</h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="filterStatus" style="width: auto;">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <!-- Legal Letters Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Judul Surat</th>
                                <th>Pembuat</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Diupdate</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="legalLettersTableBody">
                            <!-- Legal letters will be loaded here via JavaScript -->
                        </tbody>
                    </table>
                </div>
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
    const companyId = {{ $company->id }};

    // Load legal letters on page load
    loadLegalLetters();

    // Filter functionality
    document.getElementById('filterStatus').addEventListener('change', function() {
        loadLegalLetters();
    });

    // Update status form
    document.getElementById('updateStatusForm').addEventListener('submit', handleUpdateStatus);

    function loadLegalLetters() {
        fetch(`/companies/${companyId}/legal-letters`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderLegalLettersTable(data.data.requests);
                updateStatistics(data.data.requests);
            }
        })
        .catch(error => {
            console.error('Error loading legal letters:', error);
            showToast('Terjadi kesalahan saat memuat data surat legal', 'error');
        });
    }

    function renderLegalLettersTable(legalLetters) {
        const tbody = document.getElementById('legalLettersTableBody');
        const statusFilter = document.getElementById('filterStatus').value;
        
        // Filter letters based on status
        let filteredLetters = legalLetters;
        if (statusFilter) {
            filteredLetters = legalLetters.filter(letter => letter.pivot.status === statusFilter);
        }
        
        if (filteredLetters.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="icon-base ti tabler-file-text fs-1 mb-2 d-block"></i>
                            ${statusFilter ? 'Tidak ada surat legal dengan status ini' : 'Belum ada surat legal terkait'}
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filteredLetters.map(letter => {
            const updatedDate = letter.pivot.updated_at 
                ? new Date(letter.pivot.updated_at).toLocaleDateString('id-ID')
                : '-';
            const statusClass = letter.pivot.status === 'active' ? 'bg-label-success' : 'bg-label-secondary';
            const statusText = letter.pivot.status === 'active' ? 'Aktif' : 'Tidak Aktif';

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
                        <span class="badge ${statusClass}">${statusText}</span>
                    </td>
                    <td>
                        <span class="text-muted">${letter.pivot.notes || '-'}</span>
                    </td>
                    <td>${updatedDate}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="icon-base ti tabler-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="/legal-letters/${letter.id}">
                                    <i class="icon-base ti tabler-eye me-2"></i> Lihat Detail
                                </a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="updateStatus(${letter.id}, '${letter.pivot.status}', '${letter.pivot.notes || ''}')">
                                    <i class="icon-base ti tabler-edit me-2"></i> Update Status
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updateStatistics(legalLetters) {
        const total = legalLetters.length;
        const active = legalLetters.filter(letter => letter.pivot.status === 'active').length;
        const inactive = legalLetters.filter(letter => letter.pivot.status === 'inactive').length;
        
        document.getElementById('totalLetters').textContent = total;
        document.getElementById('activeLetters').textContent = active;
        document.getElementById('inactiveLetters').textContent = inactive;
        
        // Find the most recent update
        const lastUpdate = legalLetters.reduce((latest, letter) => {
            const letterDate = new Date(letter.pivot.updated_at || letter.updated_at);
            return letterDate > latest ? letterDate : latest;
        }, new Date(0));
        
        if (lastUpdate.getTime() > 0) {
            document.getElementById('lastUpdated').textContent = lastUpdate.toLocaleDateString('id-ID');
        }
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

        fetch(`/legal-letters/${legalLetterId}/companies/${companyId}/status`, {
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
    window.updateStatus = updateStatus;
});
</script>
@endsection
