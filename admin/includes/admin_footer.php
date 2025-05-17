<script>
// JavaScript for dropdown toggle
document.addEventListener('DOMContentLoaded', function() {
    // Language dropdown
    const languageMenu = document.getElementById('language-menu');
    if (languageMenu) {
        const dropdown = document.querySelector('[role="menu"]');
        let isOpen = false;
        
        languageMenu.addEventListener('click', function() {
            isOpen = !isOpen;
            dropdown.style.display = isOpen ? 'block' : 'none';
        });
        
        // Close when clicking outside
        document.addEventListener('click', function(event) {
            if (!languageMenu.contains(event.target) && !dropdown.contains(event.target)) {
                isOpen = false;
                dropdown.style.display = 'none';
            }
        });
    }
    
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const closeMobileMenu = document.getElementById('close-mobile-menu');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const mobileOverlay = document.getElementById('mobile-menu-overlay');
    
    if (mobileMenuButton && mobileSidebar) {
        mobileMenuButton.addEventListener('click', function() {
            mobileSidebar.classList.add('active');
            mobileOverlay.classList.remove('hidden');
        });
        
        if (closeMobileMenu) {
            closeMobileMenu.addEventListener('click', function() {
                mobileSidebar.classList.remove('active');
                mobileOverlay.classList.add('hidden');
            });
        }
        
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function() {
                mobileSidebar.classList.remove('active');
                mobileOverlay.classList.add('hidden');
            });
        }
    }
});
</script>
</body>
</html>