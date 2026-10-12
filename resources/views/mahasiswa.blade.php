<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
</head>
<body>
    <h1>Data Mahasiswa</h1>
    <p>Database: praktikum_web_2401020105</p>

    @if(isset($error))
        <p style="color:red;">{{ $error }}</p>
    @endif

    <form action="/mahasiswa" method="GET"
          onsubmit="if(this.q.value.trim()) window.location='/mahasiswa/'+this.q.value.trim(); return false;">
        <input type="text" name="q" placeholder="Cari berdasarkan NIM..." value="{{ $nim ?? '' }}">
        <button type="submit">Cari</button>
        @if($nim)
            <a href="/mahasiswa">Reset</a>
        @endif
    </form>

    <br>

    @if($nim)
        <p>Hasil pencarian NIM: <strong>{{ $nim }}</strong> ({{ count($mahasiswa) }} ditemukan)</p>
    @else
        <p>Menampilkan seluruh data mahasiswa ({{ count($mahasiswa) }} data)</p>
    @endif

    @if(count($mahasiswa) > 0)
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Usia</th>
                    <th>Program Studi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswa as $mhs)
                    <tr>
                        <td>{{ $mhs['nim'] }}</td>
                        <td>{{ $mhs['nama'] }}</td>
                        <td>{{ $mhs['email'] }}</td>
                        <td>{{ $mhs['usia'] }}</td>
                        <td>{{ $mhs['nama_prodi'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        @if($nim)
            <p>Tidak ditemukan mahasiswa dengan NIM <strong>{{ $nim }}</strong></p>
        @else
            <p>Belum ada data mahasiswa di database</p>
        @endif
    @endif
</body>
</html>
