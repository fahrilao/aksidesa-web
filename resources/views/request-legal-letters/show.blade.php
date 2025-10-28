@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Manajemen / Permintaan Surat Legal /</span> Detail
                </h4>
            </div>
        </div>

        <div class="row">
            <!-- Request Profile Card -->
            <div class="col-xl-4 col-lg-5 col-md-5">
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="request-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <div class="avatar avatar-xl mb-4">
                                    <span class="avatar-initial rounded-circle bg-label-primary fs-2" id="requestAvatar">
                                        <!-- Request initial will be set via JavaScript -->
                                    </span>
                                </div>
                                <div class="request-info text-center">
                                    <h4 class="mb-2" id="requestTitle">Loading...</h4>
                                    <span class="badge rounded-pill" id="requestStatus">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around flex-wrap mt-4 pt-4 border-top">
                            <div class="d-flex align-items-center me-5 mt-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="icon-base ti tabler-user icon-md"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0" id="requesterName">Loading...</h6>
                                    <span>Pemohon</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-4">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="icon-base ti tabler-user-check icon-md"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0" id="assigneeName">Belum ditugaskan</h6>
                                    <span>Ditugaskan ke</span>
                                </div>
                            </div>
                        </div>
                        <h5 class="pb-4 border-bottom mb-4 mt-6">Detail</h5>
                        <div class="info-container">
                            <ul class="list-unstyled mb-6">
                                <li class="mb-2">
                                    <span class="h6">Nama:</span>
                                    <span id="requestName">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">NIK:</span>
                                    <span id="requestNik">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Dibuat:</span>
                                    <span id="requestCreatedDate">Loading...</span>
                                </li>
                                <li class="mb-2">
                                    <span class="h6">Terakhir Diupdate:</span>
                                    <span id="requestUpdatedDate">Loading...</span>
                                </li>
                                <li class="mb-2" id="legalLetterInfo" style="display: none;">
                                    <span class="h6">Surat Legal:</span>
                                    <a href="#" id="legalLetterLink" class="text-primary">Lihat Surat Legal</a>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                @if(auth()->user()->isOperator())
                                <button class="btn btn-primary btn-sm" onclick="assignToSelf()" id="assignBtn" style="display: none;">
                                    <i class="icon-base ti tabler-user-check me-2"></i>
                                    Ambil Tugas
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="updateStatus()" id="updateStatusBtn" style="display: none;">
                                    <i class="icon-base ti tabler-edit me-2"></i>
                                    Update Status
                                </button>
                                <button class="btn btn-success btn-sm" onclick="completeRequest()" id="completeBtn" style="display: none;">
                                    <i class="icon-base ti tabler-check me-2"></i>
                                    Selesaikan
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request Information and Documents -->
            <div class="col-xl-8 col-lg-7 col-md-7">
                <!-- Request Details Card -->
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Deskripsi Permintaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="description-content" id="requestDescription">
                                    Loading...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dokumen Pendukung</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="document-section">
                                    <h6 class="mb-3">Foto KTP</h6>
                                    <div class="document-preview" id="ktpDocument">
                                        <div class="text-center text-muted">
                                            <i class="icon-base ti tabler-photo fs-1 mb-2 d-block"></i>
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="document-section">
                                    <h6 class="mb-3">Foto KK</h6>
                                    <div class="document-preview" id="kkDocument">
                                        <div class="text-center text-muted">
                                            <i class="icon-base ti tabler-photo fs-1 mb-2 d-block"></i>
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Permintaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="newStatus" class="form-label">Status Baru</label>
                                <select class="form-select" id="newStatus" name="status" required>
                                    <option value="Pending">Menunggu</option>
                                    <option value="Processing">Diproses</option>
                                    <option value="Completed">Selesai</option>
                                </select>
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

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewTitle">Preview Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagePreviewContent" class="img-fluid" style="max-height: 70vh;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a id="imageDownloadLink" class="btn btn-primary" download>
                        <i class="icon-base ti tabler-download me-2"></i>
                        Download
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestId = {{ $requestLegalLetter->id }};
    const userRole = '{{ auth()->user()->role }}';
    const userId = {{ auth()->user()->id }};
    let requestData = null;

    // Load request data on page load
    loadRequestData();

    @if(auth()->user()->isOperator())
    // Update status form
    document.getElementById('updateStatusForm').addEventListener('submit', handleUpdateStatus);
    @endif

    function loadRequestData() {
        fetch(`/request-legal-letters/${requestId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                requestData = data.data;
                populateRequestData(requestData);
            } else {
                showToast('Gagal memuat data permintaan', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading request data:', error);
            showToast('Terjadi kesalahan saat memuat data permintaan', 'error');
        });
    }

    function populateRequestData(request) {
        // Basic info
        document.getElementById('requestAvatar').textContent = request.title.charAt(0).toUpperCase();
        document.getElementById('requestTitle').textContent = request.title;
        
        // Status
        const statusElement = document.getElementById('requestStatus');
        const statusClass = getStatusClass(request.status);
        const statusText = getStatusText(request.status);
        statusElement.className = `badge ${statusClass} rounded-pill`;
        statusElement.textContent = statusText;
        
        // Personal info
        document.getElementById('requestName').textContent = request.name;
        document.getElementById('requestNik').textContent = request.nik;
        document.getElementById('requesterName').textContent = request.requester ? request.requester.name : 'Unknown';
        document.getElementById('assigneeName').textContent = request.assigned_company ? request.assigned_company.name : 'Belum ditugaskan';
        
        // Dates
        const createdDate = new Date(request.created_at).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('requestCreatedDate').textContent = createdDate;
        
        const updatedDate = new Date(request.updated_at).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('requestUpdatedDate').textContent = updatedDate;
        
        // Description
        document.getElementById('requestDescription').innerHTML = request.description.replace(/\n/g, '<br>');
        
        // Legal letter link
        if (request.legal_letter) {
            document.getElementById('legalLetterInfo').style.display = 'block';
            document.getElementById('legalLetterLink').href = `/legal-letters/${request.legal_letter.id}`;
            document.getElementById('legalLetterLink').textContent = request.legal_letter.title;
        }
        
        // Documents
        populateDocuments(request);
        
        // Action buttons for operators
        if (userRole === 'Operator') {
            updateActionButtons(request);
        }
    }

    function populateDocuments(request) {
        // KTP Document
        const ktpContainer = document.getElementById('ktpDocument');
        if (request.ktp_image_url) {
            ktpContainer.innerHTML = `
                <img src="${request.ktp_image_url}" 
                     class="img-thumbnail cursor-pointer" 
                     style="width: 100%; height: 200px; object-fit: cover;"
                     onclick="previewImage('${request.ktp_image_url}', 'Foto KTP')" />
            `;
        } else {
            ktpContainer.innerHTML = `
                <div class="text-center text-muted p-4 border rounded">
                    <i class="icon-base ti tabler-photo-off fs-1 mb-2 d-block"></i>
                    Foto KTP tidak tersedia
                </div>
            `;
        }

        // KK Document
        const kkContainer = document.getElementById('kkDocument');
        if (request.kk_image_url) {
            kkContainer.innerHTML = `
                <img src="${request.kk_image_url}" 
                     class="img-thumbnail cursor-pointer" 
                     style="width: 100%; height: 200px; object-fit: cover;"
                     onclick="previewImage('${request.kk_image_url}', 'Foto KK')" />
            `;
        } else {
            kkContainer.innerHTML = `
                <div class="text-center text-muted p-4 border rounded">
                    <i class="icon-base ti tabler-photo-off fs-1 mb-2 d-block"></i>
                    Foto KK tidak tersedia
                </div>
            `;
        }
    }

    function updateActionButtons(request) {
        const assignBtn = document.getElementById('assignBtn');
        const updateStatusBtn = document.getElementById('updateStatusBtn');
        const completeBtn = document.getElementById('completeBtn');

        // Show assign button if not assigned to any company
        if (!request.assigned_company_id) {
            assignBtn.style.display = 'inline-block';
        } else {
            assignBtn.style.display = 'none';
        }

        // Show update status and complete buttons if assigned to current user's company
        if (request.assigned_company_id == `{{ auth()->user()->company_id }}`) {
            updateStatusBtn.style.display = 'inline-block';
            
            if (request.status !== 'Completed') {
                completeBtn.style.display = 'inline-block';
            } else {
                completeBtn.style.display = 'none';
            }
        } else {
            updateStatusBtn.style.display = 'none';
            completeBtn.style.display = 'none';
        }
    }

    function previewImage(imageUrl, title) {
        document.getElementById('imagePreviewTitle').textContent = title;
        document.getElementById('imagePreviewContent').src = imageUrl;
        document.getElementById('imageDownloadLink').href = imageUrl;
        
        new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
    }

    @if(auth()->user()->isOperator())
    function assignToSelf() {
        if (!confirm('Apakah Anda yakin ingin mengambil tugas ini?')) {
            return;
        }

        fetch(`/request-legal-letters/${requestId}/assign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Tugas berhasil diambil', 'success');
                loadRequestData(); // Reload to update UI
            } else {
                showToast(data.message || 'Gagal mengambil tugas', 'error');
            }
        })
        .catch(error => {
            console.error('Error assigning request:', error);
            showToast('Terjadi kesalahan saat mengambil tugas', 'error');
        });
    }

    function updateStatus() {
        if (requestData) {
            document.getElementById('newStatus').value = requestData.status;
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        }
    }

    function handleUpdateStatus(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');

        const jsonData = {
            status: formData.get('status')
        };

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/request-legal-letters/${requestId}/status`, {
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
                showToast('Status berhasil diupdate', 'success');
                bootstrap.Modal.getInstance(document.getElementById('updateStatusModal')).hide();
                loadRequestData(); // Reload to update UI
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

    function completeRequest() {
        if (!confirm('Apakah Anda yakin ingin menyelesaikan permintaan ini? Ini akan membuat surat legal baru.')) {
            return;
        }

        fetch(`/request-legal-letters/${requestId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Permintaan berhasil diselesaikan dan surat legal telah dibuat', 'success');
                loadRequestData(); // Reload to update UI
            } else {
                showToast(data.message || 'Gagal menyelesaikan permintaan', 'error');
            }
        })
        .catch(error => {
            console.error('Error completing request:', error);
            showToast('Terjadi kesalahan saat menyelesaikan permintaan', 'error');
        });
    }
    @endif

    // Utility functions
    function getStatusClass(status) {
        switch(status) {
            case 'Pending': return 'bg-label-warning';
            case 'Processing': return 'bg-label-info';
            case 'Completed': return 'bg-label-success';
            default: return 'bg-label-secondary';
        }
    }

    function getStatusText(status) {
        switch(status) {
            case 'Pending': return 'Menunggu';
            case 'Processing': return 'Diproses';
            case 'Completed': return 'Selesai';
            default: return status;
        }
    }

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
    window.previewImage = previewImage;
    @if(auth()->user()->isOperator())
    window.assignToSelf = assignToSelf;
    window.updateStatus = updateStatus;
    window.completeRequest = completeRequest;
    @endif
});
</script>

<style>
.cursor-pointer {
    cursor: pointer;
}

.document-preview img:hover {
    opacity: 0.8;
    transition: opacity 0.3s ease;
}
</style>
@endsection
