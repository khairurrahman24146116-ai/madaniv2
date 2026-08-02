<?php

namespace App\Policies;

use App\Models\ActiveLetterRequest;
use App\Models\User;

/**
 * Policy akses surat aktif siswa.
 *
 * Setiap ActiveLetterRequest terikat ke seorang siswa (student_id). Aturan:
 *   - admin: semua
 *   - guru : hanya untuk siswa di kelas yang dia ajar, atau request yang ia ajukan
 *   - wali : hanya untuk anaknya sendiri (student.user_id = user)
 */
class ActiveLetterRequestPolicy
{
    public function view(User $user, ActiveLetterRequest $letter): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isWaliMurid()) {
            return isset($letter->student) && $letter->student->user_id === $user->id;
        }

        if ($user->isGuru()) {
            if ($letter->teacher_id === $user->id) {
                return true;
            }

            if (isset($letter->student)) {
                return $user->teacherSubjects()
                    ->where('classroom_id', $letter->student->classroom_id)
                    ->exists();
            }
        }

        return false;
    }

    public function update(User $user, ActiveLetterRequest $letter): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ActiveLetterRequest $letter): bool
    {
        return $user->isAdmin();
    }
}
