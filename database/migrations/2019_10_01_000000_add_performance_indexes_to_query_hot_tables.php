<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance: menambahkan index untuk kolom yang sering dipakai pada
 * WHERE, COUNT, GROUP BY, dan ORDER BY di tabel yang bertambah cepat
 * (scores, attendances, teacher_attendances, student_fees, activity_logs).
 *
 * Index creation bersifat non-destruktif — tidak menghapus/mengubah data.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Filter & urutkan nilai per semester/tahun ajaran; orderBy created_at.
        Schema::table('scores', function (Blueprint $table) {
            $table->index(['semester', 'academic_year'], 'scores_semester_year_index');
            $table->index('created_at', 'scores_created_at_index');
        });

        // Absensi siswa: filter tanggal, dan urutkan per siswa+ tanggal.
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['date', 'status'], 'attendances_date_status_index');
            $table->index(['student_id', 'date'], 'attendances_student_date_index');
        });

        // Absensi guru: filter/urutkan tanggal dan status.
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->index(['date', 'status'], 'teacher_attendances_date_status_index');
        });

        // SPP: filter status pembayaran.
        Schema::table('student_fees', function (Blueprint $table) {
            $table->index('is_paid', 'student_fees_is_paid_index');
        });

        // Activity log: urutkan created_at desc (latest()).
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at', 'activity_logs_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropIndex('scores_semester_year_index');
            $table->dropIndex('scores_created_at_index');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_date_status_index');
            $table->dropIndex('attendances_student_date_index');
        });

        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->dropIndex('teacher_attendances_date_status_index');
        });

        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropIndex('student_fees_is_paid_index');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_created_at_index');
        });
    }
};
