@extends('layouts.app')

@section('content')
<x-page-header
    title="Log Aktivitas"
    subtitle="Catatan seluruh aktivitas pengguna dalam sistem"
    icon="history"
/>

<div class="mb-lg bg-surface-container-low rounded-xl p-md border border-outline-variant flex flex-wrap gap-md items-center">
    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-wrap gap-md items-end w-full">
        <div>
            <label class="text-caption text-on-surface-variant block mb-xs">ACTION</label>
            <select name="action" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md" onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="login" @selected(request('action') === 'login')>Login</option>
                <option value="logout" @selected(request('action') === 'logout')>Logout</option>
                <option value="create" @selected(request('action') === 'create')>Create</option>
                <option value="update" @selected(request('action') === 'update')>Update</option>
                <option value="delete" @selected(request('action') === 'delete')>Delete</option>
            </select>
        </div>
        <div>
            <label class="text-caption text-on-surface-variant block mb-xs">USER</label>
            <select name="user_id" class="rounded-lg border-outline-variant bg-surface-bright text-on-surface py-2 px-3 text-body-md" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach(\App\Models\User::orderBy('name')->get() as $u)
                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }} ({{ $u->role }})</option>
                @endforeach
            </select>
        </div>
        <x-button variant="primary" type="submit" icon="search">Filter</x-button>
        @if(request()->anyFilled(['action', 'user_id']))
        <a href="{{ route('admin.activity-logs.index') }}" class="text-label-md text-primary hover:underline">Reset</a>
        @endif
    </form>
</div>

<x-card variant="default" class="overflow-x-auto">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="px-md py-4 text-label-md text-on-surface-variant">WAKTU</th>
                    <th class="px-md py-4 text-label-md text-on-surface-variant">USER</th>
                    <th class="px-md py-4 text-label-md text-on-surface-variant">ACTION</th>
                    <th class="px-md py-4 text-label-md text-on-surface-variant">DESKRIPSI</th>
                    <th class="px-md py-4 text-label-md text-on-surface-variant">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($logs as $log)
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-md py-4 text-body-md text-on-surface tabular-nums whitespace-nowrap">{{ $log->created_at->format('d M H:i') }}</td>
                    <td class="px-md py-4">
                        <div class="flex items-center gap-sm">
                            <div class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-label-md text-on-surface-variant font-bold">
                                {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-body-md text-on-surface">{{ $log->user?->name ?? 'System' }}</p>
                                <p class="text-caption text-on-surface-variant">{{ $log->user?->role ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-md py-4">
                        @php
                        $colors = [
                            'login' => 'bg-blue-100 text-blue-700',
                            'logout' => 'bg-gray-100 text-gray-700',
                            'create' => 'bg-green-100 text-green-700',
                            'update' => 'bg-amber-100 text-amber-700',
                            'delete' => 'bg-red-100 text-red-700',
                        ];
                        $labels = [
                            'login' => 'Login',
                            'logout' => 'Logout',
                            'create' => 'Tambah',
                            'update' => 'Ubah',
                            'delete' => 'Hapus',
                        ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $colors[$log->action] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $labels[$log->action] ?? $log->action }}
                        </span>
                    </td>
                    <td class="px-md py-4 text-body-md text-on-surface">{{ $log->description }}</td>
                    <td class="px-md py-4 text-body-md text-on-surface-variant font-mono text-xs">{{ $log->ip_address ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-md py-xl text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-md block">history</span>
                        <p>Belum ada aktivitas tercatat.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

<div class="mt-lg">
    {{ $logs->links() }}
</div>
@endsection
