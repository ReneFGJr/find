<?php
if (!isset($title)) { $title = ':: Title ::';}
?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title><?php echo $title;?></title>
  <link rel="stylesheet" href="<?php echo base_url('css/bootstrap.css?v5.1');?>">
  <link rel="stylesheet" href="<?php echo base_url('css/style.css?version=0.21.06B');?>">
  <link rel="stylesheet" href="<?php echo base_url('css/style_form_sisdoc.css');?>">

  <script type="text/javascript" src="<?php echo base_url('js/jquery.js?v3.3.6');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('js/utils.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('js/tether.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('js/bootstrap.js?v5.1');?>"></script>  <script type="text/javascript" src="<?php echo base_url('js/jquery.mask.js');?>"></script>
  <script type="text/javascript" src="<?php echo base_url('js/form_sisdoc.js');?>"></script>

  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300&display=swap" rel="stylesheet">
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.js"></script>
<style>
    body.dark-mode {
      background: #181a1b !important;
      color: #f1f1f1 !important;
    }
    body.dark-mode .navbar,
    body.dark-mode .dropdown-menu {
      background-color: #23272b !important;
      color: #f1f1f1 !important;
    }
    body.dark-mode .navbar .nav-link,
    body.dark-mode .dropdown-item {
      color: #f1f1f1 !important;
    }
    body.dark-mode .navbar-brand img {
      filter: brightness(0.85) contrast(1.2);
    }
    body.dark-mode .form-control,
    body.dark-mode .form-select {
      background-color: #23272b !important;
      color: #f1f1f1 !important;
      border-color: #333a40 !important;
    }
    body.dark-mode .form-control:focus,
    body.dark-mode .form-select:focus {
      background-color: #23272b !important;
      color: #f1f1f1 !important;
    }
    body.dark-mode .btn-outline-light {
      color: #fff;
      border-color: #888;
    }
    body.dark-mode .btn-outline-light:hover {
      background: #333a40;
      color: #fff;
    }
    body.dark-mode .bg-white,
    body.dark-mode .border-top {
      background: #23272b !important;
      border-color: #333a40 !important;
    }
    body.dark-mode .text-muted {
      color: #b0b0b0 !important;
    }
</style>
<script>
// Dark mode toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('darkModeToggle');
    const icon = document.getElementById('darkModeIcon');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    let dark = localStorage.getItem('find_dark_mode');
    if (dark === null) {
        dark = prefersDark ? '1' : '0';
    }
    function setDarkMode(on) {
        document.body.classList.toggle('dark-mode', on);
        if (icon) icon.className = on ? 'bi bi-sun' : 'bi bi-moon';
        localStorage.setItem('find_dark_mode', on ? '1' : '0');
    }
    setDarkMode(dark === '1');
    if (toggle) {
        toggle.addEventListener('click', function() {
            const isDark = document.body.classList.toggle('dark-mode');
            if (icon) icon.className = isDark ? 'bi bi-sun' : 'bi bi-moon';
            localStorage.setItem('find_dark_mode', isDark ? '1' : '0');
        });
    }
});
</script>
