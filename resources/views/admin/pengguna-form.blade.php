@extends('layouts.admin')
@section('title', 'Form Pelanggan')

@php
  $edit = !request()->is('admin/pengguna/tambah');
  $in = 'w-full bg-[#EDF0F5] text-[#2F5B8C] placeholder-[#4F8DB8] rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-light';
@endphp

@section('heading')
  <h1 class="text-3xl text-brand">
    @if ($edit)
      <span class="font-semibold">Ubah Data</span> <span class="font-light">Pelanggan</span>
    @else
      <span class="font-semibold">Tambah</span> <span class="font-light">Pelanggan</span>
    @endif
  </h1>
@endsection

@section('content')
<a href="/admin/pengguna" title="Kembali"
   class="w-7 h-7 rounded-full border border-brand-light text-brand flex items-center justify-center hover:bg-brand-soft">
  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
</a>

<form id="form" onsubmit="return false" novalidate class="mt-4 max-w-4xl">
  <div class="border border-brand-light/50 rounded-2xl p-6 md:p-8">

    {{-- Baris atas sesuai desain Figma: kiri nama + telepon, kanan alamat --}}
    <div class="grid md:grid-cols-2 gap-x-10 gap-y-5">
      <div class="space-y-5">
        <div>
          <label for="nama_pengguna" class="block text-xs font-semibold text-brand mb-2">Nama Pelanggan</label>
          <input id="nama_pengguna" autocomplete="off" placeholder="Masukkan nama pelanggan" class="{{ $in }}">
        </div>

        <div>
          <label for="no_telpon" class="block text-xs font-semibold text-brand mb-2">Nomor Telepon</label>
          <div class="flex items-center bg-[#EDF0F5] rounded-lg focus-within:ring-2 focus-within:ring-brand-light">
            <span class="px-3 text-xs font-semibold text-brand border-r border-slate-300">+62</span>
            <input id="no_telpon" type="tel" inputmode="numeric" autocomplete="off" placeholder="Nomor telepon"
              class="w-full bg-transparent text-[#2F5B8C] placeholder-[#4F8DB8] px-3 py-2.5 text-xs focus:outline-none">
          </div>
        </div>
      </div>

      <div class="flex flex-col">
        <label for="alamat" class="block text-xs font-semibold text-brand mb-2">Alamat</label>
        <textarea id="alamat" placeholder="Masukkan alamat pelanggan" class="{{ $in }} flex-1 min-h-[7rem] resize-none"></textarea>
      </div>
    </div>

  </div>

  <p id="error" class="hidden mt-4 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" role="alert"></p>

  <div class="mt-5 flex justify-end">
    <button type="submit" id="btn" class="bg-brand text-white text-xs px-6 py-2.5 rounded-lg hover:bg-brand-dark disabled:opacity-60">
      {{ $edit ? 'Ubah Data' : 'Simpan Data' }}
    </button>
  </div>
</form>
@endsection

@push('scripts')
<script>
const $ = (id) => document.getElementById(id);
const cocok = location.pathname.match(/\/pengguna\/(\d+)\/ubah/);
const editId = cocok ? cocok[1] : null;

let usernameLama = ''; // dipertahankan saat mengubah data

// Username & password tidak ada di form: dibuat otomatis saat tambah pelanggan.
// Username dari nomor telepon; password acak (pelanggan tidak login lewat akun ini).
function buatPasswordAcak() {
  const h = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
  const a = new Uint32Array(16);
  crypto.getRandomValues(a);
  return Array.from(a, n => h[n % h.length]).join('');
}

// Nomor telepon: buang +62 / 62 / 0 di depan, sisakan angkanya saja
const bersihkanNomor = (s) => String(s || '').replace(/\D/g, '').replace(/^62/, '').replace(/^0+/, '');

(async () => {
  if (!editId) return;
  try {
    const r = await api('/admin/pengguna/' + editId);
    const x = (r && r.data) ? r.data : r; // dukung respons dibungkus {data} maupun langsung
    $('nama_pengguna').value = x.nama_pengguna || '';
    $('no_telpon').value = bersihkanNomor(x.no_telpon);
    usernameLama = x.username || '';
    $('alamat').value = x.alamat || '';
  } catch (e) {
    showError($('error'), e);
    $('btn').disabled = true;
  }
})();

$('form').addEventListener('submit', async (e) => {
  e.preventDefault();
  hideError($('error'));

  const nomor = bersihkanNomor($('no_telpon').value);
  const body = {
    nama_pengguna: $('nama_pengguna').value.trim(),
    no_telpon: nomor ? '+62' + nomor : '',
    alamat: $('alamat').value.trim(),
  };

  if (!body.nama_pengguna || !body.no_telpon || !body.alamat) {
    showError($('error'), new Error('Nama, nomor telepon, dan alamat wajib diisi.'));
    return;
  }
  if (editId) {
    body.username = usernameLama; // tidak diubah
  } else {
    body.username = 'p' + nomor;
    body.password = buatPasswordAcak();
    body.role = 'pelanggan';
  }

  $('btn').disabled = true;
  try {
    await api(editId ? `/admin/pengguna/${editId}` : '/admin/pengguna', {
      method: editId ? 'PUT' : 'POST',
      body,
    });
    location.href = '/admin/pengguna';
  } catch (ex) {
    showError($('error'), ex);
    $('btn').disabled = false;
  }
});
</script>
@endpush