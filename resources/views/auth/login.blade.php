@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="landing">
  <div class="landing-bg">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>
  <div class="landing-content">
    <div class="landing-hero">
      <span class="logo-badge">🥛</span>
      <h1 class="landing-title">YogurtLog</h1>
      <p class="landing-subtitle">Platform logbook digital pemantau proyek<br>fermentasi yogurt berbasis sains</p>
    </div>

    <div class="auth-tabs">
      <button class="auth-tab {{ ($tab ?? 'login') === 'login' ? 'active' : '' }}" onclick="switchTab('login')">Masuk</button>
      <button class="auth-tab {{ ($tab ?? 'login') === 'register' ? 'active' : '' }}" onclick="switchTab('register')">Daftar Siswa</button>
    </div>

    {{-- LOGIN --}}
    <div class="auth-card {{ ($tab ?? 'login') === 'register' ? 'hidden' : '' }}" id="form-login">
      <h2>Selamat Datang</h2>
      <p class="auth-desc">Masuk ke akun Anda untuk mengakses logbook</p>

      @if(session('error'))
        <div class="form-error">{{ session('error') }}</div>
      @endif

      <form method="POST" action="/login" novalidate>
        @csrf
        <div class="form-group">
          <label for="login-username">Username</label>
          <input type="text" id="login-username" name="username" value="{{ old('username') }}"
                 placeholder="Masukkan username" required autocomplete="username">
        </div>
        <div class="form-group">
          <label for="login-password">Password</label>
          <div class="input-with-toggle">
            <input type="password" id="login-password" name="password"
                   placeholder="Masukkan password" required autocomplete="current-password">
            <button type="button" class="toggle-pass" onclick="togglePw('login-password')">👁</button>
          </div>
        </div>
        @if($errors->has('login'))
          <div class="form-error">{{ $errors->first('login') }}</div>
        @endif
        <button type="submit" class="btn btn-primary btn-full btn-lg">Masuk</button>
      </form>
      <div class="auth-hint">
        <small>Akun guru: <code>guru</code> / <code>guru123</code></small>
      </div>
    </div>

    {{-- REGISTER --}}
    <div class="auth-card {{ ($tab ?? 'login') === 'login' ? 'hidden' : '' }}" id="form-register">
      <h2>Daftar Akun Siswa</h2>
      <p class="auth-desc">Buat akun untuk mengisi logbook proyek yogurt</p>
      <form method="POST" action="/register" novalidate>
        @csrf
        <div class="form-group">
          <label for="reg-name">Nama Lengkap</label>
          <input type="text" id="reg-name" name="name" value="{{ old('name') }}"
                 placeholder="Masukkan nama lengkap" required>
        </div>
        <div class="form-group">
          <label for="reg-group">Nama Kelompok</label>
          <input type="text" id="reg-group" name="group_name" value="{{ old('group_name') }}"
                 placeholder="Contoh: Kelompok A" required>
        </div>
        <div class="form-group">
          <label for="reg-username">Username</label>
          <input type="text" id="reg-username" name="username" value="{{ old('username') }}"
                 placeholder="Buat username unik (tanpa spasi)" required>
        </div>
        <div class="form-group">
          <label for="reg-password">Password</label>
          <div class="input-with-toggle">
            <input type="password" id="reg-password" name="password"
                   placeholder="Minimal 4 karakter" required>
            <button type="button" class="toggle-pass" onclick="togglePw('reg-password')">👁</button>
          </div>
        </div>
        @if($errors->any())
          <div class="form-error">{{ $errors->first() }}</div>
        @endif
        <button type="submit" class="btn btn-primary btn-full btn-lg">Daftar & Masuk →</button>
      </form>
    </div>

    <div class="landing-features">
      <div class="feature-chip">🔬 Uji Organoleptik</div>
      <div class="feature-chip">📊 Evaluasi Otomatis</div>
      <div class="feature-chip">📸 Upload Foto</div>
      <div class="feature-chip">👩‍🏫 Pemantauan Guru</div>
    </div>
  </div>
</div>
@endsection
