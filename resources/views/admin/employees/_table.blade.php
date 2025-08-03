<table class="table table-bordered align-middle table-hover">
  <thead class="table-light">
    <tr>
      <th>Nama</th>
      <th>Email</th>
      <th>Role</th>
      <th>Tanggal Dibuat</th>
      <th style="width: 140px;">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($employees as $employee)
      <tr>
        <td>
          {{ $employee->name }}
          @if ($employee->id === auth()->id())
            <span class="badge bg-info text-dark ms-1">Akun Anda</span>
          @endif
        </td>
        <td>{{ $employee->email }}</td>
        <td>
          <span class="badge {{ $employee->role === 'admin' ? 'bg-secondary' : 'bg-success' }}">
            {{ ucfirst($employee->role) }}
          </span>
        </td>
        <td>{{ $employee->created_at->format('d/m/Y') }}</td>
        <td>
          @if ($employee->role === 'user')
            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Hapus</button>
            </form>
          @else
            <span class="text-muted">-</span>
          @endif
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="5" class="text-center text-muted">Data karyawan tidak ditemukan.</td>
      </tr>
    @endforelse
  </tbody>
</table>

@if ($employees->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $employees->links() }}
  </div>
@endif
