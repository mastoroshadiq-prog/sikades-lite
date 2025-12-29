    </div> <!-- End content-wrapper -->
    
    <!-- Footer -->
    <footer class="text-center py-4 mt-5" style="margin-left: 250px;">
        <div class="container-fluid">
            <p class="text-muted mb-0">
                &copy; <?= date('Y') ?> SiKaDes - Sistem Informasi Kawal Desa
                <span class="mx-2">|</span>
                <small>Sesuai Permendagri No. 20 Tahun 2018</small>
            </p>
        </div>
    </footer>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Select2 for searchable dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar?.contains(e.target) && !toggle?.contains(e.target)) {
                    sidebar?.classList.remove('show');
                }
            }
        });
        
        // DataTables initialization with destroy check
        $(document).ready(function() {
            // Destroy existing DataTables before reinitializing
            $('.data-table').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().destroy();
                }
            });
            
            // Initialize DataTables
            $('.data-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 25,
                responsive: true,
                destroy: true  // Allow re-initialization
            });
            
            // Initialize Select2 for all searchable dropdowns
            $('.select2-search').select2({
                theme: 'bootstrap-5',
                placeholder: function() {
                    return $(this).data('placeholder') || '-- Pilih --';
                },
                allowClear: true,
                width: '100%'
            });
        });
        
        // Toast notification helper
        function showToast(icon, title, message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: title,
                text: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
        
        // Confirm delete helper
        function confirmDelete(url, itemName) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus ${itemName}? Data yang dihapus tidak dapat dikembalikan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send DELETE request
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('success', 'Berhasil', data.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast('error', 'Gagal', data.message);
                        }
                    })
                    .catch(error => {
                        showToast('error', 'Error', 'Terjadi kesalahan sistem');
                    });
                }
            });
        }
        
        // Format currency (IDR)
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);
        }
        
        // Show flash messages
        <?php if (session()->has('success')): ?>
            showToast('success', 'Berhasil', '<?= session('success') ?>');
        <?php endif; ?>
        
        <?php if (session()->has('error')): ?>
            showToast('error', 'Error', '<?= session('error') ?>');
        <?php endif; ?>
        
        <?php if (session()->has('info')): ?>
            showToast('info', 'Informasi', '<?= session('info') ?>');
        <?php endif; ?>
    </script>
</body>
</html>
