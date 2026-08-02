<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

/**
 * Policy akses data siswa.
 *
 * IDOR guard: memastikan hanya pihak berhak yang bisa melihat/metaphis akses
 * suatu siswa dengan cara menebak ID. Aturan:
 *   - admin: semua siswa
 *   - guru : hanya siswa di kelas yang dia ajar (teacher_subjects -> classroom)
 *   - wali : hanya siswa yang terikat ke akun user tersebut (user_id = user)
 */
class StudentPolicy
{
    /**
     * Determine whether the user can view/access the student.
     */
    public function view(User $user, Student $student): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isGuru()) {
            return $user->teacherSubjects()
                ->where('classroom_id', $student->classroom_id)
                ->exists();
        }

        if ($user->isWaliMurid()) {
            return $student->user_id === $user->id;
        }

        return false;
    }
}
