/* ============================================================
   VenueVibe - venue-list.js
   Interactive Client Logic for Venue Catalog & Search
   ============================================================ */

var VENUE_PAGE = 1;
var VENUE_PER_PAGE = 12;
var VENUE_TYPE_FILTER = "all";
var VENUE_QUAD = "All";
var VENUE_STATUS = "All";

function getMatchingVenueCards() {
    var inputElem = document.getElementById("searchInput");
    var q = inputElem ? inputElem.value.toLowerCase().trim() : "";
    var cards = document.getElementsByClassName("venue-card");
    var out = [];
    for (var i = 0; i < cards.length; i++) {
        var card = cards[i];
        var text = card.innerText.toLowerCase();
        if (q !== "" && text.indexOf(q) === -1) { continue; }
        if (VENUE_TYPE_FILTER !== "all") {
            var typeElem = card.querySelector(".venue-type");
            var venueType = typeElem ? typeElem.innerText.toLowerCase() : "";
            if (venueType.indexOf(VENUE_TYPE_FILTER) === -1) { continue; }
        }
        if (VENUE_QUAD !== "All" && (card.dataset.quad || "") !== VENUE_QUAD) { continue; }
        if (VENUE_STATUS !== "All" && (card.dataset.status || "") !== VENUE_STATUS) { continue; }
        out.push(card);
    }
    return out;
}

function onQuadChange(val) { VENUE_QUAD = val; VENUE_PAGE = 1; showVenuePage(); }
function onStatusChange(val) { VENUE_STATUS = val; VENUE_PAGE = 1; showVenuePage(); }
function resetVenueFilters() {
    var box = document.getElementById("quadSelect");
    if (box) { box.value = "All"; }
    var st = document.getElementById("statusSelect");
    if (st) { st.value = "All"; }
    var input = document.getElementById("searchInput");
    if (input) { input.value = ""; }
    VENUE_QUAD = "All"; VENUE_STATUS = "All"; VENUE_TYPE_FILTER = "all";
    VENUE_PAGE = 1;
    showVenuePage();
}

function showVenuePage() {
    var matched = getMatchingVenueCards();
    var cards = document.getElementsByClassName("venue-card");
    for (var i = 0; i < cards.length; i++) { cards[i].style.display = "none"; }

    var totalPages = Math.max(1, Math.ceil(matched.length / VENUE_PER_PAGE));
    if (VENUE_PAGE > totalPages) { VENUE_PAGE = totalPages; }

    var start = (VENUE_PAGE - 1) * VENUE_PER_PAGE;
    for (var j = start; j < start + VENUE_PER_PAGE && j < matched.length; j++) {
        matched[j].style.display = "";
    }

    var pager = document.getElementById("venuePagination");
    if (pager) {
        var html = "";
        html += "<button class='page-btn' onclick='goVenuePage(" + (VENUE_PAGE - 1) + ")'" + (VENUE_PAGE === 1 ? " disabled" : "") + ">&laquo; Prev</button>";
        for (var p = 1; p <= totalPages; p++) {
            html += "<button class='page-btn" + (p === VENUE_PAGE ? " active" : "") + "' onclick='goVenuePage(" + p + ")'>" + p + "</button>";
        }
        html += "<button class='page-btn' onclick='goVenuePage(" + (VENUE_PAGE + 1) + ")'" + (VENUE_PAGE === totalPages ? " disabled" : "") + ">Next &raquo;</button>";
        pager.innerHTML = html;
    }
}

function goVenuePage(p) {
    var totalPages = Math.max(1, Math.ceil(getMatchingVenueCards().length / VENUE_PER_PAGE));
    if (p < 1 || p > totalPages) { return; }
    VENUE_PAGE = p;
    showVenuePage();
    var grid = document.getElementById("venueGrid");
    if (grid) { grid.scrollIntoView({ behavior: "smooth", block: "start" }); }
}

function renderVenues() {
    var grid = document.getElementById("venueGrid");
    if (!grid || typeof VENUES === "undefined") return;

    var html = "";
    for (var i = 0; i < VENUES.length; i++) {
        var v = VENUES[i];
        var badgeClass = (v.status === "Booked") ? "badge-booked" : "badge-available";

        html += "<div class='venue-card' onclick=\"goToDetails('" + v.id + "')\">";
        html += "  <div class='venue-image-wrap'>";
        html += "    <span class='" + badgeClass + "'>" + v.status + "</span>";
        html += "    <img class='venue-image' src='" + v.thumbnail + "' alt='" + v.name + "'>";
        html += "  </div>";
        html += "  <div class='card-body'>";
        html += "    <div class='card-title-row'>";
        html += "      <div class='venue-title'>" + v.name + "</div>";
        html += "      <div class='venue-type'>" + v.type + "</div>";
        html += "    </div>";
        html += "    <div class='venue-meta'><i class=\"fa-solid fa-location-dot\"></i> " + v.location + "</div>";
        html += "    <div class='venue-meta'><i class=\"fa-solid fa-users\"></i> Capacity: " + v.capacity + " People</div>";
        html += "    <button type='button' class='view-btn'>View Details</button>";
        html += "  </div>";
        html += "</div>";
    }

    grid.innerHTML = html;
    showVenuePage();
}

function searchVenue() {
    VENUE_PAGE = 1;
    showVenuePage();
}

function filterType(type, element) {
    var pills = document.querySelectorAll(".filter-pill");
    pills.forEach(function(pill) { pill.classList.remove("active"); });
    if (element) element.classList.add("active");

    VENUE_TYPE_FILTER = type.toLowerCase().trim();
    VENUE_PAGE = 1;
    showVenuePage();
}

function goToDetails(id) {
    var isPhp = window.location.pathname.indexOf(".php") > -1;
    var target = isPhp ? "venue-details.php" : "venue-details.html";
    window.location.href = target + "?venue=" + encodeURIComponent(id);
}

// Bind live search on input if present & init pagination
document.addEventListener("DOMContentLoaded", function() {
    var searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("keyup", searchVenue);
    }
    showVenuePage();
});
