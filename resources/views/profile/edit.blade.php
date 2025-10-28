@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg me-4">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1">Profil Saya</h4>
                                <p class="mb-0 text-muted">Kelola informasi akun dan keamanan Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-6">
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Profil</h5>
                        <small class="text-muted">Perbarui informasi profil dan alamat email Anda</small>
                    </div>
                    <div class="card-body">
                        <form id="profileForm">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="role" class="form-label">Role</label>
                                    <input type="text" class="form-control" id="role" value="{{ $user->role }}" readonly>
                                    <div class="form-text">Role tidak dapat diubah</div>
                                </div>
                            </div>
                            @if($user->company)
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="company" class="form-label">Desa</label>
                                    <input type="text" class="form-control" id="company" value="{{ $user->company->name }}" readonly>
                                    <div class="form-text">Desa tidak dapat diubah</div>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="col-md-6">
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ubah Password</h5>
                        <small class="text-muted">Pastikan akun Anda menggunakan password yang kuat</small>
                    </div>
                    <div class="card-body">
                        <form id="passwordForm">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">Minimal 8 karakter</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-warning">
                                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                                        Ubah Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="icon-base ti tabler-calendar me-3 text-muted"></i>
                                    <div>
                                        <h6 class="mb-0">Akun Dibuat</h6>
                                        <small class="text-muted">{{ $user->created_at->format('d F Y, H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="icon-base ti tabler-clock me-3 text-muted"></i>
                                    <div>
                                        <h6 class="mb-0">Terakhir Diperbarui</h6>
                                        <small class="text-muted">{{ $user->updated_at->format('d F Y, H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile form submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
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

            fetch('{{ route("profile.update") }}', {
                method: 'PUT',
                body: JSON.stringify({
                    name: formData.get('name'),
                    email: formData.get('email')
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
                    showToast(data.message, 'success');
                    // Update the navbar user name if it exists
                    const navbarUserName = document.querySelector('.navbar .dropdown-item h6');
                    if (navbarUserName) {
                        navbarUserName.textContent = formData.get('name');
                    }
                } else {
                    if (data.errors) {
                        showFormErrors(form, data.errors);
                    } else {
                        showToast(data.message || 'Terjadi kesalahan', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error updating profile:', error);
                showToast('Terjadi kesalahan saat memperbarui profil', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            });
        });

        // Password form submission
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
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

            fetch('{{ route("profile.update-password") }}', {
                method: 'PUT',
                body: JSON.stringify({
                    current_password: formData.get('current_password'),
                    password: formData.get('password'),
                    password_confirmation: formData.get('password_confirmation')
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
                    showToast(data.message, 'success');
                    form.reset();
                } else {
                    if (data.errors) {
                        showFormErrors(form, data.errors);
                    } else {
                        showToast(data.message || 'Terjadi kesalahan', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error updating password:', error);
                showToast('Terjadi kesalahan saat mengubah password', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            });
        });

        // Helper functions
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

        function showToast(message, type) {
            // Simple toast implementation - you can replace with your preferred toast library
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
    });
    </script>
@endsection
