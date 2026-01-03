<?php

function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash_message'] = compact('type', 'message');
}

function getFlashMessage(): ?array {
    if (!isset($_SESSION['flash_message'])) return null;

    $flash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
    return $flash;
}
