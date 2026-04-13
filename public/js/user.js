// --------------------- script for navbar-----------------------------
// for sidebar menu
  function toggleSidebar() {
    document.getElementById('mobileSidebar').classList.toggle('active');
    document.getElementById('sidebarOverlay').classList.toggle('active');
  }

  // for dropdown
  document.querySelectorAll('.mobile-dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
      toggle.parentElement.classList.toggle('active');
    });
  });


    // --------------------- script for navbar-----------------------------