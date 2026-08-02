<?php

namespace App\Policies;

use App\Models\Letter;
use App\Models\User;

/**
 * Policy akses surat (announcement).
 *
 * Surat merupakan pengumuman insititusion. Draft (belum publish) hanya boleh
 * dilihat admin. Guru & wali hanya melihat surat yang sudah dipublikasikan.
 */
class LetterPolicy
{
    public function view(User $user, Letter $letter): bool
    {
        return $user->isAdmin() || $letter->is_published;
    }
}
