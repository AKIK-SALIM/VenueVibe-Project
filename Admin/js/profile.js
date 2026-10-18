/* js / profile.js : admin header profile popup */

function openOverlay(el) {
  el.style.display = "flex";
  el.style.position = "fixed";
  el.style.inset = "0";
  el.style.background = "rgba(15,23,42,.45)";
  el.style.alignItems = "center";
  el.style.justifyContent = "center";
  el.style.zIndex = "999";
}

function toggleAdminProfile() {
  var el = document.getElementById("adminProfileOverlay");
  if (!el) { return; }
  if (el.style.display === "flex") {
    el.style.display = "none";
    return;
  }
  var p = window.ADMIN_PROFILE || {};
  document.getElementById("apName").innerHTML = p.name || "Admin";
  document.getElementById("apRole").innerHTML = (p.role || "Admin") + (p.department ? " · " + p.department : "");
  document.getElementById("apEmail").innerHTML = p.email || "-";
  document.getElementById("apDept").innerHTML = p.department || "-";
  var av = document.getElementById("apAvatar");
  if (av) { av.innerHTML = (p.name || "A").charAt(0).toUpperCase(); }
  openOverlay(el);
}
