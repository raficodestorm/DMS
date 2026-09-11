// --------------------- Scripts for Navbar & Theme ---------------------

// Sidebar Menu Toggle
function toggleSidebar() {
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
}

// Mobile Search Bar Drawer Toggle
function toggleMobileSearch() {
    const searchDrawer = document.getElementById('mobileSearchDrawer');
    if (searchDrawer) {
        searchDrawer.classList.toggle('active');
        if (searchDrawer.classList.contains('active')) {
            const input = searchDrawer.querySelector('input');
            if (input) setTimeout(() => input.focus(), 100);
        }
    }
}

// Mobile Category Accordion Toggle
function toggleMobileCategory() {
    const accordion = document.getElementById('mobileCatAccordion');
    if (accordion) {
        accordion.classList.toggle('active');
    }
}

// Desktop Category Dropdown Click Toggle (if clicked)
document.addEventListener('DOMContentLoaded', () => {
    const browseBtn = document.getElementById('browseCatBtn');
    const dropdownWrap = document.getElementById('categoriesDropdownWrap');
    if (browseBtn && dropdownWrap) {
        browseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownWrap.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownWrap.contains(e.target)) {
                dropdownWrap.classList.remove('active');
            }
        });
    }

    updateThemeIcons();
});

// Theme Switcher (Dark / Light Mode)
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.getAttribute("data-theme") === "dark";

    if (isDark) {
        html.removeAttribute("data-theme");
        localStorage.setItem("theme", "light");
    } else {
        html.setAttribute("data-theme", "dark");
        localStorage.setItem("theme", "dark");
    }
    updateThemeIcons();
}

function updateThemeIcons() {
    const isDark = document.documentElement.getAttribute("data-theme") === "dark";
    document.querySelectorAll(".theme-toggle-icon").forEach(icon => {
        if (isDark) {
            icon.classList.remove("fa-moon");
            icon.classList.add("fa-sun");
        } else {
            icon.classList.remove("fa-sun");
            icon.classList.add("fa-moon");
        }
    });
}