<?php require_once 'guard.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VenueVibe - Booking Requests</title>
  <link rel="stylesheet" href="css/style.css?v=20261013">
  <link rel="stylesheet" href="css/requests.css?v=20261013">
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
      <a class="sidebar-link active" href="requests.php">
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
      <h1><button class="menu-btn" onclick="toggleMenu()">☰</button> Booking Requests</h1>
      <div class="topbar-right">
        <span class="profile-wrap">
          <span class="profile-vector" onclick="toggleAdminProfile()" title="Profile details"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4f39f6" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.2-3.8 4.2-5.5 8-5.5s6.8 1.7 8 5.5"/></svg></span>
          <span class="green-dot"></span>
        </span>
      </div>
      <div class="clear"></div>
    </div>

    <div class="content">
      <div class="req-box">
        <div>
          <div style="float:left">
            <h2 class="req-title">Pending Booking Requests</h2>
            <p class="req-sub">Review and manage upcoming venue booking requisitions across campus.</p>
          </div>
          <div style="float:right">
            <button class="tab-btn" id="tabPending" onclick="setTab('Pending')">Pending</button>
            <button class="tab-btn" id="tabApproved" onclick="setTab('Approved')">Approved</button>
            <button class="tab-btn" id="tabRejected" onclick="setTab('Rejected')">Rejected</button>
            <button class="tab-btn" id="tabAll" onclick="setTab('All')">All</button>
          </div>
          <div class="clear"></div>
        </div>

        <div class="req-head">
          <div class="req-col c1">REQUESTOR</div>
          <div class="req-col c2">VENUE</div>
          <div class="req-col c3">EVENT DATE</div>
          <div class="req-col c5">TIME SLOT</div>
          <div class="req-col c6">RESERVED ON</div>
          <div class="req-col c4">ACTIONS</div>
          <div class="clear"></div>
        </div>

        <div id="reqRows"></div>

        <div class="req-foot">
          <span id="reqCount"></span>
          <span style="float:right"><span class="sync-dot"></span>Real-time booking queue synchronized</span>
          <div class="clear"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="toast" id="toast">
    <span id="toastMsg" style="font-size:12px;font-weight:bold"></span>
    <button onclick="hideReqToast()" style="background:none;border:none;color:white;margin-left:10px;cursor:pointer">✖</button>
  </div>

  <script src="js/data.js?v=20261013"></script>
  <script src="js/sidebar.js?v=20261013"></script>
  <script src="js/requests.js?v=20261013"></script>

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
