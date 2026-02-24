<?php
$file = "/var/www/html/app/Livewire/SelfRegistration.php";
$content = file_get_contents($file);

// Add redirectDashboard method
$method = "
    protected function redirectDashboard()
    {
        \$user = auth()->user();
        if (\$user->hasRole('Pimpinan Lembaga Pengusul')) {
            return redirect()->to('/lembaga');
        } 
        if (\$user->hasAnyRole(['Kepala SPPG', 'Staf Gizi', 'Staf Akuntan', 'Staf Administrator SPPG'])) {
            return redirect()->to('/sppg');
        }
        if (\$user->hasRole('super_admin')) {
            return redirect()->to('/admin');
        }
        return redirect()->to('/');
    }
";

if (strpos($content, "redirectDashboard()") === false) {
    $content = substr($content, 0, strrpos($content, "}")) . $method . "\n}";
}

// Update register() redirect
$content = str_replace(
    "return redirect()->route('register.success');",
    "auth()->login(\$user); return \$this->redirectDashboard();",
    $content
);

// Update mount() auth check
$mountCheck = "
        if (auth()->check()) {
            return \$this->redirectDashboard();
        }
";
if (strpos($content, "auth()->check()") === false) {
    $content = str_replace(
        "public function mount(string \$role = '', string \$token = '')\n    {",
        "public function mount(string \$role = '', string \$token = '')\n    {" . $mountCheck,
        $content
    );
}

// Update validateToken used_count check
$usedCheck = "} elseif (\$token->used_count >= \$token->max_uses) {
                if (auth()->check()) {
                    return \$this->redirectDashboard();
                }
                \$this->tokenError = 'Kode registrasi sudah mencapai batas penggunaan. Silakan login menggunakan akun yang telah diaktivasi.';
            }";

$content = preg_replace(
    "/\} elseif \(\\\\\$token->used_count >= \\\\\$token->max_uses\) \{.*?\\\$this->tokenError = '.*?'\;\\n\s+\}/s",
    $usedCheck,
    $content
);

file_put_contents($file, $content);
