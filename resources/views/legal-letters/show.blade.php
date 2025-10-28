@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Manajemen / Surat Legal /</span> Detail
                </h4>
            </div>
        </div>

        <div class="row">
            <!-- Legal Letter Profile Card -->
            <div class="col-xl-4 col-lg-5 col-md-5">
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="legal-letter-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <div class="avatar avatar-xl mb-4">
                                    <span class="avatar-initial rounded-circle bg-label-primary fs-2" id="legalLetterAvatar">
                                        <!-- Legal letter initial will be set via JavaScript -->
                                    </span>
                                </div>
                                <div class="legal-letter-info text-center">
                                    <h4 class="mb-2" id="legalLetterTitle">Loading...</h4>
                                    <span class="badge bg-label-info rounded-pill" id="legalLetterCreator">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around flex-wrap mt-4 pt-4 border-top">
                            <div class="d-flex align-items-center me-5 mt-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="icon-base ti tabler-building icon-md"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0" id="totalCompanies">0</h5>
                                    <span>Total Desa</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="icon-base ti tabler-check icon-md"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0" id="activeCompanies">0</h5>
                                    <span>Desa Aktif</span>
                                </div>
                            </div>
                        </div>
                        <h5 class="pb-4 border-bottom mb-4 mt-6">Detail</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6">
                                <li class="mb-2">
                                    <span class="h6">Pembuat:</span>
                                    <span id="legalLetterCreatorDetail">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Dibuat:</span>
                                    <span id="legalLetterCreatedDate">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Terakhir Diupdate:</span>
                                    <span id="legalLetterUpdatedDate">Loading...</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-center">
                                @if(auth()->user()->isAdministrator())
                                <button class="btn btn-primary me-4" onclick="editLegalLetter()">
                                    <i class="icon-base ti tabler-pencil me-2"></i>
                                    Edit
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteLegalLetter()">
                                    <i class="icon-base ti tabler-trash me-2"></i>
                                    Hapus
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legal Letter Information and Companies -->
            <div class="col-xl-8 col-lg-7 col-md-7">
                <!-- Legal Letter Details Card -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Deskripsi Surat Legal</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="description-content" id="legalLetterDescription">
                                    Loading...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Companies Management Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Desa Terkait</h5>
                        @if(auth()->user()->isAdministrator())
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#attachCompaniesModal">
                            <i class="icon-base ti tabler-plus me-2"></i>
                            Tambah Desa
                        </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Desa</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                        <th>Diupdate</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="companiesTableBody">
                                    <!-- Companies will be loaded here via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdministrator())
    <!-- Attach Companies Modal -->
    <div class="modal fade" id="attachCompaniesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Desa ke Surat Legal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="attachCompaniesForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="selectCompanies" class="form-label">Pilih Desa</label>
                                <select id="selectCompanies" name="company_ids[]" multiple required></select>
                                <div class="form-text">Pilih satu atau lebih desa</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="companyStatus" class="form-label">Status</label>
                                <select class="form-select" id="companyStatus" name="status">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="companyNotes" class="form-label">Catatan</label>
                                <textarea class="form-control" id="companyNotes" name="notes" rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                            Tambah Desa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Update Company Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Desa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm">
                    <input type="hidden" id="updateCompanyId" name="company_id">
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
                                <textarea class="form-control" id="updateNotes" name="notes" rows="3"></textarea>
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
    const legalLetterId = {{ $legalLetter->id }};
    let legalLetterData = null;

    // Load legal letter data on page load
    loadLegalLetterData();
    loadCompanies();
    @if(auth()->user()->isAdministrator())
    loadAvailableCompanies();
    
    // Handle modal events for Select2
    $('#attachCompaniesModal').on('hidden.bs.modal', function() {
        // Destroy Select2 when modal is closed to prevent memory leaks
        if ($('#selectCompanies').hasClass('select2-hidden-accessible')) {
            $('#selectCompanies').select2('destroy');
        }
    });
    
    $('#attachCompaniesModal').on('shown.bs.modal', function() {
        // Reinitialize Select2 when modal is shown if not already initialized
        if (!$('#selectCompanies').hasClass('select2-hidden-accessible')) {
            loadAvailableCompanies();
        }
    });
    @endif

    @if(auth()->user()->isAdministrator())
    // Attach companies form
    document.getElementById('attachCompaniesForm').addEventListener('submit', handleAttachCompanies);
    @endif

    // Update status form
    document.getElementById('updateStatusForm').addEventListener('submit', handleUpdateStatus);

    function loadLegalLetterData() {
        fetch(`/legal-letters/${legalLetterId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                legalLetterData = data.data;
                populateLegalLetterData(legalLetterData);
            } else {
                showToast('Gagal memuat data surat legal', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading legal letter data:', error);
            showToast('Terjadi kesalahan saat memuat data surat legal', 'error');
        });
    }

    function populateLegalLetterData(legalLetter) {
        // Basic info
        document.getElementById('legalLetterAvatar').textContent = legalLetter.title.charAt(0).toUpperCase();
        document.getElementById('legalLetterTitle').textContent = legalLetter.title;
        document.getElementById('legalLetterCreator').textContent = legalLetter.creator ? legalLetter.creator.name : 'Unknown';
        
        // Details
        document.getElementById('legalLetterCreatorDetail').textContent = legalLetter.creator ? legalLetter.creator.name : 'Unknown';
        
        // Dates
        const createdDate = new Date(legalLetter.created_at).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('legalLetterCreatedDate').textContent = createdDate;
        
        const updatedDate = new Date(legalLetter.updated_at).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('legalLetterUpdatedDate').textContent = updatedDate;
        
        // Description
        document.getElementById('legalLetterDescription').innerHTML = legalLetter.description.replace(/\n/g, '<br>');
    }

    function loadCompanies() {
        fetch(`/legal-letters/${legalLetterId}/companies`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderCompaniesTable(data.data);
                updateCompanyStats(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading companies:', error);
        });
    }

    @if(auth()->user()->isAdministrator())
    function loadAvailableCompanies() {
        fetch('/companies/active/list', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('selectCompanies');
                select.innerHTML = '';
                data.data.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company.id;
                    option.textContent = `${company.name} (${company.code})`;
                    select.appendChild(option);
                });
                
                // Initialize Select2 after options are loaded
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#selectCompanies').select2({
                        placeholder: 'Pilih desa...',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#attachCompaniesModal'),
                        closeOnSelect: false,
                        language: {
                            noResults: function() {
                                return "Tidak ada desa ditemukan";
                            },
                            searching: function() {
                                return "Mencari...";
                            }
                        }
                    });
                } else {
                    console.error('Select2 not loaded');
                }
            }
        })
        .catch(error => {
            console.error('Error loading available companies:', error);
        });
    }
    @endif

    function renderCompaniesTable(companies) {
        const tbody = document.getElementById('companiesTableBody');
        
        if (companies.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="text-muted">
                            <i class="icon-base ti tabler-building fs-1 mb-2 d-block"></i>
                            Belum ada desa terkait
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = companies.map(company => {
            const updatedDate = company.pivot.updated_at 
                ? new Date(company.pivot.updated_at).toLocaleDateString('id-ID')
                : '-';

            return `
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
                                <small class="text-muted">${company.code}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${company.pivot.status === 'active' ? 'bg-label-success' : 'bg-label-secondary'}">
                            ${company.pivot.status === 'active' ? 'Aktif' : 'Tidak Aktif'}
                        </span>
                    </td>
                    <td>
                        <span class="text-muted">${company.pivot.notes || '-'}</span>
                    </td>
                    <td>${updatedDate}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="icon-base ti tabler-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="/companies/${company.id}">
                                    <i class="icon-base ti tabler-eye me-2"></i> Lihat Desa
                                </a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="updateCompanyStatus(${company.id}, '${company.pivot.status}', '${company.pivot.notes || ''}')">
                                    <i class="icon-base ti tabler-edit me-2"></i> Update Status
                                </a>
                                @if(auth()->user()->isAdministrator())
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="detachCompany(${company.id}, '${company.name}')">
                                    <i class="icon-base ti tabler-unlink me-2"></i> Lepas Desa
                                </a>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updateCompanyStats(companies) {
        const totalCompanies = companies.length;
        const activeCompanies = companies.filter(c => c.pivot.status === 'active').length;
        
        document.getElementById('totalCompanies').textContent = totalCompanies;
        document.getElementById('activeCompanies').textContent = activeCompanies;
    }

    @if(auth()->user()->isAdministrator())
    function handleAttachCompanies(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');

        // Get selected companies from Select2
        const selectedCompanies = $('#selectCompanies').val() || [];

        // Validate selection
        if (selectedCompanies.length === 0) {
            showToast('Pilih minimal satu desa', 'error');
            return;
        }

        const jsonData = {
            company_ids: selectedCompanies,
            status: formData.get('status'),
            notes: formData.get('notes')
        };

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/legal-letters/${legalLetterId}/companies`, {
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
                bootstrap.Modal.getInstance(document.getElementById('attachCompaniesModal')).hide();
                form.reset();
                $('#selectCompanies').val(null).trigger('change'); // Clear Select2 selection
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
            console.error('Error attaching companies:', error);
            showToast('Terjadi kesalahan saat menambah desa', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }
    @endif

    function handleUpdateStatus(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const companyId = document.getElementById('updateCompanyId').value;
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
                showToast('Status desa berhasil diupdate', 'success');
                bootstrap.Modal.getInstance(document.getElementById('updateStatusModal')).hide();
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
            console.error('Error updating status:', error);
            showToast('Terjadi kesalahan saat mengupdate status', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function updateCompanyStatus(companyId, currentStatus, currentNotes) {
        document.getElementById('updateCompanyId').value = companyId;
        document.getElementById('updateStatus').value = currentStatus;
        document.getElementById('updateNotes').value = currentNotes;
        
        new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
    }

    @if(auth()->user()->isAdministrator())
    function detachCompany(companyId, companyName) {
        if (!confirm(`Apakah Anda yakin ingin melepas desa ${companyName} dari surat legal ini?`)) {
            return;
        }

        fetch(`/legal-letters/${legalLetterId}/companies/${companyId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Desa berhasil dilepas', 'success');
                loadCompanies();
            } else {
                showToast(data.message || 'Gagal melepas desa', 'error');
            }
        })
        .catch(error => {
            console.error('Error detaching company:', error);
            showToast('Terjadi kesalahan saat melepas desa', 'error');
        });
    }

    function editLegalLetter() {
        window.location.href = `/legal-letters/${legalLetterId}/edit`;
    }

    function deleteLegalLetter() {
        if (!confirm('Apakah Anda yakin ingin menghapus surat legal ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        fetch(`/legal-letters/${legalLetterId}`, {
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
                setTimeout(() => {
                    window.location.href = '/legal-letters';
                }, 1500);
            } else {
                showToast(data.message || 'Gagal menghapus surat legal', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting legal letter:', error);
            showToast('Terjadi kesalahan saat menghapus surat legal', 'error');
        });
    }
    @endif

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
    window.updateCompanyStatus = updateCompanyStatus;
    @if(auth()->user()->isAdministrator())
    window.detachCompany = detachCompany;
    window.editLegalLetter = editLegalLetter;
    window.deleteLegalLetter = deleteLegalLetter;
    @endif
});
</script>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endpush
