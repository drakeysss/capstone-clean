/**
 * COMPREHENSIVE MODAL FIXES
 * Fixes all modal backdrop, z-index, and display issues
 */

(function() {
    'use strict';
    
    // Prevent multiple initializations
    if (window.ModalFixesInitialized) {
        return;
    }
    window.ModalFixesInitialized = true;

    console.log('🔧 Initializing comprehensive modal fixes...');

    // Global modal cleanup function
    function cleanupAllModalStates() {
        console.log('🧹 Cleaning up all modal states...');
        
        // Remove all modal backdrops
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => {
            console.log('Removing backdrop:', backdrop);
            backdrop.remove();
        });
        
        // Reset body classes and styles
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Hide all modals
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            modal.removeAttribute('aria-modal');
            modal.removeAttribute('role');
        });
        
        console.log('✅ Modal states cleaned up');
    }

    // Safe modal show function
    function showModalSafely(modalId) {
        console.log('🔄 Showing modal safely:', modalId);
        
        // First cleanup any existing modal states
        cleanupAllModalStates();
        
        // Wait a moment for cleanup to complete
        setTimeout(() => {
            const modalElement = document.getElementById(modalId);
            if (!modalElement) {
                console.error('❌ Modal not found:', modalId);
                return;
            }
            
            // Ensure proper z-index
            modalElement.style.zIndex = '1060';
            
            // Show modal
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: true
            });
            
            modal.show();
            
            // Force proper z-index after show
            setTimeout(() => {
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.style.zIndex = '1055';
                }
                modalElement.style.zIndex = '1060';
            }, 100);
            
            console.log('✅ Modal shown successfully:', modalId);
        }, 50);
    }

    // Safe modal hide function
    function hideModalSafely(modalId) {
        console.log('🔄 Hiding modal safely:', modalId);
        
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            console.error('❌ Modal not found:', modalId);
            return;
        }
        
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
        
        // Force cleanup after hide
        setTimeout(() => {
            cleanupAllModalStates();
        }, 300);
        
        console.log('✅ Modal hidden successfully:', modalId);
    }

    // Override Bootstrap modal events to ensure proper cleanup
    document.addEventListener('hidden.bs.modal', function(event) {
        console.log('🔄 Modal hidden event triggered:', event.target.id);
        setTimeout(() => {
            cleanupAllModalStates();
        }, 100);
    });

    // Fix for stuck modals on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔄 DOM loaded - cleaning up any stuck modals...');
        cleanupAllModalStates();
    });

    // Emergency cleanup on window focus (in case user switches tabs)
    window.addEventListener('focus', function() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        if (backdrops.length > 0) {
            console.log('🔄 Window focus - cleaning up stuck backdrops...');
            cleanupAllModalStates();
        }
    });

    // Keyboard shortcut for emergency cleanup (Ctrl+Alt+M)
    document.addEventListener('keydown', function(event) {
        if (event.ctrlKey && event.altKey && event.key === 'm') {
            console.log('🚨 Emergency modal cleanup triggered by keyboard shortcut');
            cleanupAllModalStates();
            event.preventDefault();
        }
    });

    // Make functions available globally
    window.ModalFixes = {
        cleanup: cleanupAllModalStates,
        showModal: showModalSafely,
        hideModal: hideModalSafely
    };

    // Also make individual functions available
    window.cleanupModalStates = cleanupAllModalStates;
    window.showModalSafely = showModalSafely;
    window.hideModalSafely = hideModalSafely;

    console.log('✅ Comprehensive modal fixes initialized successfully');
    console.log('💡 Available functions:');
    console.log('   - window.ModalFixes.cleanup()');
    console.log('   - window.ModalFixes.showModal(modalId)');
    console.log('   - window.ModalFixes.hideModal(modalId)');
    console.log('   - Emergency cleanup: Ctrl+Alt+M');

})();
