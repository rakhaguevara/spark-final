<?php

function formatRupiah($angka): string {
    return 'Rp ' . number_format((int)$angka, 0, ',', '.');
}

function formatDate($date): string {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $d = date('Y-m-d', strtotime($date));
    [$y, $m, $day] = explode('-', $d);

    return $day . ' ' . $bulan[(int)$m] . ' ' . $y;
}

function formatTime($time): string {
    return date('H:i', strtotime($time));
}
