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
        <button id="profile-button" class="btn-outline" onclick="window.location.href='donor_profile.html'">Profile</button>
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

  updateProfileButton();
}

async function updateProfileButton() {
  try {
    const response = await fetch('../backend/get_user_profile.php', {
      method: 'GET',
      credentials: 'include'
    });

    if (!response.ok) {
      return;
    }

    const data = await response.json();
    if (data.success && data.user) {
      const profileButton = document.getElementById('profile-button');
      if (profileButton) {
        const userName = data.user.full_name || 'Profile';
        const firstName = userName.split(' ')[0];
        profileButton.textContent = `${firstName}'s Profile`;
      }
    }
  } catch (error) {
    console.warn('Unable to update profile button:', error);
  }
}

// Load navbar when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadNavbar);
} else {
  loadNavbar();
}

