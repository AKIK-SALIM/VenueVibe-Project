/* js / venues.js : logic for venues.html */

function openOverlay(el) {
  el.style.display = 'flex';
  el.style.position = 'fixed';
  el.style.inset = '0';
  el.style.background = 'rgba(15,23,42,.45)';
  el.style.alignItems = 'center';
  el.style.justifyContent = 'center';
  el.style.zIndex = '999';
}

var venueSearch = "";
var venueQuad = "All";
var venueStatus = "All";
var venueList = [];
var drawerType = "Auditorium";
var drawerAmenities = ["AV 4K Stage Setup", "Wi-Fi 6 High Density"];
var drawerStatus = "Active";
var editingId = null;

var DEFAULT_FORM = {
  name: "",
  quad: "North Academic Quad",
  building: "",
  floor: "",
  room: "",
  seated: "",
  standing: "",
  attendee: ""
};

var drawerMedia = "";
function uploadVenueMedia(input) {
  if (!input.files || !input.files.length) { return; }
  var fd = new FormData();
  for (var i = 0; i < input.files.length; i++) { fd.append('files[]', input.files[i]); }
  fetch('api.php?action=venue_media_upload', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (res && res.status === 'success' && res.media && res.media.length > 0) {
        drawerMedia = res.media[0];
        var lbl = document.getElementById('fMediaLabel');
        if (lbl) { lbl.innerHTML = 'Selected: ' + drawerMedia; }
      }
    });
}

function startVenues() {
  venueList = [];
  for (var i = 0; i < VENUES.length; i++) {
    venueList.push(VENUES[i]);
  }
  buildQuadOptions();
  showVenueStats();
  showVenues();
  showTypeButtons();
  showAmenityButtons();
}

function buildQuadOptions() {
  var sel = document.getElementById("quadSelect");
  var html = "<option value='All'>Campus: All Quads</option>";
  for (var i = 0; i < QUADS.length; i++) {
    html += "<option value='" + QUADS[i] + "'>" + QUADS[i] + "</option>";
  }
  sel.innerHTML = html;
}

function onSearchChange(val) {
  venueSearch = val;
  venuePage = 1;
  showVenues();
}

function clearSearch() {
  document.getElementById("venueSearch").value = "";
  venueSearch = "";
  showVenues();
}

function onQuadChange(val) {
  venueQuad = val;
  venuePage = 1;
  showVenues();
}

function onStatusChange(val) {
  venueStatus = val;
  venuePage = 1;
  showVenues();
}

function resetVenueFilters() {
  venueSearch = "";
  venueQuad = "All";
  venueStatus = "All";
  document.getElementById("venueSearch").value = "";
  document.getElementById("quadSelect").value = "All";
  document.getElementById("statusSelect").value = "All";
  venuePage = 1;
  showVenues();
}

function matchesVenue(v) {
  if (venueQuad !== "All" && v.quad !== venueQuad) { return false; }
  if (venueStatus !== "All" && v.status !== venueStatus) { return false; }
  if (venueSearch !== "") {
    var q = venueSearch.toLowerCase();
    var text = (v.name + " " + v.location + " " + v.type + " " + v.suiteCode).toLowerCase();
    if (text.indexOf(q) === -1) { return false; }
  }
  return true;
}

function showVenueStats() {
  var total = venueList.length;
  var active = 0;
  var maint = 0;
  var draft = 0;
  for (var i = 0; i < venueList.length; i++) {
    if (venueList[i].status === "Active") { active++; }
    else if (venueList[i].status === "Maintenance") { maint++; }
    else { draft++; }
  }
  document.getElementById("statTotal").innerHTML = total;
  document.getElementById("statActive").innerHTML = active;
  document.getElementById("statMaint").innerHTML = maint;
  document.getElementById("statDraft").innerHTML = draft;
}

var venuePage = 1;
var VENUES_PER_PAGE = 12;

function showVenues() {
  var box = document.getElementById("venueGrid");
  var html = "";
  var matched = [];
  var shown = 0;
  for (var i = 0; i < venueList.length; i++) {
    if (matchesVenue(venueList[i])) { matched.push(venueList[i]); }
  }
  var totalPages = Math.max(1, Math.ceil(matched.length / VENUES_PER_PAGE));
  if (venuePage > totalPages) { venuePage = totalPages; }
  var start = (venuePage - 1) * VENUES_PER_PAGE;
  for (var j = start; j < start + VENUES_PER_PAGE && j < matched.length; j++) {
    var v = matched[j];
    shown++;
    var badgeClass = "badge green";
    if (v.status === "Maintenance") { badgeClass = "badge orange"; }
    if (v.status === "Draft") { badgeClass = "badge gray"; }
    var slotColor = "#059669";
    if (v.status === "Maintenance") { slotColor = "#dc2626"; }
    html += "<div class='venue-card' onclick='openVenueDetail(\"" + v.id + "\")'>";
    html += "<div class='venue-img-wrap'>";
    html += "<img src='" + v.image + "' alt='" + v.name + "'>";
    html += "<div class='badge-row'>";
    html += "<span class='" + badgeClass + "'><span class='dot-pulse'></span>" + v.status + "</span>";
    html += "<span class='type-pill'>" + v.type + "</span>";
    html += "</div>";
    html += "<span class='room-pill'>" + v.suiteCode + "</span>";
    html += "</div>";
    html += "<div class='venue-body'>";
    html += "<p class='venue-name'>" + v.name + "</p>";
    html += "<p class='venue-loc'><svg width='13' height='13' viewBox='0 0 20 20' fill='none' stroke='#98a2b3' stroke-width='1.8'><path d='M10 18s6-5.3 6-10a6 6 0 0 0-12 0c0 4.7 6 10 6 10z'/><circle cx='10' cy='8' r='2.2'/></svg> " + v.location + "</p>";
    html += "<div class='venue-info'>";
    html += "<div><span class='info-label'>CAPACITY</span><b>" + v.capacity + "</b></div>";
    html += "<div><span class='info-label'>AV / SETUP</span><b>" + v.setup + "</b></div>";
    html += "<div><span class='info-label'>TODAY'S SLOTS</span><b style='color:" + slotColor + "'>" + v.slots + "</b></div>";
    html += "</div>";
    html += "</div>";
    html += "<div class='venue-foot'>";
    html += "<button class='edit-link' onclick='event.stopPropagation();openEditDrawer(\"" + v.id + "\")'><svg width='13' height='13' viewBox='0 0 20 20' fill='none' stroke='currentColor' stroke-width='1.8'><path d='M13.5 3.5l3 3L8 15l-4 1 1-4 8.5-8.5z'/></svg> Edit</button>";
    html += "<button class='del-link' onclick='event.stopPropagation();deleteVenue(\"" + v.id + "\")'><svg width='13' height='13' viewBox='0 0 20 20' fill='none' stroke='currentColor' stroke-width='1.8'><path d='M3 5h14M8 5V3h4v2M6 5l1 11h6l1-11'/></svg> Remove</button>";
    html += "<div class='clear'></div></div>";
    html += "</div>";
  }
  // register card at end
  html += "<div class='register-card'>";
  html += "<div style='display:inline-block;background:#e0e4ff;border-radius:14px;padding:12px'><svg width='28' height='28' viewBox='0 0 24 24' fill='none' stroke='#4f39f6' stroke-width='1.8'><path d='M5 21V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16'/><path d='M3 21h18'/><path d='M15 8h4a1 1 0 0 1 1 1v12'/></svg></div>";
  html += "<h3 style='color:#0f172a'>Register Facility</h3>";
  html += "<p style='font-size:12px;color:#64748b'>Introduce a newly renovated classroom, lab wing, lecture hall, or sports complex into VenueVibe.</p>";
  html += "<button class='open-wizard-btn' onclick='openDrawer()'>Open Facility Wizard →</button>";
  html += "</div>";
  html += "<div class='clear'></div>";
  box.innerHTML = html;

  // pagination controls
  var pager = document.getElementById("venuePagination");
  if (pager) {
    var phtml = "";
    phtml += "<button class='page-btn' onclick='setVenuePage(" + (venuePage - 1) + ")'" + (venuePage === 1 ? " disabled" : "") + ">&laquo; Prev</button>";
    for (var p = 1; p <= totalPages; p++) {
      phtml += "<button class='page-btn" + (p === venuePage ? " active" : "") + "' onclick='setVenuePage(" + p + ")'>" + p + "</button>";
    }
    phtml += "<button class='page-btn' onclick='setVenuePage(" + (venuePage + 1) + ")'" + (venuePage === totalPages ? " disabled" : "") + ">Next &raquo;</button>";
    pager.innerHTML = phtml;
  }

  showVenueStats();
}

function setVenuePage(p) {
  venuePage = p;
  showVenues();
}

function deleteVenue(id) {
  var kept = [];
  for (var i = 0; i < venueList.length; i++) {
    if (venueList[i].id !== id) { kept.push(venueList[i]); }
  }
  venueList = kept;
  API.post("venue_delete", { id: id });
  showVenues();
}

// ---- venue detail widget ----
function openVenueDetail(id) {
  var found = null;
  for (var i = 0; i < venueList.length; i++) {
    if (venueList[i].id === id) { found = venueList[i]; }
  }
  if (found === null) { return; }
  var badgeClass = "badge green";
  if (found.status === "Maintenance") { badgeClass = "badge orange"; }
  if (found.status === "Draft") { badgeClass = "badge gray"; }
  var slotColor = "#059669";
  if (found.status === "Maintenance") { slotColor = "#dc2626"; }
  var html = "";
  html += "<div class='venue-detail-img'>";
  html += "<img src='" + found.image + "' alt='" + found.name + "'>";
  html += "<div class='badge-row'><span class='" + badgeClass + "'><span class='dot-pulse'></span>" + found.status + "</span><span class='type-pill'>" + found.type + "</span></div>";
  html += "<span class='room-pill'>" + found.suiteCode + "</span>";
  html += "</div>";
  html += "<div class='venue-detail-body'>";
  html += "<p class='venue-name' style='white-space:normal'>" + found.name + "</p>";
  html += "<p class='venue-loc'><svg width='13' height='13' viewBox='0 0 20 20' fill='none' stroke='#98a2b3' stroke-width='1.8'><path d='M10 18s6-5.3 6-10a6 6 0 0 0-12 0c0 4.7 6 10 6 10z'/><circle cx='10' cy='8' r='2.2'/></svg> " + found.location + "</p>";
  html += "<div class='venue-info'>";
  html += "<div><span class='info-label'>CAPACITY</span><b>" + found.capacity + "</b></div>";
  html += "<div><span class='info-label'>AV / SETUP</span><b>" + found.setup + "</b></div>";
  html += "<div><span class='info-label'>TODAY'S SLOTS</span><b style='color:" + slotColor + "'>" + found.slots + "</b></div>";
  html += "</div>";
  html += "<div class='detail-grid'>";
  html += "<div><span class='detail-key'>CAMPUS QUAD</span><span class='detail-val'>" + found.quad + "</span></div>";
  html += "<div><span class='detail-key'>BUILDING</span><span class='detail-val'>" + found.building + "</span></div>";
  html += "<div><span class='detail-key'>FLOOR</span><span class='detail-val'>" + found.floor + "</span></div>";
  html += "<div><span class='detail-key'>ROOM / SUITE</span><span class='detail-val'>" + found.suiteCode + "</span></div>";
  if (found.attendeeEmail) {
    html += "<div><span class='detail-key'>ATTENDEE EMAIL</span><span class='detail-val'>" + found.attendeeEmail + "</span></div>";
  }
  html += "</div>";
  html += "<div class='detail-actions'>";
  html += "<button class='small-btn' onclick='closeVenueDetail()'>Close</button>";
  html += "<button class='small-btn danger' onclick='deleteVenue(\"" + found.id + "\");closeVenueDetail()'>Remove</button>";
  html += "<button class='btn' onclick='closeVenueDetail();openEditDrawer(\"" + found.id + "\")'>Edit Details</button>";
  html += "</div>";
  html += "</div>";
  document.getElementById("venueDetailBody").innerHTML = html;
  openOverlay(document.getElementById("venueModal"));
}

function closeVenueDetail() {
  document.getElementById("venueModal").style.display = "none";
}

// ---- drawer (add + edit) ----
function setFormValues(vals) {
  document.getElementById("fName").value = vals.name;
  document.getElementById("fQuad").value = vals.quad;
  document.getElementById("fBuilding").value = vals.building;
  document.getElementById("fFloor").value = vals.floor;
  document.getElementById("fRoom").value = vals.room;
  document.getElementById("fSeated").value = vals.seated;
  document.getElementById("fStanding").value = vals.standing;
  document.getElementById("fAttendee").value = vals.attendee || "";
}

function openDrawer() {
  // add mode: fresh empty form
  editingId = null;
  drawerType = "Auditorium";
  drawerAmenities = [];
  drawerStatus = "Active";
  drawerMedia = "";
  var lbl = document.getElementById('fMediaLabel');
  if (lbl) { lbl.innerHTML = ""; }
  setFormValues(DEFAULT_FORM);
  document.getElementById("statusActive").checked = true;
  document.getElementById("drawerTitle").innerHTML = "Add New Venue";
  document.getElementById("drawerSaveBtn").innerHTML = "&#10003; Save & Add Venue";
  showTypeButtons();
  showAmenityButtons();
  document.getElementById("drawerBg").style.display = "flex";
}

function openEditDrawer(id) {
  // edit mode: pre-fill form with this venue's details
  var found = null;
  for (var i = 0; i < venueList.length; i++) {
    if (venueList[i].id === id) { found = venueList[i]; }
  }
  if (found === null) { return; }
  editingId = id;
  drawerType = found.type;
  drawerAmenities = [];
  drawerStatus = found.status === "Active" ? "Active" : "Draft";
  drawerMedia = "";
  var lbl = document.getElementById('fMediaLabel');
  if (lbl) { lbl.innerHTML = ""; }
  var seats = found.capacity.replace(/[^0-9]/g, "");
  setFormValues({
    name: found.name,
    quad: found.quad,
    building: found.building,
    floor: found.floor,
    room: found.suiteCode,
    seated: seats === "" ? "100" : seats,
    standing: document.getElementById("fStanding").value,
    attendee: found.attendeeEmail || ""
  });
  document.getElementById("statusActive").checked = (drawerStatus === "Active");
  document.getElementById("statusDraft").checked = (drawerStatus !== "Active");
  document.getElementById("drawerTitle").innerHTML = "Edit Venue";
  document.getElementById("drawerSaveBtn").innerHTML = "&#10003; Save Changes";
  showTypeButtons();
  showAmenityButtons();
  document.getElementById("drawerBg").style.display = "flex";
}

function closeDrawer() {
  document.getElementById("drawerBg").style.display = "none";
  editingId = null;
}

function pickType(t) {
  drawerType = t;
  showTypeButtons();
}

function showTypeButtons() {
  var box = document.getElementById("typeButtons");
  if (!box) { return; }
  var html = "";
  for (var i = 0; i < VENUE_TYPES.length; i++) {
    var cls = "type-btn";
    if (VENUE_TYPES[i] === drawerType) { cls = "type-btn selected"; }
    html += "<button type='button' class='" + cls + "' onclick='pickType(\"" + VENUE_TYPES[i] + "\")'>" + VENUE_TYPES[i] + "</button>";
  }
  box.innerHTML = html;
}

function pickAmenity(a) {
  var pos = drawerAmenities.indexOf(a);
  if (pos === -1) { drawerAmenities.push(a); }
  else { drawerAmenities.splice(pos, 1); }
  showAmenityButtons();
}

function showAmenityButtons() {
  var box = document.getElementById("amenityButtons");
  if (!box) { return; }
  var html = "";
  for (var i = 0; i < AMENITIES.length; i++) {
    var cls = "amenity-btn";
    var tick = "";
    if (drawerAmenities.indexOf(AMENITIES[i]) !== -1) {
      cls = "amenity-btn selected";
      tick = "<span class='tick'>&#10003;</span>";
    }
    html += "<button type='button' class='" + cls + "' onclick='pickAmenity(\"" + AMENITIES[i] + "\")'>" + AMENITIES[i] + tick + "</button>";
  }
  box.innerHTML = html;
}

function pickStatus(s) {
  drawerStatus = s;
}

function saveVenue() {
  var name = document.getElementById("fName").value;
  if (name === "") {
    alert("Venue name is required.");
    return;
  }
  var quad = document.getElementById("fQuad").value;
  var building = document.getElementById("fBuilding").value;
  var floor = document.getElementById("fFloor").value;
  var room = document.getElementById("fRoom").value;
  var seated = document.getElementById("fSeated").value;
  if (seated === "") { seated = "100"; }
  var attendee = document.getElementById("fAttendee").value.trim();
  if (attendee !== "" && attendee.indexOf("@") === -1) {
    alert("Attendee email looks invalid. Please enter a valid email or leave it empty.");
    return;
  }
  var setup = "Standard Config";
  if (drawerAmenities.length > 0) { setup = drawerAmenities[0]; }
  var status = "Active";
  if (drawerStatus !== "Active") { status = "Draft"; }

  if (editingId !== null) {
    // update the existing venue in place, keep its photo and slots
    for (var k = 0; k < venueList.length; k++) {
      if (venueList[k].id === editingId) {
        venueList[k].name = name;
        venueList[k].suiteCode = room === "" ? "Suite 100" : room;
        venueList[k].quad = quad;
        venueList[k].building = building;
        venueList[k].floor = floor;
        venueList[k].location = quad + ", " + building;
        venueList[k].type = drawerType;
        venueList[k].status = status;
        venueList[k].capacity = seated + " seats";
        venueList[k].attendeeEmail = attendee;
        if (drawerAmenities.length > 0) { venueList[k].setup = setup; }
        API.post("venue_save", { id: editingId, name: name, quad: quad, building: building, floor: floor, room: room, seated: seated, standing: document.getElementById("fStanding").value || 0, type: drawerType, status: status, setup: setup, image: drawerMedia, attendee_email: attendee });
      }
    }
    closeDrawer();
    showVenues();
    return;
  }

  var newVenue = {
    id: "v-" + Date.now(),
    name: name,
    suiteCode: room === "" ? "Suite 100" : room,
    quad: quad,
    building: building,
    floor: floor,
    location: quad + ", " + building,
    type: drawerType,
    status: status,
    capacity: seated + " seats",
    setup: setup,
    slots: "Open Registration",
    image: "https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=800&q=80"
  };
  API.post("venue_save", { name: name, quad: quad, building: building, floor: floor, room: room, seated: seated, standing: document.getElementById("fStanding").value || 0, type: drawerType, status: status, setup: setup, image: drawerMedia, attendee_email: attendee })
    .then(function (res) {
      if (res && res.status === "success" && res.id) { newVenue.id = res.id; }
      venueList.unshift(newVenue);
      closeDrawer();
      showVenues();
    });
}
