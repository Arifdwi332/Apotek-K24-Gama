
<?php

use App\Models\M_User;
use Illuminate\Support\Facades\Auth;

if (!function_exists('get_user')) {
    /**
     * Ambil nama user dari ID
     *
     * @param  int|null $id
     * @return string
     */
    function get_user($id)
    {
        if (!$id) {
            return '-';
        }

        $user = M_User::find($id);
        return $user ? $user->username : '-';
    }

}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        $user = Auth::user();
        return (bool) ($user && $user->pegawai && (int)$user->pegawai->role_id === 1);
    }
}

if (!function_exists('only_admin')) {
    function only_admin(callable $callback): string
    {
        return is_admin() ? (string)$callback() : '';
    }
}
