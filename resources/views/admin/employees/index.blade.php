@extends('layouts.app')

@section('title', 'Kelola Karyawan')

@section('content')
<div class="container my-4 px-3 px-md-5">
  <div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h3 class="mb-0">Data Karyawan</h3>
      <a href="{{ route('admin.employees.create') }}" class="btn btn-success">
        + Tambah Karyawan
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success rounded-2">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
      <input type="text" id="searchInput" class="form-control" placeholder="Cari nama atau email...">
    </div>

    <div id="tableContainer">
      @include('admin.employees._table', ['employees' => $employees])
    </div>
    
  </div>
</div>
@endsection

@push('scripts')
<script>
  const input = document.getElementById('searchInput');
  const container = document.getElementById('tableContainer');
  let timer;

  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
      const keyword = input.value.trim();
      const url = `?search=${encodeURIComponent(keyword)}`;

      fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(res => res.text())
      .then(html => {
        container.innerHTML = html;
      })
      .catch(err => console.error(err));
    }, 400);
  });

  document.addEventListener('click', function (e) {
    if (e.target.tagName === 'A' && e.target.closest('.pagination')) {
      e.preventDefault();
      fetch(e.target.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(res => res.text())
      .then(html => {
        container.innerHTML = html;
        window.history.pushState({}, '', e.target.href);
      });
    }
  });
</script>
@endpush
