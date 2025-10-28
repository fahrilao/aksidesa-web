@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-12">
                <h4 class="py-3 mb-4">
                    <span class="text-muted fw-light">Manajemen /</span> Permintaan Surat Legal
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
                                <span class="text-heading">Total Permintaan</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="totalRequests">0</h4>
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
                                <span class="text-heading">Menunggu</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="pendingRequests">0</h4>
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
                                <span class="text-heading">Diproses</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="processingRequests">0</h4>
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="icon-base ti tabler-progress"></i>
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
                                <span class="text-heading">Selesai</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2" id="completedRequests">0</h4>
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
        </div>

        <!-- Request Legal Letters List Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Permintaan Surat Legal</h5>
                @if(auth()->user()->role === 'RW')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRequestModal">
                    <i class="icon-base ti tabler-plus me-2"></i>
                    Buat Permintaan
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
                            <input type="text" class="form-control" id="searchRequests" placeholder="Cari judul, nama, NIK, atau deskripsi...">
                        </div>
                        <div class="form-text">Pencarian akan mencari di judul, nama pemohon, NIK, dan deskripsi permintaan</div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="Pending">Menunggu</option>
                            <option value="Processing">Diproses</option>
                            <option value="Completed">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="sortBy">
                            <option value="created_at">Tanggal Dibuat</option>
                            <option value="title">Judul</option>
                            <option value="name">Nama</option>
                            <option value="nik">NIK</option>
                            <option value="status">Status</option>
                        </select>
                    </div>
                </div>

                <!-- Requests Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Nama & NIK</th>
                                <th>Status</th>
                                <th>Ditugaskan Ke</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            <!-- Requests will be loaded here via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center" id="requestsPagination">
                        <!-- Pagination will be loaded here via JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'RW')
    <!-- Create Request Modal -->
    <div class="modal fade" id="createRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Permintaan Surat Legal Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createRequestForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="createTitle" class="form-label">Judul Permintaan</label>
                                <input type="text" class="form-control" id="createTitle" name="title" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="createName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="createName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="createNik" class="form-label">NIK</label>
                                <input type="text" class="form-control" id="createNik" name="nik" maxlength="16" required>
                                <div class="form-text">16 digit NIK sesuai KTP</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="createDescription" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="createDescription" name="description" rows="4" required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="createKtpImage" class="form-label">Foto KTP</label>
                                <input type="file" class="form-control" id="createKtpImage" name="ktp_image" accept="image/*" required>
                                <div class="form-text">Format: JPG, PNG. Maksimal 2MB</div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="createKkImage" class="form-label">Foto KK</label>
                                <input type="file" class="form-control" id="createKkImage" name="kk_image" accept="image/*" required>
                                <div class="form-text">Format: JPG, PNG. Maksimal 2MB</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="preview-container" id="ktpPreview" style="display: none;">
                                    <label class="form-label">Preview KTP</label>
                                    <img class="img-thumbnail" style="max-height: 200px; width: 100%;" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="preview-container" id="kkPreview" style="display: none;">
                                    <label class="form-label">Preview KK</label>
                                    <img class="img-thumbnail" style="max-height: 200px; width: 100%;" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                            Kirim Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
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
                    <input type="hidden" id="updateStatusRequestId" name="request_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="updateStatusSelect" class="form-label">Status Baru</label>
                                <select class="form-select" id="updateStatusSelect" name="status" required>
                                    <option value="Pending">Menunggu</option>
                                    <option value="Processing">Diproses</option>
                                    <option value="Completed">Selesai</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="updateStatusNotes" class="form-label">Catatan (Opsional)</label>
                                <textarea class="form-control" id="updateStatusNotes" name="notes" rows="3" placeholder="Tambahkan catatan perubahan status..."></textarea>
                                <div class="form-text">Catatan ini akan membantu melacak perubahan status</div>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const userRole = '{{ auth()->user()->role }}';

    // Load requests and statistics on page load
    loadRequests();
    loadStatistics();

    // Update status form handler
    document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = submitBtn.querySelector('.spinner-border');
        const requestId = document.getElementById('updateStatusRequestId').value;

        // Clear previous errors
        clearFormErrors(form);
        
        // Show loading
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`/request-legal-letters/${requestId}/status`, {
            method: 'PUT',
            body: JSON.stringify({
                status: formData.get('status'),
                notes: formData.get('notes')
            }),
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
                form.reset();
                loadRequests();
                loadStatistics();
            } else {
                if (data.errors) {
                    showFormErrors(form, data.errors);
                } else {
                    showToast(data.message || 'Gagal mengupdate status', 'error');
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
    });

    // Search functionality
    document.getElementById('searchRequests').addEventListener('input', function() {
        currentPage = 1;
        loadRequests();
    });

    // Filter functionality
    document.getElementById('filterStatus').addEventListener('change', function() {
        currentPage = 1;
        loadRequests();
    });

    // Sort functionality
    document.getElementById('sortBy').addEventListener('change', function() {
        currentPage = 1;
        loadRequests();
    });

    @if(auth()->user()->role === 'RW')
    // Create request form
    document.getElementById('createRequestForm').addEventListener('submit', handleCreateRequest);

    // File preview functionality
    document.getElementById('createKtpImage').addEventListener('change', function(e) {
        previewImage(e.target, 'ktpPreview');
    });

    document.getElementById('createKkImage').addEventListener('change', function(e) {
        previewImage(e.target, 'kkPreview');
    });
    @endif

    function loadRequests() {
        const search = document.getElementById('searchRequests').value;
        const status = document.getElementById('filterStatus').value;
        const sortBy = document.getElementById('sortBy').value;
        
        const params = new URLSearchParams({
            page: currentPage,
            sort_by: sortBy,
            sort_order: 'desc'
        });

        if (search) params.append('search', search);
        if (status) params.append('status', status);

        fetch(`/request-legal-letters?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderRequestsTable(data.data.data);
                renderPagination(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading requests:', error);
            showToast('Terjadi kesalahan saat memuat data permintaan', 'error');
        });
    }

    function loadStatistics() {
        fetch('/request-legal-letters-statistics', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalRequests').textContent = data.data.total;
                document.getElementById('pendingRequests').textContent = data.data.by_status.Pending;
                document.getElementById('processingRequests').textContent = data.data.by_status.Processing;
                document.getElementById('completedRequests').textContent = data.data.by_status.Completed;
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
    }

    function renderRequestsTable(requests) {
        const tbody = document.getElementById('requestsTableBody');
        
        if (requests.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="icon-base ti tabler-file-text fs-1 mb-2 d-block"></i>
                            Tidak ada data permintaan
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = requests.map(request => {
            const createdDate = new Date(request.created_at).toLocaleDateString('id-ID');
            const statusClass = getStatusClass(request.status);
            const statusText = getStatusText(request.status);

            return `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    ${request.title.charAt(0).toUpperCase()}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">${request.title}</h6>
                                <small class="text-muted">${request.description.length > 50 ? request.description.substring(0, 50) + '...' : request.description}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <h6 class="mb-0">${request.name}</h6>
                            <small class="text-muted">NIK: ${request.nik}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${statusClass}">${statusText}</span>
                    </td>
                    <td>
                        ${request.assigned_company ? `<span class="badge bg-label-success">${request.assigned_company.name}</span>` : '<span class="text-muted">Belum ditugaskan</span>'}
                    </td>
                    <td>${createdDate}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="icon-base ti tabler-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="viewRequest(${request.id})">
                                    <i class="icon-base ti tabler-eye me-2"></i> Lihat Detail
                                </a>
                                ${userRole === 'Operator' && !request.assigned_company ? `
                                <a class="dropdown-item" href="javascript:void(0);" onclick="assignToSelf(${request.id})">
                                    <i class="icon-base ti tabler-user-check me-2"></i> Ambil Tugas
                                </a>
                                ` : ''}
                                ${userRole === 'Operator' && request.assigned_company && request.status !== 'Completed' ? `
                                <a class="dropdown-item" href="javascript:void(0);" onclick="updateStatus(${request.id}, '${request.status}')">
                                    <i class="icon-base ti tabler-edit me-2"></i> Update Status
                                </a>
                                ` : ''}
                                ${userRole === 'Operator' && request.assigned_company && request.status !== 'Completed' ? `
                                <a class="dropdown-item" href="javascript:void(0);" onclick="completeRequest(${request.id})">
                                    <i class="icon-base ti tabler-check me-2"></i> Selesaikan
                                </a>
                                ` : ''}
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderPagination(paginationData) {
        const pagination = document.getElementById('requestsPagination');
        
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
        loadRequests();
    }

    @if(auth()->user()->role === 'RW')
    function handleCreateRequest(e) {
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

        fetch('/request-legal-letters', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Permintaan berhasil dikirim', 'success');
                bootstrap.Modal.getInstance(document.getElementById('createRequestModal')).hide();
                form.reset();
                clearPreviews();
                loadRequests();
                loadStatistics();
            } else {
                if (data.errors) {
                    showFormErrors(form, data.errors);
                } else {
                    showToast(data.message || 'Terjadi kesalahan', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error creating request:', error);
            showToast('Terjadi kesalahan saat mengirim permintaan', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    }

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const img = preview.querySelector('img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }

    function clearPreviews() {
        document.getElementById('ktpPreview').style.display = 'none';
        document.getElementById('kkPreview').style.display = 'none';
    }
    @endif

    function viewRequest(id) {
        window.location.href = `/request-legal-letters/${id}`;
    }

    function assignToSelf(id) {
        if (!confirm('Apakah Anda yakin ingin mengambil tugas ini?')) {
            return;
        }

        fetch(`/request-legal-letters/${id}/assign`, {
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
                loadRequests();
                loadStatistics();
            } else {
                showToast(data.message || 'Gagal mengambil tugas', 'error');
            }
        })
        .catch(error => {
            console.error('Error assigning request:', error);
            showToast('Terjadi kesalahan saat mengambil tugas', 'error');
        });
    }

    function updateStatus(id, currentStatus) {
        // Set the request ID and current status in the modal
        document.getElementById('updateStatusRequestId').value = id;
        document.getElementById('updateStatusSelect').value = currentStatus;
        document.getElementById('updateStatusNotes').value = '';
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        modal.show();
    }

    function completeRequest(id) {
        if (!confirm('Apakah Anda yakin ingin menyelesaikan permintaan ini? Ini akan membuat surat legal baru.')) {
            return;
        }

        fetch(`/request-legal-letters/${id}/complete`, {
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
                loadRequests();
                loadStatistics();
            } else {
                showToast(data.message || 'Gagal menyelesaikan permintaan', 'error');
            }
        })
        .catch(error => {
            console.error('Error completing request:', error);
            showToast('Terjadi kesalahan saat menyelesaikan permintaan', 'error');
        });
    }

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
    window.viewRequest = viewRequest;
    window.assignToSelf = assignToSelf;
    window.updateStatus = updateStatus;
    window.completeRequest = completeRequest;
    window.changePage = changePage;
});
</script>
@endsection
