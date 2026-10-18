<?php require_once 'guard.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VenueVibe - Messages</title>
  <link rel="stylesheet" href="css/style.css?v=20261013">
  <style>
    .msg-layout { display: flex; gap: 16px; align-items: stretch; height: calc(100vh - 132px); min-height: 520px; }
    .msg-users { width: 300px; background:#fff; border:1px solid #e5e9f2; border-radius:16px; padding:14px; overflow-y:auto; box-shadow:0 1px 3px rgba(15,23,42,.05); }
    .msg-panel-title { font-size:12px; font-weight:800; letter-spacing:.06em; color:#94a3b8; text-transform:uppercase; margin:0 6px 10px; }
    .msg-panel-btn {
      width:100%; cursor:pointer; border:1px solid #e0e7ff; background:#eef0ff; color:#4f39f6;
      border-radius:12px; padding:9px 12px; font-size:13px; font-weight:700; margin-bottom:12px; transition:all .15s;
    }
    .msg-panel-btn:hover { background:#4f39f6; color:#fff; border-color:#4f39f6; }
    .msg-user { display:flex; gap:12px; padding:10px 10px; border-radius:12px; cursor:pointer; align-items:center; transition:background .15s; }
    .msg-user:hover { background:#f6f6ff; }
    .msg-user.active { background:#eef0ff; box-shadow:inset 0 0 0 1px #d9deff; }
    .msg-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#6366f1,#4f39f6); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; flex-shrink:0; box-shadow:0 2px 6px rgba(79,57,246,.35); }
    .msg-user-name { font-weight:700; font-size:14px; color:#1d2939; }
    .msg-user-sub { font-size:11px; color:#64748b; }
    .msg-last { font-size:11px; color:#94a3b8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:170px; }
    .msg-chat { flex:1; background:#fff; border:1px solid #e5e9f2; border-radius:16px; display:flex; flex-direction:column; box-shadow:0 1px 3px rgba(15,23,42,.05); overflow:hidden; }
    .msg-chat-head { padding:16px 20px; border-bottom:1px solid #eef1f7; font-weight:800; font-size:15px; color:#1d2939; }
    .msg-chat-body { flex:1; padding:18px 20px; overflow-y:auto; background:#fafbff; }
    .bubble { max-width:70%; padding:10px 14px; border-radius:16px; margin-bottom:10px; font-size:13.5px; box-shadow:0 1px 2px rgba(15,23,42,.06); }
    .bubble.me { background:linear-gradient(135deg,#6366f1,#4f39f6); color:#fff; margin-left:auto; border-bottom-right-radius:5px; }
    .bubble.them { background:#fff; color:#1d2939; margin-right:auto; border-bottom-left-radius:5px; border:1px solid #eef1f7; }
    .bubble-time { font-size:10.5px; opacity:.65; margin-top:5px; }
    .msg-chat-input { display:flex; gap:10px; padding:14px 16px; border-top:1px solid #eef1f7; background:#fff; }
    .msg-chat-input input { flex:1; padding:11px 16px; border:1px solid #e2e8f0; border-radius:999px; font-size:13.5px; outline:none; background:#f8fafc; }
    .msg-chat-input input:focus { border-color:#4f39f6; background:#fff; }
    .msg-chat-input button { background:linear-gradient(135deg,#6366f1,#4f39f6); color:#fff; border:none; border-radius:999px; padding:11px 22px; font-weight:700; cursor:pointer; box-shadow:0 4px 10px rgba(79,57,246,.35); }
    .msg-empty { display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:13px; height:100%; }
  </style>
</head>
<body>

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
      <a class="sidebar-link" href="announcements.php">
        <span class="nav-ic">
          <svg width="17" height="17" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 10V6a1 1 0 0 1 1-1h8l4-3v12l-4-3H4a1 1 0 0 1-1-1z"/><path d="M5 14v3"/></svg>
        </span>Announcements
      </a>
      <a class="sidebar-link active" href="messages.php">
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
      <h1><button class="menu-btn" onclick="toggleMenu()">☰</button> Messages</h1>
      <div class="topbar-right">
        <span class="profile-wrap">
          <span class="profile-vector" onclick="toggleAdminProfile()" title="Profile details"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4f39f6" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c1.2-3.8 4.2-5.5 8-5.5s6.8 1.7 8 5.5"/></svg></span>
          <span class="green-dot"></span>
        </span>
      </div>
    </div>

    <div class="content">
    <div class="msg-layout">
      <div class="msg-users" id="msgUsers">Loading users…</div>
      <div class="msg-chat">
        <div class="msg-chat-head" id="msgChatHead">Select a user</div>
        <div class="msg-chat-body" id="msgChatBody">
          <div class="msg-empty">Choose a user to start messaging</div>
        </div>
        <div class="msg-chat-input">
          <input type="text" id="msgInput" placeholder="Type a message…" onkeydown="if(event.key==='Enter'){sendAdminMsg()}">
          <button onclick="sendAdminMsg()">Send</button>
        </div>
      </div>
    </div>
    </div>
  </div>

  <script src="js/sidebar.js?v=20261013"></script>
  <script>
    var adminContacts = [];
    var activeUserId = null;
    var showAllUsers = false;

    function esc(s) {
      return (s || "").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    fetch("api.php?action=contacts&nc=" + Date.now(), { cache: "no-store" })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        adminContacts = (data && data.contacts) ? data.contacts : [];
        adminContacts.sort(function (a, b) {
          var ua = a.unread_count || 0;
          var ub = b.unread_count || 0;
          if (ua !== ub) { return ub - ua; }
          return (b.last_at || "").localeCompare(a.last_at || "");
        });
        renderContactList();
      });

    function renderContactList() {
      var box = document.getElementById("msgUsers");
      var st = box ? box.scrollTop : 0;
      // "Recent" = contacts that actually have a conversation
      var recent = [];
      for (var r = 0; r < adminContacts.length; r++) {
        if (adminContacts[r].last_text && adminContacts[r].last_text !== "No messages yet") {
          recent.push(adminContacts[r]);
        }
      }
      var list = adminContacts.slice();
      if (!showAllUsers) {
        list = recent.length > 0 ? recent.slice(0, 5) : adminContacts.slice(0, 5);
      }
      var html = "";
      html += "<p class='msg-panel-title'>" + (showAllUsers ? "All profiles to chat" : "Recent chats") + "</p>";
      html += "<button class='msg-panel-btn' onclick='toggleAllUsers()'>" + (showAllUsers ? "Show recent chats only" : "All profiles (" + adminContacts.length + ")") + "</button>";
      for (var i = 0; i < list.length; i++) {
        var c = list[i];
        html += "<div class='msg-user' id='mrow-" + c.id + "' onclick='openAdminMsg(" + c.id + ")'" + ((c.unread_count || 0) > 0 ? " style='border-left:3px solid #4f39f6'" : "") + ">";
        html += "<div class='msg-avatar'>" + esc(c.name.charAt(0).toUpperCase()) + "</div>";
        html += "<div class='msg-user-name' style='flex:1'>" + esc(c.name) + "</div>";
        if ((c.unread_count || 0) > 0) {
          html += "<span class='msg-unread' style='margin-left:auto;background:#4f39f6;color:#fff;border-radius:999px;padding:2px 8px;font-size:11px;font-weight:700'>" + c.unread_count + "</span>";
        }
        html += "</div>";
      }
      box.innerHTML = html || "<p style='font-size:12px;color:#94a3b8;padding:10px'>No users found.</p>";
      if (box) { box.scrollTop = st; }
    }

    function toggleAllUsers() {
      showAllUsers = !showAllUsers;
      renderContactList();
    }

    // Live-refresh contacts + active conversation so new student messages appear
    setInterval(function () {
      fetch("api.php?action=contacts&nc=" + Date.now(), { cache: "no-store" })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data && data.status === "success" && Array.isArray(data.contacts)) {
            adminContacts = data.contacts;
            adminContacts.sort(function (a, b) {
              var ua = a.unread_count || 0;
              var ub = b.unread_count || 0;
              if (ua !== ub) { return ub - ua; }
              return (b.last_at || "").localeCompare(a.last_at || "");
            });
            renderContactList();
          }
        });
      if (activeUserId !== null) { openAdminMsg(activeUserId, true); }
    }, 1500);

    function openAdminMsg(id, silent) {
      activeUserId = id;
      var name = "";
      for (var i = 0; i < adminContacts.length; i++) {
        if (adminContacts[i].id === id) { name = adminContacts[i].name; }
      }
      var rows = document.querySelectorAll(".msg-user");
      for (var j = 0; j < rows.length; j++) { rows[j].className = "msg-user"; }
      var active = document.getElementById("mrow-" + id);
      if (active) { active.className = "msg-user active"; }
      document.getElementById("msgChatHead").innerHTML = esc(name);
      fetch("api.php?action=chat_messages&user_id=" + id + "&nc=" + Date.now(), { cache: "no-store" })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          var body = document.getElementById("msgChatBody");
          var html = "";
          var msgs = (data && data.messages) ? data.messages : [];
          var prevCount = body ? parseInt(body.dataset.msgCount || "0", 10) : 0;
          if (prevCount === msgs.length && msgs.length > 0) { return; }
          for (var i = 0; i < msgs.length; i++) {
            html += "<div class='bubble " + (msgs[i].who === "me" ? "me" : "them") + "'>";
            html += esc(msgs[i].text);
            html += "<div class='bubble-time'>" + esc(msgs[i].time || "") + "</div>";
            html += "</div>";
          }
          body.innerHTML = html || "<div class='msg-empty'>No messages yet. Say hello!</div>";
          body.scrollTop = body.scrollHeight;
          body.dataset.msgCount = String(msgs.length);
        });
    }

    function sendAdminMsg() {
      var input = document.getElementById("msgInput");
      var text = input.value.trim();
      if (!text || !activeUserId) { return; }
      input.value = "";
      fetch("api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "chat_send", receiver_id: activeUserId, text: text })
      }).then(function () {
        openAdminMsg(activeUserId);
      });
    }
  </script>

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
