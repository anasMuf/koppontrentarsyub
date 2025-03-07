<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Koperasi | Invoice</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
            }
            /* ... the rest of the rules ... */
        }

        *{
            margin: 0;
            padding: 0;
        }

        body{
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: black;
        }
        .header{
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2rem;
        }
        .logo{
            padding-left: 4rem;
            padding-right: 4rem;
        }
        .logo img{
            width: 100px;
        }
        .description{
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .description h3{
            margin: 0.7rem;
        }
        .description p{
            text-align: center;
            margin: 0;
            padding-left: 2rem;
            padding-right: 2rem;
        }
        .contact ul{
            display: flex;
            gap: 1rem;
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .hr-cop{
            margin-top: 1rem;
            height:3px;
            border-top:1px solid black;
            border-bottom:2px solid black;
        }
        .body{
            margin-right: 2rem;
            margin-left: 2rem;
        }
        .body .title{
            margin-top: 2rem;
            text-align: center;
        }
        .profile{
            margin-top: 1rem;
        }
        .profile table{
            width: -webkit-fill-available;
        }
        .content{
            margin-top: 1rem;
        }
        .content table{
            width: -webkit-fill-available;
            border-collapse: collapse;
        }
        .content table tr td, .content table tr th{
            border: 1px solid black;
            padding: 0.5rem
        }
        .content table tr th{
            background-color: gainsboro;
        }
        .content table .number{
            text-align: right
        }
        .content table .text{
            text-align: center
        }
        .ttd{
            display: flex;
            flex-direction: row-reverse;
            width: 100%;
            flex-wrap: wrap;
        }
        .f-jcc{
            justify-content: center;
        }
        .ttd-content{
            margin-top: 3rem;
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .ttd-header{}
        .ttd-body{
            height: 5rem;
        }
        .ttd-footer{}
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ $logo == '' ? asset(config('adminlte.logo_img')) : asset('dist/images/logos/'.$logo) }}" alt="{{ $logo == '' ? asset(config('adminlte.logo_img_alt')) : $nama.' Logo' }}">
        </div>
        <div class="description">
            <h3>{{ $nama == '' ? asset(config('adminlte.logo')) : $nama }}</h3>
            <p>{{ $alamat ?? 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolore dolorem sint qui ipsa reiciendis ut corrupti obcaecati laudantium autem commodi iste laborum accusantium odio pariatur iure, nihil ab corporis sed!' }}</p>
            <div class="contact">
                <ul>
                    <li><strong>telepon</strong>: {{ $telepon }}</li>
                    <li><strong>email</strong>: {{ $email }}</li>
                </ul>
            </div>
        </div>
    </div>
    <hr class="hr-cop">
    <div class="body">
        <h3 class="title"><u>INVOICE</u></h3>
        <div class="profile">
            <table>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $anggota->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td>Tgl Bergabung</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($anggota->tgl_bergabung)->isoFormat('DD MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $anggota->alamat }}</td>
                </tr>
                <tr>
                    <td>No. Telepon</td>
                    <td>:</td>
                    <td>{{ $anggota->no_telepon }}</td>
                </tr>
            </table>
        </div>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Transaksi</th>
                        <th>Total Tagihan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text">{{ 'IURAN '.strtoupper($jenis_iuran->jenis_iuran) }}</td>
                        <td class="number">Rp {{ number_format($data->nominal,0,',','.') }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="ttd">
                <div class="ttd-content">
                    <div class="ttd-header">
                        <label for="">Mengetahui, {{ strtoupper(Auth::user()->roles->pluck('name')[0]) }}</label>
                    </div>
                    <div class="ttd-body"></div>
                    <div class="ttd-footer">
                        <p>({{ Auth::user()->name }})</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.print()
    </script>
</body>
</html>
