/* js / requests.js : logic for requests.html */

function openOverlay(el) {
  el.style.display = 'flex';
  el.style.position = 'fixed';
  el.style.inset = '0';
  el.style.background = 'rgba(15,23,42,.45)';
  el.style.alignItems = 'center';
  el.style.justifyContent = 'center';
  el.style.zIndex = '999';
}

var reqList = [];
var activeTab = "All";

function escHtml(s) {
  return String(s == null ? "" : s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}

function attachLink(r) {
  if (!r.attachment) { return ""; }
  var safe = escHtml(r.attachment);
  return "<br><a class='req-attach' href='../User/uploads/" + encodeURIComponent(r.attachment) + "' target='_blank' title='" + safe + "'>&#128206; " + safe + "</a>";
}

function attendeeLine(r) {
  if (!r.attendeeEmail) { return ""; }
  var safe = escHtml(r.attendeeEmail);
  var note = r.attendeeNotified ? " &middot; notified" : "";
  return "<br><span class='req-attendee' title='" + safe + "'>&#9993; " + safe + note + "</span>";
}

function startRequests() {
  reqList = [];
  for (var i = 0; i < BOOKING_REQUESTS.length; i++) {
    var r = BOOKING_REQUESTS[i];
    reqList.push({ id: r.id, name: r.name, role: r.role, initials: r.initials, venue: r.venue, date: r.date, time: r.time || "", requested: r.requested || "", attachment: r.attachment || "", attendeeEmail: r.attendeeEmail || "", attendeeNotified: r.attendeeNotified ? 1 : 0, status: r.status });
  }
  showRequestTabs();
  showRequestRows();
}

function countBy(status) {
  var c = 0;
  for (var i = 0; i < reqList.length; i++) {
    if (reqList[i].status === status) { c++; }
  }
  return c;
}

function showRequestTabs() {
  var pending = countBy("pending");
  var approved = countBy("approved");
  var rejected = countBy("rejected");
  document.getElementById("tabPending").innerHTML = "Pending (" + pending + ")";
  document.getElementById("tabApproved").innerHTML = "Approved (" + approved + ")";
  document.getElementById("tabRejected").innerHTML = "Rejected (" + rejected + ")";
  document.getElementById("tabAll").innerHTML = "All (" + reqList.length + ")";
  var tabs = ["Pending", "Approved", "Rejected", "All"];
  for (var i = 0; i < tabs.length; i++) {
    var btn = document.getElementById("tab" + tabs[i]);
    if (tabs[i] === activeTab) { btn.className = "tab-btn active"; }
    else { btn.className = "tab-btn"; }
  }
}

function setTab(t) {
  activeTab = t;
  showRequestTabs();
  showRequestRows();
}

function filteredReqs() {
  var out = [];
  for (var i = 0; i < reqList.length; i++) {
    if (activeTab === "All") { out.push(reqList[i]); }
    else if (reqList[i].status === activeTab.toLowerCase()) { out.push(reqList[i]); }
  }
  return out;
}

function showRequestRows() {
  var rows = filteredReqs();
  var box = document.getElementById("reqRows");
  var html = "";
  if (rows.length === 0) {
    html += "<div class='empty-box'>";
    html += "<div style='font-size:32px'>ℹ️</div>";
    html += "<p><b>No requests found</b></p>";
    html += "<p style='color:#94a3b8;font-size:12px'>Try switching active tab filters to see more requests.</p>";
    html += "<button class='small-btn' onclick='resetReqFilter()'>Reset Filters</button>";
    html += "</div>";
  } else {
    for (var i = 0; i < rows.length; i++) {
      var r = rows[i];
      html += "<div class='req-row'>";
      html += "<div class='req-col c1'><div class='avatar-circle'>" + r.initials + "</div><span class='req-name'>" + r.name + "</span><br><span class='req-role'>" + r.role + "</span><div class='clear'></div></div>";
      html += "<div class='req-col c2 req-venue'>" + r.venue + attachLink(r) + attendeeLine(r) + "</div>";
      html += "<div class='req-col c3 req-date'>" + r.date + "</div>";
      html += "<div class='req-col c5 req-time'>" + (r.time && r.time !== "" ? r.time : "—") + "</div>";
      html += "<div class='req-col c6 req-date'>" + (r.requested && r.requested !== "" ? r.requested : "—") + "</div>";
      html += "<div class='req-col c4'>";
      var pillClass = "pill-pending";
      var pillText = r.status;
      if (r.status === "approved") { pillClass = "pill-ok"; pillText = "✓ Approved"; }
      else if (r.status === "rejected") { pillClass = "pill-bad"; pillText = "✕ Rejected"; }
      else { pillText = "Pending"; }
      html += "<span class='" + pillClass + "'>" + pillText + "</span>";
      html += " <select class='req-status-edit' onchange='editReqStatus(" + r.id + ", this.value)'>";
      html += "<option value='Pending'" + (r.status === "pending" ? " selected" : "") + ">Pending</option>";
      html += "<option value='Approved'" + (r.status === "approved" ? " selected" : "") + ">Approved</option>";
      html += "<option value='Rejected'" + (r.status === "rejected" ? " selected" : "") + ">Rejected</option>";
      html += "</select>";
      html += "</div><div class='clear'></div></div>";
    }
  }
  box.innerHTML = html;
  document.getElementById("reqCount").innerHTML = "Showing " + rows.length + " total entries";
}

function approveReq(id) {
  for (var i = 0; i < reqList.length; i++) {
    if (reqList[i].id === id) {
      reqList[i].status = "approved";
      API.post("request_status", { id: id, status: "Approved" });
      showReqToast("Booking for " + reqList[i].name + " (" + reqList[i].venue + ") approved!", "green");
    }
  }
  showRequestTabs();
  showRequestRows();
}

function editReqStatus(id, newStatus) {
  API.post("request_status", { id: id, status: newStatus.charAt(0).toUpperCase() + newStatus.slice(1) })
    .then(function () {
      for (var i = 0; i < reqList.length; i++) {
        if (reqList[i].id === id) { reqList[i].status = newStatus.toLowerCase(); }
      }
      showReqToast("Status changed for request #" + id + " to " + newStatus, "green");
      showRequestTabs();
      showRequestRows();
    });
}

function rejectReq(id) {
  for (var i = 0; i < reqList.length; i++) {
    if (reqList[i].id === id) {
      reqList[i].status = "rejected";
      API.post("request_status", { id: id, status: "Rejected" });
      showReqToast("Booking for " + reqList[i].name + " (" + reqList[i].venue + ") rejected.", "red-bg");
    }
  }
  showRequestTabs();
  showRequestRows();
}

function resetReqFilter() {
  activeTab = "All";
  showRequestTabs();
  showRequestRows();
}

var reqTimer = null;
function showReqToast(msg, color) {
  var t = document.getElementById("toast");
  t.className = "toast " + color;
  document.getElementById("toastMsg").innerHTML = msg;
  t.style.display = "block";
  if (reqTimer !== null) { clearTimeout(reqTimer); }
  reqTimer = setTimeout(function() {
    t.style.display = "none";
  }, 3500);
}

function hideReqToast() {
  document.getElementById("toast").style.display = "none";
}
