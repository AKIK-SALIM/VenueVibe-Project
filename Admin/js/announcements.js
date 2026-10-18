/* js / announcements.js : logic for announcements.html */

function openOverlay(el) {
  el.style.display = 'flex';
  el.style.position = 'fixed';
  el.style.inset = '0';
  el.style.background = 'rgba(15,23,42,.45)';
  el.style.alignItems = 'center';
  el.style.justifyContent = 'center';
  el.style.zIndex = '999';
}

var annList = [];
var annSearch = "";
var annCategory = "All Categories";
var annTab = "All";

var CATEGORIES = ["All Categories", "Maintenance", "Booking Policy", "Weather Alert", "Event Notice"];
var TABS = ["All", "Active", "Scheduled", "Drafts", "Archived"];

function startAnnouncements() {
  annList = [];
  for (var i = 0; i < ANNOUNCEMENTS.length; i++) {
    annList.push(ANNOUNCEMENTS[i]);
  }
  buildAnnCategories();
  showAnnTabs();
  showAnnStats();
  showAnnList();
}

function buildAnnCategories() {
  var sel = document.getElementById("annCategory");
  var html = "";
  for (var i = 0; i < CATEGORIES.length; i++) {
    html += "<option value='" + CATEGORIES[i] + "'>" + CATEGORIES[i] + "</option>";
  }
  sel.innerHTML = html;
}

function onAnnSearch(val) {
  annSearch = val;
  showAnnList();
}

function onAnnCategory(val) {
  annCategory = val;
  showAnnList();
}

function setAnnTab(t) {
  annTab = t;
  showAnnTabs();
  showAnnList();
}

function showAnnTabs() {
  var box = document.getElementById("annTabs");
  var html = "";
  var counts = { All: annList.length, Active: 0, Scheduled: 0, Drafts: 0, Archived: 0 };
  for (var c = 0; c < annList.length; c++) {
    var st = annList[c].status;
    if (st === "Active") { counts.Active++; }
    else if (st === "Scheduled") { counts.Scheduled++; }
    else if (st === "Archived") { counts.Archived++; }
    else { counts.Drafts++; }
  }
  for (var i = 0; i < TABS.length; i++) {
    var cls = "cat-tab";
    if (TABS[i] === annTab) { cls = "cat-tab active"; }
    html += "<button class='" + cls + "' onclick='setAnnTab(\"" + TABS[i] + "\")'>" + TABS[i] + " (" + counts[TABS[i]] + ")</button>";
  }
  box.innerHTML = html;
}

function showAnnStats() {
  var sched = 0;
  for (var i = 0; i < annList.length; i++) {
    if (annList[i].status === "Scheduled") { sched++; }
  }
  var el = document.getElementById("schedCount");
  if (el) { el.innerHTML = sched; }
}

function matchesAnn(a) {
  if (annTab !== "All" && a.status !== annTab) { return false; }
  if (annCategory !== "All Categories" && a.category !== annCategory) { return false; }
  if (annSearch !== "") {
    var q = annSearch.toLowerCase();
    var text = (a.title + " " + a.content + " " + (a.author || "")).toLowerCase();
    if (text.indexOf(q) === -1) { return false; }
  }
  return true;
}

function tagClass(tag) {
  if (tag === "Maintenance") { return "tag tag-blue"; }
  if (tag === "Urgent") { return "tag tag-red"; }
  if (tag === "Weather Alert") { return "tag tag-orange"; }
  if (tag === "Event Notice") { return "tag tag-cyan"; }
  return "tag tag-gray";
}

// small outline icons (same style as the rest of the app)
var IC_USERS = "<svg width='13' height='13' viewBox='0 0 20 20' fill='none' stroke='#4f39f6' stroke-width='1.8'><circle cx='7' cy='7' r='3'/><path d='M2 17c0-3 2.5-5 5-5s5 2 5 5'/><circle cx='14' cy='8' r='2.2'/><path d='M15.5 12.5c2 .5 3.5 2 3.5 4.5'/></svg>";
var IC_CLOCK = "<svg width='13' height='13' viewBox='0 0 20 20' fill='none' stroke='#4f39f6' stroke-width='1.8'><circle cx='10' cy='10' r='7'/><path d='M10 6v4l2.5 2.5'/></svg>";
var IC_USER = "<svg width='13' height='13' viewBox='0 0 20 20' fill='none' stroke='#4f39f6' stroke-width='1.8'><circle cx='10' cy='7' r='3.2'/><path d='M3.5 17c0-3.5 3-5.5 6.5-5.5s6.5 2 6.5 5.5'/></svg>";
var IC_EYE = "<svg width='14' height='14' viewBox='0 0 20 20' fill='none' stroke='#12a56f' stroke-width='1.8'><path d='M2 10s3-5.5 8-5.5S18 10 18 10s-3 5.5-8 5.5S2 10 2 10z'/><circle cx='10' cy='10' r='2.2'/></svg>";
var IC_CLOCK_GRAY = "<svg width='13' height='13' viewBox='0 0 20 20' fill='none' stroke='#64748b' stroke-width='1.8'><circle cx='10' cy='10' r='7'/><path d='M10 6v4l2.5 2.5'/></svg>";
var IC_BOX_GRAY = "<svg width='13' height='13' viewBox='0 0 20 20' fill='none' stroke='#64748b' stroke-width='1.8'><rect x='3' y='6' width='14' height='11' rx='2'/><path d='M3 9.5h14M7 6V4.5A1.5 1.5 0 0 1 8.5 3h3A1.5 1.5 0 0 1 13 4.5V6'/></svg>";
var IC_PENCIL = "<svg width='14' height='14' viewBox='0 0 20 20' fill='none' stroke='currentColor' stroke-width='1.8'><path d='M13.5 3.5l3 3L8 15l-4 1 1-4 8.5-8.5z'/></svg>";
var IC_CHART = "<svg width='14' height='14' viewBox='0 0 20 20' fill='none' stroke='currentColor' stroke-width='1.8'><path d='M3 17h14'/><path d='M6 17v-5M10.5 17V8M15 17v-8'/></svg>";
var IC_TRASH = "<svg width='14' height='14' viewBox='0 0 20 20' fill='none' stroke='currentColor' stroke-width='1.8'><path d='M3 5h14M8 5V3h4v2M6 5l1 11h6l1-11'/></svg>";
var IC_SEARCH_BIG = "<svg width='22' height='22' viewBox='0 0 20 20' fill='none' stroke='#94a3b8' stroke-width='1.8'><circle cx='9' cy='9' r='6'/><path d='M13.5 13.5L17 17'/></svg>";

function showAnnList() {
  var box = document.getElementById("annList");
  var html = "";
  var shown = 0;
  for (var i = 0; i < annList.length; i++) {
    var a = annList[i];
    if (!matchesAnn(a)) { continue; }
    shown++;
    var statusCls = "status-pill gray";
    if (a.status === "Active") { statusCls = "status-pill green"; }
    if (a.status === "Scheduled") { statusCls = "status-pill blue"; }
    html += "<div class='ann-card' onclick='openAnnDetail(\"" + a.id + "\")'>";
    html += "<div>";
    for (var t = 0; t < (a.tags || []).length; t++) {
      html += "<span class='" + tagClass(a.tags[t]) + "'>" + a.tags[t] + "</span>";
    }
    html += "<span class='" + statusCls + "'>" + a.statusLabel + "</span>";
    html += "<div class='clear'></div></div>";
    html += "<p class='ann-title'>" + a.title + "</p>";
    html += "<p class='ann-text'>" + a.content + "</p>";
    html += "<div class='ann-info'><span>" + IC_USERS + " " + a.audience + "</span><span>" + IC_CLOCK + " " + a.time + "</span><span>" + IC_USER + " " + (a.author || "") + "</span></div>";
    html += "</div>";
  }
  if (shown === 0) {
    html += "<div class='ann-card' style='text-align:center;padding:40px'>";
    html += "<div class='empty-icon'>" + IC_SEARCH_BIG + "</div>";
    html += "<h3 style='color:#1d2939'>No announcements found</h3>";
    html += "<p style='font-size:12px;color:#64748b'>Try adjusting your search query, status tab, or category filters.</p>";
    html += "<button class='small-btn' onclick='resetAnnFilters()'>Reset Filters</button>";
    html += "</div>";
  }
  box.innerHTML = html;
  var cnt = document.getElementById("annCount");
  if (cnt) { cnt.innerHTML = "Showing <b>" + shown + "</b> primary active records"; }
  showAnnStats();
}

function deleteAnn(id) {
  var kept = [];
  for (var i = 0; i < annList.length; i++) {
    if (annList[i].id !== id) { kept.push(annList[i]); }
  }
  annList = kept;
  API.post("announcement_delete", { id: id });
  showAnnList();
}

// ---- announcement detail widget ----
function openAnnDetail(id) {
  var found = null;
  for (var i = 0; i < annList.length; i++) {
    if (annList[i].id === id) { found = annList[i]; }
  }
  if (found === null) { return; }
  var statusCls = "status-pill gray";
  if (found.status === "Active") { statusCls = "status-pill green"; }
  if (found.status === "Scheduled") { statusCls = "status-pill blue"; }
  var html = "";
  html += "<div>";
  for (var t = 0; t < (found.tags || []).length; t++) {
    html += "<span class='" + tagClass(found.tags[t]) + "'>" + found.tags[t] + "</span>";
  }
  html += "<span class='" + statusCls + "'>" + found.statusLabel + "</span>";
  html += "<div class='clear'></div></div>";
  html += "<h2 class='ann-detail-title'>" + found.title + "</h2>";
  html += "<p class='ann-detail-text'>" + found.content + "</p>";
  html += "<div class='ann-info'><span>" + IC_USERS + " " + found.audience + "</span><span>" + IC_CLOCK + " " + found.time + "</span><span>" + IC_USER + " " + (found.author || "") + "</span></div>";
  html += "<div class='ann-detail-grid'>";
  html += "<div><span class='detail-key'>CATEGORY</span><span class='detail-val'>" + found.category + "</span></div>";
  html += "<div><span class='detail-key'>PRIORITY</span><span class='detail-val'>" + found.priority + "</span></div>";
  html += "<div><span class='detail-key'>STATUS</span><span class='detail-val'>" + found.status + "</span></div>";
  if (found.reads) {
    var extra = found.reads + " reads";
    if (found.delivery) { extra += " • " + found.delivery + " delivery"; }
    html += "<div><span class='detail-key'>REACH</span><span class='detail-val'>" + extra + "</span></div>";
  } else if (found.countdown) {
    html += "<div><span class='detail-key'>DISPATCH</span><span class='detail-val'>" + found.countdown + "</span></div>";
  } else if (found.retention) {
    html += "<div><span class='detail-key'>RETENTION</span><span class='detail-val'>" + found.retention + "</span></div>";
  }
  html += "</div>";
  html += "<div class='ann-actions'><button class='small-btn' onclick='closeAnnDetail()'>Close</button></div>";
  document.getElementById("annDetailBody").innerHTML = html;
  openOverlay(document.getElementById("annDetailModal"));
}

function closeAnnDetail() {
  document.getElementById("annDetailModal").style.display = "none";
}

function resetAnnFilters() {
  annSearch = "";
  annCategory = "All Categories";
  annTab = "All";
  document.getElementById("annSearch").value = "";
  document.getElementById("annCategory").value = "All Categories";
  showAnnTabs();
  showAnnList();
}

// ---- create modal ----
function openAnnModal() {
  openOverlay(document.getElementById("annModal"));
}

function closeAnnModal() {
  document.getElementById("annModal").style.display = "none";
}

function publishAnn() {
  var title = document.getElementById("aTitle").value;
  var content = document.getElementById("aContent").value;
  if (title === "" || content === "") {
    alert("Please fill title and content.");
    return;
  }
  var cat = document.getElementById("aCategory").value;
  var pri = document.getElementById("aPriority").value;
  var aud = "All Faculty & Students IC";
  var sched = document.getElementById("aTime").value;
  var status = "Active";
  var label = "Active / Live";
  if (sched !== "") {
    status = "Scheduled";
    label = "Scheduled: " + sched;
  }
  var newAnn = {
    id: "" + Date.now(),
    title: title,
    content: content,
    category: cat,
    priority: pri,
    status: status,
    statusLabel: label,
    audience: aud,
    time: sched === "" ? "Oct 24, 2024 • 10:00 AM" : sched,
    author: "Admin User",
    reads: "0",
    delivery: "100%",
    tags: [cat, pri]
  };
  annList.unshift(newAnn);
  API.post("announcement_create", { title: title, content: content, category: cat, priority: pri, audience: aud, time: sched });
  document.getElementById("aTitle").value = "";
  document.getElementById("aContent").value = "";
  document.getElementById("aTime").value = "";
  closeAnnModal();
  showAnnList();
}
