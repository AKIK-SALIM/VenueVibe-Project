<?php require_once 'guard.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VenueVibe - Venue Management</title>
  <link rel="stylesheet" href="css/style.css?v=20261013">
  <link rel="stylesheet" href="css/venues.css?v=20261013">
</head>
<body onload="initPage()">

  <div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <a href="index.php">VenueVibe</a>
    </div>
    <nav>
      <a class="sidebar-link" href="index.php">
        <span class="nav-ic">
          <svg width="17" height="17" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="2" width="6" height="6" rx="1.5"/><rect x="12" y="2" width="6" height="6" rx="1.5"/><rect x="2" y="12" width="6" height="6" rx="1.5"/><rect x="12" y="12" width="6" height="6" rx="1.5"/></svg>
        </span>Dashboard
      </a>
      <a class="sidebar-link active" href="venues.php">
        <span class="nav-ic">
          <svg width="17" height="17" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 18V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13"/><path d="M2 18h16"/><circle cx="12.5" cy="10" r="1" fill="currentColor"/></svg>
        </span>Venues
      </a>
      <a class="sidebar-link" href="requests.php">
        <span class="nav-ic">
          <svg width="17" height="17" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="3" width="12" height="14" rx="2"/><path d="M7 7h6M7 10.5h6M7 14h4"/></svg>
        </span>Requests
      </a>
      <a class="sidebar-link" href="announcements.php">
        <span class="nav-ic">
          <svg width="17" height="17" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 10V6a1 1 0 0 1 1-1h8l4-3v12l-4-3H4a1 1 0 0 1-1-1z"/><path d="M5 14v3"/></svg>
        </span>Announcements
      </a>
      <a class="sidebar-link" href="messages.php">
        <span class="nav-ic">
          <svg width="17" height="17" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 5h14v9H8l-5 4V5z"/></svg>
        </span>Messages
      </a>
    </nav>
    <div class="sidebar-footer">
      <p style="margin:0;font-weight:700;color:#475467">VenueVibe v1.0</p>
      <p style="margin:4px 0 6px 0">Campus Facilities Portal</p>
      <a href="../User/logout.php" style="font-size:12px;font-weight:700;color:#4f39f6;text-decoration:none">Logout</a>
    </div>
  </div>

  <div class="main">
    <div class="topbar">
      <h1><button class="menu-btn" onclick="toggleMenu()">☰</button> Venue Management</h1>
      <div class="topbar-right">
        <span class="profile-wrap">
          <span class="profile-vector" onclick="toggleAdminProfile()" title="Profile details"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4f39f6" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.2-3.8 4.2-5.5 8-5.5s6.8 1.7 8 5.5"/></svg></span>
          <span class="green-dot"></span>
        </span>
      </div>
      <div class="clear"></div>
    </div>

    <div class="content">
      <div class="page-top">
        <button class="btn top-btn" onclick="openDrawer()">＋ Add New Venue</button>
        <div class="page-path">VenueVibe / Venues / <b>Directory & Facility Operations</b></div>
        <!-- <p class="page-title">Venue Management</p> -->
        
        <div class="clear"></div>
      </div>

      <!-- stat cards -->
      <div class="mini-row">
        <div class="mini-stat">
          <div class="mini-icon" style="background:#eef0ff;color:#4f39f6">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 21V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16"/><path d="M3 21h18"/><path d="M15 8h4a1 1 0 0 1 1 1v12"/></svg>
          </div>
          <div class="mini-label">TOTAL CAMPUS SPACES</div>
          <div class="mini-num-row"><span class="mini-num" id="statTotal">5</span></div>
        </div>
        <div class="mini-stat">
          <div class="mini-icon" style="background:#e7f8ef;color:#12a56f">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8.5"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
          </div>
          <div class="mini-label">ACTIVE &amp; BOOKABLE</div>
          <div class="mini-num-row"><span class="mini-num" id="statActive">4</span></div>
        </div>
        <div class="mini-stat">
          <div class="mini-icon" style="background:#fef3c7;color:#b45309">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14.5 6.5a4 4 0 0 0-5.6 5L4 16.4V20h3.6l4.9-4.9a4 4 0 0 0 5-5.6l-3 3-2.5-2.5 2.5-2.5z"/></svg>
          </div>
          <div class="mini-label">UNDER MAINTENANCE</div>
          <div class="mini-num-row"><span class="mini-num" id="statMaint">1</span></div>
        </div>
        <div class="mini-stat last">
          <div class="mini-icon" style="background:#f1f5f9;color:#64748b">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 9.5h16M8.5 3v4M15.5 3v4"/></svg>
          </div>
          <div class="mini-label">DECOMMISSIONED / DRAFT</div>
          <div class="mini-num-row"><span class="mini-num" id="statDraft">0</span></div>
        </div>
        <div class="clear"></div>
      </div>

      <!-- filter bar -->
      <div class="filter-box">
        <span class="search-wrap">
          <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="#98a2b3" stroke-width="2"><circle cx="9" cy="9" r="6"/><path d="M13.5 13.5L17 17"/></svg>
          <input class="search-input" id="venueSearch" placeholder="Search by name / Room ID..." onkeyup="onSearchChange(this.value)">
        </span>
        <span class="select-wrap">
          <select class="filter-select" id="quadSelect" onchange="onQuadChange(this.value)"></select>
          <span class="select-arrow">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="#475569" stroke-width="2.2"><path d="M4.5 7.5L10 13l5.5-5.5"/></svg>
          </span>
        </span>
        <span class="select-wrap">
          <select class="filter-select" id="statusSelect" onchange="onStatusChange(this.value)">
            <option value="All">Status: All</option>
            <option value="Active">Active &amp; Bookable</option>
            <option value="Maintenance">Under Maintenance</option>
            <option value="Draft">Draft / Under Review</option>
          </select>
          <span class="select-arrow">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="#475569" stroke-width="2.2"><path d="M4.5 7.5L10 13l5.5-5.5"/></svg>
          </span>
        </span>
        <button class="reset-btn" onclick="resetVenueFilters()">Reset Filters</button>
      </div>

      <!-- venue cards -->
      <div class="venue-grid" id="venueGrid"></div>
      <div id="venuePagination" style="display:flex;gap:6px;justify-content:center;margin:20px 0;flex-wrap:wrap"></div>
    </div>
  </div>

  <!-- add venue drawer -->
  <div class="drawer-bg" id="drawerBg" style="display:none">
    <div class="drawer">
      <div class="drawer-head">
        <div class="drawer-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"><path d="M5 21V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16"/><path d="M3 21h18"/><path d="M15 8h4a1 1 0 0 1 1 1v12"/></svg>
        </div>
        <div>
          <h2 id="drawerTitle">Add New Venue</h2>
          <p>Register a new campus space, configure capacities, and upload imagery.</p>
        </div>
        <button class="close-btn" onclick="closeDrawer()">&#10005;</button>
      </div>

      <div class="drawer-body">
        <label class="d-label">Venue Name <span class="red">*</span></label>
        <input class="d-input" id="fName" value="" placeholder="e.g. Centennial Auditorium & Science Theater">

        <div class="d-row">
          <div class="d-col">
            <label class="d-label">Campus Quad <span class="red">*</span></label>
            <span class="d-select-wrap">
              <select class="d-input" id="fQuad">
                <option>North Academic Quad</option>
                <option>West Wing</option>
                <option>Student Union</option>
                <option>Science Complex</option>
                <option>South Quad</option>
              </select>
              <span class="d-chevron">
                <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="#475569" stroke-width="2.2"><path d="M4.5 7.5L10 13l5.5-5.5"/></svg>
              </span>
            </span>
          </div>
          <div class="d-col">
            <label class="d-label">Building / Complex <span class="red">*</span></label>
            <input class="d-input" id="fBuilding" value="" placeholder="e.g. Hall of Sciences, Bldg 4">
          </div>
        </div>

        <div class="d-row">
          <div class="d-col">
            <label class="d-label">Floor Level <span class="red">*</span></label>
            <input class="d-input" id="fFloor" value="" placeholder="e.g. Floor 3">
          </div>
          <div class="d-col">
            <label class="d-label">Room / Suite Number <span class="red">*</span></label>
            <input class="d-input" id="fRoom" value="" placeholder="e.g. Room 302">
          </div>
        </div>

        <label class="d-label">Venue Type in Campus <span class="red">*</span></label>
        <div id="typeButtons" class="pill-row"></div>

        <div class="d-row">
          <div class="d-col">
            <label class="d-label">Seated Capacity <span class="red">*</span></label>
            <input class="d-input" id="fSeated" type="number" value="" placeholder="e.g. 350">
          </div>
          <div class="d-col">
            <label class="d-label">Standing / Fire Code Max</label>
            <input class="d-input" id="fStanding" type="number" value="" placeholder="e.g. 420">
          </div>
        </div>

        <label class="d-label">Attendee Email</label>
        <input class="d-input" id="fAttendee" type="email" value="" placeholder="e.g. attendee@uiu.ac.bd">

        <label class="d-label">Images &amp; Visual Media</label>
        <div class="dropzone">
          <div class="drop-icon">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="#4f39f6" stroke-width="1.8"><path d="M10 13V3M6 7l4-4 4 4"/><path d="M3 15v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2"/></svg>
          </div>
          <b>Drop venue photos or browse</b>
          <div class="drop-sub">High-resolution JPG or PNG up to 20MB per photo</div>
          <button type="button" class="drop-btn" onclick="document.getElementById('fMedia').click()">Select Files from Campus Media</button>
          <input type="file" id="fMedia" name="files[]" accept="image/jpeg,image/png" multiple style="display:none" onchange="uploadVenueMedia(this)">
          <div id="fMediaLabel" style="font-size:11px;color:#64748b;margin-top:6px"></div>
        </div>

        <label class="d-label">Operational Status &amp; Amenities</label>
        <div class="radio-row">
          <label><input type="radio" id="statusActive" name="vstatus" checked onclick="pickStatus('Active')"> Active &amp; Bookable</label>
          <label><input type="radio" id="statusDraft" name="vstatus" onclick="pickStatus('Draft')"> Draft / Under Review</label>
        </div>
        <div id="amenityButtons" class="amen-grid"></div>
      </div>

      <div class="drawer-foot">
        <button class="small-btn" onclick="closeDrawer()">Cancel</button>
        <button class="small-btn" onclick="closeDrawer()">Save Draft</button>
        <button class="btn" id="drawerSaveBtn" onclick="saveVenue()">&#10003; Save &amp; Add Venue</button>
      </div>
    </div>
  </div>

  <!-- venue detail widget -->
  <div class="modal-bg" id="venueModal" style="display:none">
    <div class="modal-box venue-detail-box">
      <button class="detail-close" onclick="closeVenueDetail()">&#10005;</button>
      <div id="venueDetailBody"></div>
    </div>
  </div>

  <script src="js/data.js?v=20261013"></script>
  <script src="js/sidebar.js?v=20261013"></script>
  <script src="js/venues.js?v=20261013"></script>

  <div class="admin-profile-overlay" id="adminProfileOverlay" style="display:none" onclick="if(event.target===this)toggleAdminProfile()">
    <div class="admin-profile-card ap-card2">
      <button class="notif-x" onclick="toggleAdminProfile()" title="Close">×</button>
      <div class="ap-hero-av" id="apAvatar">A</div>
      <h3 id="apName" class="ap-name"></h3>
      <p id="apRole" class="ap-sub"></p>
      <div class="notif-divider"></div>
      <div class="ap-row"><span class="ap-label">Email</span><b id="apEmail"></b></div>
      <div class="ap-row"><span class="ap-label">Department</span><b id="apDept"></b></div>
      <div class="notif-divider"></div>
      <div style="display:flex;gap:10px;margin-top:16px">
        <a href="../User/logout.php" class="ap-btn danger" style="flex:1;text-align:center">Logout</a>
      </div>
    </div>
  </div>

  <script>
  window.ADMIN_PROFILE = {
    name: <?php echo json_encode($_SESSION['full_name'] ?? 'Admin') ?>,
    email: <?php echo json_encode($_SESSION['email'] ?? '') ?>,
    department: <?php echo json_encode($_SESSION['department'] ?? '') ?>,
    role: <?php echo json_encode(ucfirst($_SESSION['role'] ?? 'admin')) ?>
  };
  </script>
  <script src="js/profile.js?v=20261013"></script>
</body>
</html>
