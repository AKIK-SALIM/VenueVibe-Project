<?php require_once 'guard.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VenueVibe - Announcements Management</title>
  <link rel="stylesheet" href="css/style.css?v=20261013">
  <link rel="stylesheet" href="css/announcements.css?v=20261013">
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
      <a class="sidebar-link" href="venues.php">
        <span class="nav-ic">
          <svg width="17" height="17" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 18V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13"/><path d="M2 18h16"/><circle cx="12.5" cy="10" r="1" fill="currentColor"/></svg>
        </span>Venues
      </a>
      <a class="sidebar-link" href="requests.php">
        <span class="nav-ic">
          <svg width="17" height="17" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="3" width="12" height="14" rx="2"/><path d="M7 7h6M7 10.5h6M7 14h4"/></svg>
        </span>Requests
      </a>
      <a class="sidebar-link active" href="announcements.php">
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
      <h1><button class="menu-btn" onclick="toggleMenu()">☰</button> Announcements Management</h1>
      <div class="topbar-right">
        <span class="profile-wrap">
          <span class="profile-vector" onclick="toggleAdminProfile()" title="Profile details"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4f39f6" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.2-3.8 4.2-5.5 8-5.5s6.8 1.7 8 5.5"/></svg></span>
          <span class="green-dot"></span>
        </span>
      </div>
      <div class="clear"></div>
    </div>

    <div class="content">
      <div class="ann-top-row">
        <p class="ann-top-text">Create, schedule, and track campus-wide announcements.</p>
        <button class="btn" onclick="openAnnModal()"><svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="white" stroke-width="1.8"><path d="M3 10V6a1 1 0 0 1 1-1h8l4-3v12l-4-3H4a1 1 0 0 1-1-1z"/><path d="M5 14v3"/></svg> + Create New Announcement</button>
      </div>

      <!-- search + filter -->
      <div class="ann-filter-bar">
        <span class="ann-search-wrap">
          <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="#98a2b3" stroke-width="2"><circle cx="9" cy="9" r="6"/><path d="M13.5 13.5L17 17"/></svg>
          <input class="ann-search-input" id="annSearch" placeholder="Search announcements ......" onkeyup="onAnnSearch(this.value)">
        </span>
        <span class="ann-select-wrap">
          <select class="ann-select" id="annCategory" onchange="onAnnCategory(this.value)"></select>
          <span class="ann-chevron">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="#475569" stroke-width="2.2"><path d="M4.5 7.5L10 13l5.5-5.5"/></svg>
          </span>
        </span>
        <span class="ann-select-wrap">
          <select class="ann-select" id="annDateRange">
            <option>Past 30 Days</option>
            <option>Past 7 Days</option>
            <option>All Time</option>
          </select>
          <span class="ann-chevron">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="#475569" stroke-width="2.2"><path d="M4.5 7.5L10 13l5.5-5.5"/></svg>
          </span>
        </span>
      </div>
      <div class="ann-tabs-row">
        <div id="annTabs"></div>
        <span class="ann-count" id="annCount"></span>
      </div>

      <!-- list -->
      <div id="annList"></div>
    </div>
  </div>

  <!-- create modal -->
  <div class="modal-bg" id="annModal" style="display:none">
    <div class="modal-box">
      <div class="modal-top">
        <button class="close-btn" onclick="closeAnnModal()">&#10005;</button>
        <h2><span class="icon-tile"><svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="#4f39f6" stroke-width="1.8"><path d="M3 10V6a1 1 0 0 1 1-1h8l4-3v12l-4-3H4a1 1 0 0 1-1-1z"/><path d="M5 14v3"/></svg></span> Create New Announcement</h2>
      </div>
      <label class="label">Title <span class="red">*</span></label>
      <input class="input" id="aTitle" placeholder="e.g. Campus Maintenance Notice">
      <div class="m-row">
        <div class="m-col">
          <label class="label">Category</label>
          <select class="select" id="aCategory">
            <option>Maintenance</option>
            <option>Booking Policy</option>
            <option>Weather Alert</option>
            <option>Event Notice</option>
          </select>
        </div>
        <div class="m-col">
          <label class="label">Priority</label>
          <select class="select" id="aPriority">
            <option>Normal</option>
            <option>Urgent</option>
            <option>High Priority</option>
          </select>
        </div>
      </div>
      <div class="m-row">
        <div class="m-col">
          <label class="label">Schedule Time (Optional)</label>
          <input class="input" id="aTime" placeholder="e.g. Oct 28 • 09:00 AM">
        </div>
      </div>
      <label class="label">Content / Description <span class="red">*</span></label>
      <textarea class="textarea ann-textarea" id="aContent" rows="3" placeholder="Provide full description of the announcement..."></textarea>
      <br><br>
      <div style="text-align:right">
        <button class="small-btn" onclick="closeAnnModal()">Cancel</button>
        <button class="publish-btn" onclick="publishAnn()">&#10003; Publish Announcement</button>
      </div>
    </div>
  </div>

  <!-- announcement detail widget -->
  <div class="modal-bg" id="annDetailModal" style="display:none">
    <div class="modal-box ann-detail-box">
      <button class="detail-close" onclick="closeAnnDetail()">&#10005;</button>
      <div id="annDetailBody"></div>
    </div>
  </div>

  <script src="js/data.js?v=20261013"></script>
  <script src="js/sidebar.js?v=20261013"></script>
  <script src="js/announcements.js?v=20261013"></script>

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
