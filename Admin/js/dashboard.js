/* js / dashboard.js : logic for index.html (Admin Dashboard) */

function openOverlay(el) {
  el.style.display = 'flex';
  el.style.position = 'fixed';
  el.style.inset = '0';
  el.style.background = 'rgba(15,23,42,.45)';
  el.style.alignItems = 'center';
  el.style.justifyContent = 'center';
  el.style.zIndex = '999';
}

var totalBookings = (typeof window.TOTAL_BOOKINGS === "number") ? window.TOTAL_BOOKINGS : 0;
var pendingCount = (typeof window.PENDING_COUNT === "number") ? window.PENDING_COUNT : 0;
var totalVenues = (typeof window.TOTAL_VENUES === "number") ? window.TOTAL_VENUES : 0;
var activeVenues = (typeof window.ACTIVE_VENUES === "number") ? window.ACTIVE_VENUES : 0;
var dashRequests = [];
var dashNotices = [];
var currentMonth = (typeof window.CURRENT_MONTH === "number") ? window.CURRENT_MONTH : new Date().getMonth();
var activeChatId = null;
var chatMessages = [];
var selectedDay = null;

// booked days and today come from the database via data.js
var BOOKED_DAYS = window.BOOKED_DAYS || [];
var TODAY_DAY = window.TODAY_DAY || new Date().getDate();

function startDashboard() {
  // refresh globals that data.js loaded from the API
  totalBookings = (typeof window.TOTAL_BOOKINGS === "number") ? window.TOTAL_BOOKINGS : totalBookings;
  pendingCount = (typeof window.PENDING_COUNT === "number") ? window.PENDING_COUNT : pendingCount;
  totalVenues = (typeof window.TOTAL_VENUES === "number") ? window.TOTAL_VENUES : totalVenues;
  activeVenues = (typeof window.ACTIVE_VENUES === "number") ? window.ACTIVE_VENUES : activeVenues;
  BOOKED_DAYS = window.BOOKED_DAYS || BOOKED_DAYS;
  TODAY_DAY = window.TODAY_DAY || TODAY_DAY;
  if (typeof window.CURRENT_MONTH === "number") { currentMonth = window.CURRENT_MONTH; }

  // copy first 3 pending requests
  dashRequests = [];
  var count = 0;
  for (var i = 0; i < BOOKING_REQUESTS.length; i++) {
    if (BOOKING_REQUESTS[i].status === "pending" && count < 3) {
      dashRequests.push({ id: BOOKING_REQUESTS[i].id, name: BOOKING_REQUESTS[i].name, role: BOOKING_REQUESTS[i].role, initials: BOOKING_REQUESTS[i].initials, venue: BOOKING_REQUESTS[i].venue, date: BOOKING_REQUESTS[i].date, time: BOOKING_REQUESTS[i].time || "", requested: BOOKING_REQUESTS[i].requested || "", attachment: BOOKING_REQUESTS[i].attachment || "", attendeeEmail: BOOKING_REQUESTS[i].attendeeEmail || "", attendeeNotified: BOOKING_REQUESTS[i].attendeeNotified ? 1 : 0, status: "pending" });
      count++;
    }
  }
  // copy notices
  dashNotices = [];
  for (var j = 0; j < NOTICES.length; j++) {
    dashNotices.push(NOTICES[j]);
  }
  showStats();
  showDashRequests();
  showDashNotices();
  showChatList();
  showCalendar();
}

function formatNumber(n) {
  var s = "" + n;
  var out = "";
  var count = 0;
  for (var i = s.length - 1; i >= 0; i--) {
    out = s.charAt(i) + out;
    count++;
    if (count === 3 && i !== 0) {
      out = "," + out;
      count = 0;
    }
  }
  return out;
}

function showStats() {
  document.getElementById("totalBookings").innerHTML = formatNumber(totalBookings);
  document.getElementById("pendingCount").innerHTML = pendingCount;
  var avEl = document.getElementById("activeVenues");
  if (avEl) { avEl.innerHTML = formatNumber(activeVenues); }
  var barEl = document.getElementById("activeVenuesBar");
  if (barEl) {
    var pct = totalVenues > 0 ? Math.round((activeVenues / totalVenues) * 100) : 0;
    if (pct < 0) { pct = 0; }
    if (pct > 100) { pct = 100; }
    barEl.style.width = pct + "%";
    barEl.title = activeVenues + " of " + totalVenues + " venues active";
  }
}

function showDashRequests() {
  var box = document.getElementById("pendingTable");
  var html = "";
  html += "<table class='simple-table'>";
  html += "<tr><th>REQUESTOR</th><th>VENUE</th><th>EVENT DATE</th><th>TIME SLOT</th><th>RESERVED ON</th><th class='center'>ACTIONS</th></tr>";
  for (var i = 0; i < dashRequests.length; i++) {
    var r = dashRequests[i];
    html += "<tr>";
    html += "<td><div class='avatar-circle'>" + r.initials + "</div><span class='req-name'>" + r.name + "</span><br><span class='req-role'>" + r.role + "</span></td>";
    html += "<td class='venue-cell'>" + r.venue + (r.attachment ? "<br><a class='req-attach' href='../User/uploads/" + encodeURIComponent(r.attachment) + "' target='_blank'>&#128206; Attachment</a>" : "") + (r.attendeeEmail ? "<br><span class='req-attendee'>&#9993; " + r.attendeeEmail + (r.attendeeNotified ? " &middot; notified" : "") + "</span>" : "") + "</td>";
    html += "<td class='date-cell'>" + r.date + "</td>";
    html += "<td class='date-cell'>" + (r.time && r.time !== "" ? r.time : "—") + "</td>";
    html += "<td class='date-cell'>" + (r.requested && r.requested !== "" ? r.requested : "—") + "</td>";
    html += "<td style='text-align:center'>";
    var pillClass = "status-pend";
    var pillText = r.status;
    if (r.status === "approved") { pillClass = "status-ok"; pillText = "Approved"; }
    else if (r.status === "rejected") { pillClass = "status-bad"; pillText = "Rejected"; }
    html += "<span class='" + pillClass + "'>" + pillText + "</span>";
    html += " <select class='admin-edit-status' onchange='editDashStatus(" + r.id + ", this.value)'>";
    html += "<option value='Pending'" + (r.status === "pending" ? " selected" : "") + ">Pending</option>";
    html += "<option value='Approved'" + (r.status === "approved" ? " selected" : "") + ">Approved</option>";
    html += "<option value='Rejected'" + (r.status === "rejected" ? " selected" : "") + ">Rejected</option>";
    html += "</select>";
    html += "</td></tr>";
  }
  html += "</table>";
  box.innerHTML = html;

  // remaining count
  var left = 0;
  for (var k = 0; k < dashRequests.length; k++) {
    if (dashRequests[k].status === "pending") { left++; }
  }
  document.getElementById("remainingPill").innerHTML = left + " remaining";
}

function approveDash(id) {
  for (var i = 0; i < dashRequests.length; i++) {
    if (dashRequests[i].id === id) {
      dashRequests[i].status = "approved";
      totalBookings = totalBookings + 1;
      if (pendingCount > 0) { pendingCount = pendingCount - 1; }
      showToast("Booking Approved", "Request for " + dashRequests[i].name + " has been approved.");
      API.post("request_status", { id: id, status: "Approved" });
    }
  }
  for (var j = 0; j < BOOKING_REQUESTS.length; j++) {
    if (BOOKING_REQUESTS[j].id === id) { BOOKING_REQUESTS[j].status = "approved"; }
  }
  showStats();
  showDashRequests();
}

function editDashStatus(id, newStatus) {
  API.post("request_status", { id: id, status: newStatus.charAt(0).toUpperCase() + newStatus.slice(1) })
    .then(function () {
      loadAdminData().then(function () {
        startDashboard();
      });
      showToast("Status Updated", "Request status changed to " + newStatus);
    });
}

function rejectDash(id) {
  for (var i = 0; i < dashRequests.length; i++) {
    if (dashRequests[i].id === id) {
      dashRequests[i].status = "rejected";
      if (pendingCount > 0) { pendingCount = pendingCount - 1; }
      showToast("Booking Rejected", "Request for " + dashRequests[i].name + " has been rejected.");
      API.post("request_status", { id: id, status: "Rejected" });
    }
  }
  for (var j = 0; j < BOOKING_REQUESTS.length; j++) {
    if (BOOKING_REQUESTS[j].id === id) { BOOKING_REQUESTS[j].status = "rejected"; }
  }
  showStats();
  showDashRequests();
}

function showDashNotices() {
  var box = document.getElementById("noticeList");
  var html = "";
  for (var i = 0; i < dashNotices.length; i++) {
    var n = dashNotices[i];
    var cls = "notice-item";
    var icon = "<svg width='15' height='15' viewBox='0 0 20 20' fill='none' stroke='#667085' stroke-width='1.8'><circle cx='10' cy='10' r='7'/><path d='M10 9v4'/><circle cx='10' cy='6.5' r='1' fill='#667085'/></svg>";
    html += "<div class='" + cls + "'>";
    html += "<div class='notice-icon'>" + icon + "</div>";
    html += "<div class='notice-text'><div class='notice-title'>" + n.title + "</div>";
    html += "<div class='notice-body'>" + n.body + "</div>";
    html += "<div class='notice-time'>" + n.time + "</div></div>";
    html += "<div class='clear'></div></div>";
  }
  box.innerHTML = html;
}

function showChatList() {
  var box = document.getElementById("chatList");
  var html = "";
  for (var i = 0; i < CHAT_USERS.length; i++) {
    var u = CHAT_USERS[i];
    // only show recent chats (users with an actual conversation); no online status
    if (!u.message || u.message === "No messages yet") { continue; }
    html += "<div class='chat-row' onclick='openChat(" + u.id + ")'>";
    html += "<div class='chat-avatar-wrap'><img class='chat-img' src='" + u.avatar + "' alt='" + u.name + "'></div>";
    html += "<div class='chat-text'>";
    html += "<span class='chat-time'>" + u.time + "</span>";
    html += "<div class='chat-name'>" + u.name + "</div>";
    html += "<div class='chat-msg'>" + u.message + "</div>";
    html += "</div>";
    html += "<div class='clear'></div></div>";
  }
  box.innerHTML = html;
}

function showCalendar() {
  document.getElementById("calTitle").innerHTML = "Venue Calendar — " + MONTH_NAMES[currentMonth] + " 2026";
  var box = document.getElementById("calDays");
  var html = "";
  // simple: show 31 days + 4 next-month grey days
  for (var d = 1; d <= 31; d++) {
    var cls = "cal-day free";
    if (d === TODAY_DAY) { cls = "cal-day today"; }
    else if (BOOKED_DAYS.indexOf(d) !== -1) { cls = "cal-day booked"; }
    html += "<div class='" + cls + "' onclick='clickDay(" + d + ")'>" + d + "</div>";
  }
  for (var g = 1; g <= 4; g++) {
    html += "<div class='cal-day dim'>" + g + "</div>";
  }
  html += "<div class='clear'></div>";
  box.innerHTML = html;
}

function prevMonth() {
  if (currentMonth > 0) { currentMonth = currentMonth - 1; }
  else { currentMonth = 11; }
  showCalendar();
}

function nextMonth() {
  if (currentMonth < 11) { currentMonth = currentMonth + 1; }
  else { currentMonth = 0; }
  showCalendar();
}

function clickDay(day) {
  var isToday = (day === TODAY_DAY);
  var isBooked = (BOOKED_DAYS.indexOf(day) !== -1);
  var title = document.getElementById("dateTitle");
  var body = document.getElementById("dateBody");
  title.innerHTML = MONTH_NAMES[currentMonth] + " " + day + ", 2026";

  var dateStr = "2026-" + (currentMonth + 1 < 10 ? "0" : "") + (currentMonth + 1) + "-" + (day < 10 ? "0" : "") + day;
  body.innerHTML = "<div style='color:#64748b;font-size:12px'>Loading events…</div>";
  API.get("day_events", { date: dateStr }).then(function (res) {
    var approved = [];
    if (res && res.status === "success" && res.events.length > 0) {
      for (var k = 0; k < res.events.length; k++) {
        if ((res.events[k].status || "").toLowerCase() === "approved") { approved.push(res.events[k]); }
      }
    }
    if (approved.length > 0) {
      var html = "";
      for (var i = 0; i < approved.length; i++) {
        var e = approved[i];
        html += "<div style='background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;padding:12px;margin-bottom:8px'>";
        html += "<b style='font-size:10px;color:#047857'>SCHEDULED BOOKING &bull; " + (e.status || "").toUpperCase() + "</b>";
        html += "<div style='font-size:12px;font-weight:bold'>" + e.venue_name + " — " + e.purpose + "</div>";
        html += "<div style='font-size:11px;color:#475467'>" + e.booking_time + (e.requester ? " &bull; " + e.requester : "") + "</div>";
        html += "</div>";
      }
      body.innerHTML = html;
    } else if (isToday) {
      body.innerHTML = "<div style='background:#eef2ff;border:1px solid #c7d2fe;border-radius:12px;padding:14px'><b style='font-size:10px;color:#4f46e5'>CURRENT DATE</b><div style='font-size:12px;font-weight:bold'>Active Admin Working Day</div></div>";
    } else {
      body.innerHTML = "<div style='background:#f8fafc;border-radius:12px;padding:16px;text-align:center;font-size:12px;color:#64748b'>No venue bookings scheduled for this date.</div>";
    }
  });
  openOverlay(document.getElementById("dateModal"));
}

function closeDateModal() {
  document.getElementById("dateModal").style.display = "none";
}

// ---- announcement modal (send notification) ----
function openAnnounceModal() {
  openOverlay(document.getElementById("announceModal"));
}

function closeAnnounceModal() {
  document.getElementById("announceModal").style.display = "none";
}

function publishNotice() {
  var title = document.getElementById("noticeTitle").value;
  var content = document.getElementById("noticeContent").value;
  if (title === "" || content === "") {
    alert("Please fill title and content.");
    return;
  }
  var pri = document.getElementById("noticePriority").value;
  var timeText = "Just now";
  if (pri !== "Normal") { timeText = "Priority: " + pri; }
  var sched = document.getElementById("noticeTime").value;
  if (sched !== "") { timeText = "Scheduled: " + sched; }
  var cat = document.getElementById("noticeCategory").value;
  var aud = "All Faculty & Students IC";
  dashNotices.unshift({ id: Date.now(), title: title, body: content, time: timeText, category: cat, audience: aud });
  API.post("announcement_create", { title: title, content: content, category: cat, priority: pri, audience: aud, time: sched });
  document.getElementById("noticeTitle").value = "";
  document.getElementById("noticeContent").value = "";
  document.getElementById("noticeTime").value = "";
  closeAnnounceModal();
  showDashNotices();
  showToast("Notification Posted", "Announcement sent to campus dashboard.");
}

// ---- chat modal ----
function openChat(id) {
  activeChatId = id;
  chatMessages = [];
  var user = null;
  for (var i = 0; i < CHAT_USERS.length; i++) {
    if (CHAT_USERS[i].id === id) { user = CHAT_USERS[i]; }
  }
  if (user === null) { return; }
  document.getElementById("chatName").innerHTML = user.name;
  document.getElementById("chatAvatar").src = user.avatar;
  chatMessages = [];
  showChatMessages();
  API.get("chat_messages", { user_id: id }).then(function (res) {
    if (res && res.status === "success" && res.messages.length > 0) {
      chatMessages = res.messages;
      showChatMessages();
    } else {
      chatMessages.push({ who: "them", text: user.message, time: user.time });
      showChatMessages();
    }
  });
  openOverlay(document.getElementById("chatModal"));
}

function closeChat() {
  document.getElementById("chatModal").style.display = "none";
  activeChatId = null;
}

function showChatMessages() {
  var box = document.getElementById("chatMessages");
  var html = "";
  for (var i = 0; i < chatMessages.length; i++) {
    html += "<div class='bubble " + chatMessages[i].who + "'>" + chatMessages[i].text + "</div>";
  }
  box.innerHTML = html;
}

function sendChat() {
  var input = document.getElementById("chatInput");
  var text = input.value;
  if (text === "") { return; }
  chatMessages.push({ who: "me", text: text });
  API.post("chat_send", { receiver_id: activeChatId, text: text });
  input.value = "";
  showChatMessages();
}

// ---- toast ----
var toastTimer = null;
function showToast(title, msg) {
  var t = document.getElementById("toast");
  document.getElementById("toastTitle").innerHTML = title;
  document.getElementById("toastMsg").innerHTML = msg;
  t.style.display = "block";
  if (toastTimer !== null) { clearTimeout(toastTimer); }
  toastTimer = setTimeout(function() {
    t.style.display = "none";
  }, 3000);
}
