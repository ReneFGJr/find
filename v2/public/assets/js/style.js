// Dark mode toggle
document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById("darkModeToggle");
  const icon = document.getElementById("darkModeIcon");
  const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
  let dark = localStorage.getItem("find_dark_mode");
  if (dark === null) {
    dark = prefersDark ? "1" : "0";
  }

  function setDarkMode(on) {
    document.body.classList.toggle("dark-mode", on);
    if (icon) icon.className = on ? "bi bi-sun" : "bi bi-moon";
    localStorage.setItem("find_dark_mode", on ? "1" : "0");
  }
  setDarkMode(dark === "1");
  if (toggle) {
    toggle.addEventListener("click", function () {
      const isDark = document.body.classList.toggle("dark-mode");
      if (icon) icon.className = isDark ? "bi bi-sun" : "bi bi-moon";
      localStorage.setItem("find_dark_mode", isDark ? "1" : "0");
    });
  }
});
