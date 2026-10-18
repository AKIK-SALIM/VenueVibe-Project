/* js / data.js
   Shared data layer for all Admin pages.
   Fetches real data from ../Admin/api.php (PHP + MySQL backend).
   Basic JS only (var, no modules). */

var BOOKING_REQUESTS = [];
var VENUES = [];
var NOTICES = [];
var CHAT_USERS = [];
var ANNOUNCEMENTS = [];
var QUADS = ["North Academic Quad", "West Wing", "Student Union", "Science Complex", "South Quad"];
var VENUE_TYPES = ["Auditorium", "Seminar Room", "Banquet Hall", "Innovation Lab", "Outdoor Plaza", "Athletics Complex", "Lecture Hall", "Lab", "Seminar"];
var AMENITIES = ["AV 4K Stage Setup", "Hybrid Dual Broadcast", "Wi-Fi 6 High Density", "ADA Compliant Ramp"];
var MONTH_NAMES = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

var API = {
  get: function (action, params) {
    var url = "api.php?action=" + action;
    if (params) {
      for (var k in params) { url += "&" + encodeURIComponent(k) + "=" + encodeURIComponent(params[k]); }
    }
    return fetch(url, { credentials: "same-origin" }).then(function (r) { return r.json(); });
  },
  post: function (action, data) {
    data = data || {};
    data.action = action;
    return fetch("api.php", {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    }).then(function (r) { return r.json(); });
  }
};

function loadAdminData() {
  return Promise.all([
    API.get("dashboard"),
    API.get("venues"),
    API.get("announcements")
  ]).then(function (results) {
    var dash = results[0] || {};
    var venues = results[1] || {};
    var anns = results[2] || {};

    if (dash.status === "success") {
      BOOKING_REQUESTS = dash.requests || [];
      NOTICES = dash.notices || [];
      CHAT_USERS = dash.chatUsers || [];
      window.TOTAL_BOOKINGS = dash.totalBookings;
      window.PENDING_COUNT = dash.pendingCount;
      window.TOTAL_VENUES = dash.totalVenues;
      window.ACTIVE_VENUES = dash.activeVenues;
      window.BOOKED_DAYS = dash.bookedDays || [];
      window.TODAY_DAY = dash.today;
      window.CURRENT_MONTH = dash.month;
    }
    if (venues.status === "success") {
      VENUES = venues.venues || [];
      var seen = {};
      var quads = [];
      for (var i = 0; i < VENUES.length; i++) {
        if (!seen[VENUES[i].quad]) { seen[VENUES[i].quad] = true; quads.push(VENUES[i].quad); }
      }
      if (quads.length > 0) { QUADS = quads; }
    }
    if (anns.status === "success") {
      ANNOUNCEMENTS = anns.announcements || [];
    }
  }).catch(function () {
    // leave globals as-is; pages render empty states
  });
}

/* Entry point called by each page's onload */
function initPage() {
  loadAdminData().then(function () {
    if (typeof startDashboard === "function") { startDashboard(); }
    else if (typeof startRequests === "function") { startRequests(); }
    else if (typeof startVenues === "function") { startVenues(); }
    else if (typeof startAnnouncements === "function") { startAnnouncements(); }
  });
}
