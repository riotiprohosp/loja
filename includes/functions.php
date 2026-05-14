<?php
function moneyBR(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function asset(string $path): string
{
    return $path;
}
