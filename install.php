<?php
// Baixa os arquivos grandes do build (ja publicos) para esta pasta.
// Uso: acessar /install.php uma vez, depois apagar este arquivo.
header('Content-Type: text/plain; charset=utf-8');

$base = __DIR__;
$src  = 'https://crm---faith-system.odontomaridigital.workers.dev';

if (is_dir($base . '/assets')) {
    echo "Pasta assets ja existe.\n";
} else {
    @mkdir($base . '/assets', 0755, true);
}

$files = [
    'assets/index-ByiYiZdA.js',
    'assets/index-U_mMUZfB.css',
    'favicon.ico',
    'og-image.png',
    'placeholder.svg',
    'skip.png',
];

$ok = 0; $fail = [];
foreach ($files as $rel) {
    $dest = $base . '/' . $rel;
    $dir  = dirname($dest);
    if (!is_dir($dir)) { @mkdir($dir, 0755, true); }

    $data = @file_get_contents($src . '/' . $rel);
    if (($data === false || strlen($data) < 50) && function_exists('curl_init')) {
        $ch = curl_init($src . '/' . $rel);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 180,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'KingHost-install/1.0',
        ]);
        $data = curl_exec($ch);
        curl_close($ch);
    }

    if ($data === false || strlen($data) < 50) { $fail[] = $rel . ' (download falhou)'; continue; }
    if (@file_put_contents($dest, $data) === false) { $fail[] = $rel . ' (sem permissao de escrita)'; continue; }
    echo 'OK   ' . $rel . '  ' . strlen($data) . " bytes\n";
    $ok++;
}

echo "\n---\n" . $ok . " arquivo(s) gravado(s) de " . count($files) . ".\n";
if ($fail) { echo 'FALHOU: ' . implode(', ', $fail) . "\n"; }

foreach ($files as $rel) {
    $d = $base . '/' . $rel;
    printf("%-34s %s\n", $rel, is_file($d) ? filesize($d) . ' bytes' : 'AUSENTE');
}
