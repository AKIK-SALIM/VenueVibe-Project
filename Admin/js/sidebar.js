/* js / sidebar.js : mobile menu open/close. Basic JS. */

function toggleMenu() {
  var bar = document.getElementById("sidebar");
  if (bar.className === "sidebar") {
    bar.className = "sidebar open";
  } else {
    bar.className = "sidebar";
  }
}

function closeMenu() {
  var bar = document.getElementById("sidebar");
  // only hide on small screens, keep visible on big screens
  if (window.innerWidth < 900) {
    bar.className = "sidebar";
  }
}
