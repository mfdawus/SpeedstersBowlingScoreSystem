<?php
/**
 * Admin Popup Component
 * Shows popup for admin users when accessing maintenance pages
 */

require_once 'maintenance-bypass.php';

if (shouldShowAdminPopup()) {
    $popupMessage = $_SESSION['admin_popup_message'] ?? "Sorry doh taufiq belum siap part ni";
    clearAdminPopup(); // Clear the flag after showing popup
    ?>
    
    <!-- Admin Popup Modal -->
    <div class="modal fade" id="adminPopupModal" tabindex="-1" aria-labelledby="adminPopupModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="adminPopupModalLabel">
                        <i class="ti ti-tools me-2"></i>
                        Development Notice
                    </h5>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="ti ti-alert-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="mb-3"><?php echo htmlspecialchars($popupMessage); ?></h6>
                    <p class="text-muted mb-0">
                        This page is still under development. You can proceed to view the current state.
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">
                        <i class="ti ti-check me-1"></i>
                        Got it, proceed
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show popup automatically when page loads
        var adminPopupModal = new bootstrap.Modal(document.getElementById('adminPopupModal'));
        adminPopupModal.show();
        
        // Add some fun animation
        document.getElementById('adminPopupModal').addEventListener('shown.bs.modal', function() {
            const icon = this.querySelector('.ti-alert-triangle');
            icon.style.animation = 'pulse 1s ease-in-out';
        });
    });
    </script>

    <style>
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .modal-content {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .modal-header.bg-warning {
        border-bottom: 1px solid #ffc107;
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
        border: none;
        color: #000;
        font-weight: 600;
    }
    
    .btn-warning:hover {
        background: linear-gradient(135deg, #ff8c00 0%, #ff6b00 100%);
        color: #000;
        transform: translateY(-1px);
    }
    </style>
    
    <?php
}
?>
