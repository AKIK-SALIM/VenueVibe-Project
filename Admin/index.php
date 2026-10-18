<?php require_once 'guard.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VenueVibe - Admin Dashboard</title>
  <link rel="stylesheet" href="css/style.css?v=20261013">
  <link rel="stylesheet" href="css/dashboard.css?v=20261013">
</head>
<body onload="initPage()">

  <!-- ============ SIDEBAR ============ -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <a href="index.php">VenueVibe</a>
    </div>
    <nav>
      <a class="sidebar-link active" href="index.php">
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

  <!-- ============ MAIN ============ -->
  <div class="main">
    <div class="topbar">
      <h1><button class="menu-btn" onclick="toggleMenu()">☰</button> Admin Dashboard</h1>
      <div class="topbar-right">
        <span class="profile-wrap">
          <span class="profile-vector" onclick="toggleAdminProfile()" title="Profile details"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4f39f6" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.2-3.8 4.2-5.5 8-5.5s6.8 1.7 8 5.5"/></svg></span>
          <span class="green-dot"></span>
        </span>
      </div>
      <div class="clear"></div>
    </div>

    <div class="content">
      <!-- stat cards -->
      <div class="stat-row">
        <div class="stat-card">
          <div class="stat-top">
            <div class="stat-icon blue">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4f39f6" stroke-width="1.8"><rect x="7" y="2.5" width="10" height="19" rx="2.5"/><circle cx="12" cy="18.5" r="1" fill="#4f39f6"/></svg>
            </div>
            <div class="stat-text">
              <div class="stat-label">Total Bookings</div>
              <div class="stat-number" id="totalBookings">1,248</div>
            </div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <div class="stat-icon green">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#12a56f" stroke-width="1.8"><rect x="5" y="3.5" width="14" height="17" rx="2"/><path d="M9 3.5V6h6V3.5M9 10h6M9 13.5h6M9 17h3.5"/></svg>
            </div>
            <div class="stat-text">
              <div class="stat-label">Pending Requests</div>
              <div class="stat-number" id="pendingCount">24</div>
            </div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-top">
            <div class="stat-icon gray">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#475467" stroke-width="1.8"><path d="M5 21V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16"/><path d="M3 21h18"/><path d="M15 8h4a1 1 0 0 1 1 1v12"/></svg>
            </div>
            <div class="stat-text">
              <div class="stat-label">Active Venues</div>
            </div>
            <span class="active-num" id="activeVenues">0</span>
          </div>
          <div class="bar-bg"><div class="bar-fill" id="activeVenuesBar" style="width:0%"></div></div>
        </div>
      </div>

      <!-- row 1 : pending (wide left) + announcements (side right) -->
      <div class="row-gap">
        <div class="col-wide">
          <div class="card">
            <span class="section-title">Pending Booking Requests</span>
            <span class="count-pill" id="remainingPill">3 remaining</span>
            <div id="pendingTable"></div>
          </div>
        </div>
        <div class="col-side">
          <div class="card">
            <div class="card-head">
              <span class="section-title">Announcements</span>
              <a class="link-small" href="announcements.php">see all</a>
              <div class="clear"></div>
            </div>
            <div id="noticeList"></div>
            <button class="btn full-btn" onclick="openAnnounceModal()">Send Notification <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="vertical-align:-2px"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg></button>
          </div>
        </div>
        <div class="clear"></div>
      </div>

      <!-- row 2 : chat (side left) + calendar (wide right) : same as reference -->
      <div class="row-gap">
        <div class="col-side-left">
          <div class="card">
            <div class="card-head">
              <span class="section-title">Direct Chat</span>
              
              <div class="clear"></div>
            </div>
            <div id="chatList"></div>
            <button class="btn full-btn" onclick="window.location.href='messages.php'">Start new chat... <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="vertical-align:-2px"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg></button>
          </div>
        </div>
        <div class="col-wide-right">
          <div class="card">
            <div>
              <span class="section-title" id="calTitle">Venue Calendar &mdash; July 2026</span>
              <span class="cal-nav">
                <button class="small-btn" onclick="prevMonth()">&#8249; Prev</button>
                <button class="small-btn" onclick="nextMonth()">Next &#8250;</button>
              </span>
              <div class="clear"></div>
            </div>
            <div class="cal-head-row">
              <div class="cal-head">SUN</div>
              <div class="cal-head">MON</div>
              <div class="cal-head">TUE</div>
              <div class="cal-head">WED</div>
              <div class="cal-head">THU</div>
              <div class="cal-head">FRI</div>
              <div class="cal-head">SAT</div>
              <div class="clear"></div>
            </div>
            <div id="calDays"></div>
            <div class="legend"><span style="color:#4f39f6">&#9679;</span> Today &nbsp;&nbsp; <span style="color:#35c26e">&#9679;</span> Has bookings</div>
          </div>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </div>

  <!-- toast -->
  <div class="toast" id="toast">
    <b id="toastTitle" style="font-size:11px;color:#cbd5e1"></b>
    <div id="toastMsg" style="font-size:14px"></div>
  </div>

  <!-- send notification modal -->
  <div class="modal-bg" id="announceModal" style="display:none">
    <div class="modal-box">
      <div class="modal-top">
        <button class="close-btn" onclick="closeAnnounceModal()">&#10005;</button>
        <h2><span class="icon-tile"><svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="#4f39f6" stroke-width="1.8"><path d="M3 10V6a1 1 0 0 1 1-1h8l4-3v12l-4-3H4a1 1 0 0 1-1-1z"/><path d="M5 14v3"/></svg></span> Create New Announcement</h2>
      </div>
      <label class="label">Title <span class="red">*</span></label>
      <input class="input" id="noticeTitle" placeholder="e.g. Campus Maintenance Notice">
      <div class="m-row">
        <div class="m-col">
          <label class="label">Category</label>
          <select class="select" id="noticeCategory">
            <option>Maintenance</option>
            <option>Booking Policy</option>
            <option>Weather Alert</option>
            <option>Event Notice</option>
          </select>
        </div>
        <div class="m-col">
          <label class="label">Priority</label>
          <select class="select" id="noticePriority">
            <option>Normal</option>
            <option>Urgent</option>
            <option>High Priority</option>
          </select>
        </div>
      </div>
      <div class="m-row">
        <div class="m-col">
          <label class="label">Schedule Time (Optional)</label>
          <input class="input" id="noticeTime" placeholder="e.g. Oct 28 • 09:00 AM">
        </div>
      </div>
      <label class="label">Content <span class="red">*</span></label>
      <textarea class="textarea no-resize" id="noticeContent" rows="3" placeholder="Provide full description..."></textarea>
      <br><br>
      <div style="text-align:right">
        <button class="small-btn" onclick="closeAnnounceModal()">Cancel</button>
        <button class="publish-btn" onclick="publishNotice()">&#10003; Publish Announcement</button>
      </div>
    </div>
  </div>

  <!-- chat modal -->
  <div class="modal-bg" id="chatModal" style="display:none">
    <div class="modal-box">
      <div class="modal-top">
        <button class="close-btn" onclick="closeChat()">&#10005;</button>
        <img id="chatAvatar" src="" style="width:36px;height:36px;border-radius:50%;float:left;margin-right:10px" alt="">
        <b id="chatName"></b><br>
        <span style="font-size:11px;color:green">&#9679; Active Now</span>
      </div>
      <div id="chatMessages" style="min-height:200px;max-height:300px;overflow-y:auto"></div>
      <div style="border-top:1px solid #f1f5f9;padding-top:12px;margin-top:12px">
        <input class="input" id="chatInput" placeholder="Type message..." style="width:80%;display:inline-block">
        <button class="btn" onclick="sendChat()" style="width:15%">&#10148;</button>
      </div>
    </div>
  </div>

  <!-- date modal -->
  <div class="modal-bg" id="dateModal" style="display:none">
    <div class="modal-box" style="max-width:350px">
      <div class="modal-top">
        <button class="close-btn" onclick="closeDateModal()">&#10005;</button>
        <h2 id="dateTitle"></h2>
      </div>
      <div id="dateBody"></div>
      <br>
      <button class="btn full-btn" onclick="closeDateModal()">Close</button>
    </div>
  </div>

  <script src="js/data.js?v=20261013"></script>
  <script src="js/sidebar.js?v=20261013"></script>
  <script src="js/dashboard.js?v=20261013"></script>

  <div class="admin-profile-overlay" id="adminNoticesOverlay" style="display:none" onclick="if(event.target===this)toggleAdminNotices()">
    <div class="notif-card2">
      <button class="notif-x" onclick="toggleAdminNotices()" title="Close">×</button>
      <div class="notif-hero">
        <div class="notif-hero-av">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
      </div>
      <h3 class="notif-title">Notifications</h3>
      <p class="notif-sub">New booking requests</p>
      <div class="notif-divider"></div>
      <div id="adminNoticesList" class="notif-list2">Loading…</div>
      <div class="notif-divider"></div>
      <button class="ap-btn" style="width:100%;margin-top:16px" onclick="toggleAdminNotices()">Close</button>
    </div>
  </div>
  <script>
    function toggleAdminNotices() {
      var el = document.getElementById("adminNoticesOverlay");
      if (!el) { return; }
      if (el.style.display === "flex") { el.style.display = "none"; return; }
      el.style.display = "flex";
      el.style.position = "fixed";
      el.style.inset = "0";
      el.style.background = "rgba(15,23,42,.45)";
      el.style.alignItems = "center";
      el.style.justifyContent = "center";
      el.style.zIndex = "999";
      fetch("api.php?action=admin_notifications")
        .then(function (r) { return r.json(); })
        .then(function (data) {
          var box = document.getElementById("adminNoticesList");
          var rows = (data && data.requests) ? data.requests : [];
          if (rows.length === 0) {
            box.innerHTML = "<p style='color:#94a3b8;font-size:13px'>No booking requests yet.</p>";
            return;
          }
          var html = "";
          for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            var pill = "<span class='notif-pill pending'>" + r.status + "</span>";
            if (r.status === "Approved") { pill = "<span class='notif-pill ok'>" + r.status + "</span>"; }
            if (r.status === "Rejected") { pill = "<span class='notif-pill bad'>" + r.status + "</span>"; }
            html += "<div class='notif-item'>";
            html += "<div style='display:flex;align-items:center;justify-content:space-between;gap:10px'>";
            html += "<b style='font-size:13.5px;color:#1d2939'>" + r.venue_name + "</b>" + pill;
            html += "</div>";
            html += "<div style='font-size:12.5px;color:#64748b;margin-top:3px'>" + (r.requester || "Unknown") + " · " + r.booking_date + "</div>";
            html += "</div>";
            if (i < rows.length - 1) { html += "<div class='notif-divider'></div>"; }
          }
          box.innerHTML = html;
        });
    }
  </script>

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
