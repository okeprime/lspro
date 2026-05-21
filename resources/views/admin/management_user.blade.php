<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Management User - LSPRO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { display: flex; background-color: #f1f5f9; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar { width: 260px; background: #064e3b; height: 100vh; position: fixed; color: white; display: flex; flex-direction: column; }
        .brand { padding: 20px; text-align: center; background: #022c22; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .brand img { width: 60px; margin-bottom: 10px; }
        .nav-links { list-style: none; padding: 20px 0; }
        .nav-links li a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #a7f3d0; text-decoration: none; font-size: 14px; }
        .nav-links li a.active { background: #059669; color: white; border-left: 5px solid #34d399; }

        /* CONTENT */
        .main-content { flex: 1; margin-left: 260px; padding: 30px; }
        .stats-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: white; padding: 20px; border-radius: 12px; border-left: 5px solid #064e3b; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .stat-box small { color: #64748b; font-weight: bold; font-size: 11px; }
        .stat-box h1 { color: #1e293b; margin-top: 5px; }

        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f8fafc; padding: 12px; text-align: left; color: #64748b; font-size: 12px; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .badge { padding: 4px 8px; border-radius: 15px; font-size: 11px; background: #d1fae5; color: #065f46; font-weight: bold; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <img src="{{ asset('kementan.png') }}" alt="Logo">
            <h3>LSPRO</h3>
            <small>KEMENTERIAN PERTANIAN</small>
        </div>
        <ul class="nav-links">
            <li><a href="#"><i class="fa-solid fa-house"></i> Beranda</a></li>
            <li><a href="/management-user" class="active"><i class="fa-solid fa-users-gear"></i> Management User</a></li>
            <li><a href="#"><i class="fa-solid fa-user-circle"></i> Profil</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="stats-container">
            <div class="stat-box">
                <small>Pengajuan Baru</small>
                <h1>{{ $masuk }}</h1>
            </div>
            <div class="stat-box">
                <small>Menunggu Revisi</small>
                <h1>{{ $revisi }}</h1>
            </div>
            <div class="stat-box">
                <small>Total Selesai</small>
                <h1>{{ $selesai }}</h1>
            </div>
        </div>

        <div class="card">
            <h2 style="color: #064e3b;">Daftar Perusahaan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Perusahaan</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td><strong>{{ $user->nama_perusahaan }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge">{{ $user->role }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>