<?= view('layout/header', ['title' => esc($book['title'] ?? 'Item') . ' • FIND']); ?>
<?= view('layout/navbar'); ?>
<?php require("item_details.php"); ?>
<?= view('layout/footer'); ?>