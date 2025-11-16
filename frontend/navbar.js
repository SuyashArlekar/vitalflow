// Common Navbar Component
function loadNavbar() {
  const navbarHTML = `
    <header class="navbar">
      <div class="logo">🩸 BloodBank</div>
      <nav>
        <ul>
          <li><a href="homepage.html">Home</a></li>
          <li><a href="donationcamps.html">Donation Camps</a></li>
          <li><a href="about.html">About</a></li>
          <li><a href="contact_page.html">Contact</a></li>
        </ul>
      </nav>
      <div class="nav-buttons">
        <button class="btn-outline" onclick="window.location.href='register.html'">Sign In</button>
        <button class="btn-primary" onclick="window.location.href='donationcamps.html'">Donate Now</button>
      </div>
    </header>
  `;
  
  // Insert navbar into placeholder or at the beginning of body
  const placeholder = document.getElementById('navbar-placeholder');
  if (placeholder) {
    placeholder.outerHTML = navbarHTML;
  } else {
    document.body.insertAdjacentHTML('afterbegin', navbarHTML);
  }
}

// Load navbar when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadNavbar);
} else {
  loadNavbar();
}

