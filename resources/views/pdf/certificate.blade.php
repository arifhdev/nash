<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $certificate->user->name }}</title>
    <style>
        /* Pengaturan ukuran kertas A4 Landscape */
        @page {
            margin: 0; /* Margin 0 agar background full edge-to-edge */
            size: A4 landscape;
        }
        body {
            margin: 0;
            padding: 0;
            /* WAJIB: Pastikan file certificate-bg.jpg ada di folder public/images/ */
            background-image: url('{{ public_path("images/certificate-bg.jpg") }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
        }
        
        /* Container utama untuk teks, dipaskan ke tengah */
        .content {
            text-align: center;
            padding-top: 140px; /* Jarak dari atas kertas ke teks SERTIFIKAT */
            width: 100%;
        }

        .title {
            font-size: 55px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 5px;
            /* Font khusus serif untuk judul agak lebih elegan jika didukung */
            font-family: 'Times New Roman', Times, serif; 
        }

        .no-reg {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 50px;
        }

        .name {
            font-size: 38px;
            font-weight: normal; /* Sesuai gambar, nama tidak terlalu bold tebal */
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 35px;
        }

        /* Container untuk tabel biodata agar bisa ke tengah */
        .biodata-container {
            width: 100%;
        }

        .biodata {
            margin: 0 auto;
            text-align: left;
            font-size: 16px;
        }

        .biodata td {
            padding: 6px 10px;
        }

        .telah-mengikuti {
            margin-top: 40px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .course-name {
            font-size: 26px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 35px;
        }

        .date {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .division-head {
            font-size: 14px;
        }

        .signature-name {
            margin-top: 80px; /* Ruang kosong untuk stempel / tanda tangan */
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="content">
        <div class="title">SERTIFIKAT</div>
        
        <div class="no-reg">
            {{ $certificate->certificate_number }}
        </div>

        <div class="name">
            {{ $certificate->user->name }}
        </div>

        <div class="biodata-container">
            <table class="biodata">
                <tr>
                    <td style="width: 130px;">HONDA ID</td>
                    <td style="width: 10px;">:</td>
                    <td>{{ $certificate->user->honda_id ?? '-' }}</td>
                </tr>
                <tr>
                    <td>MAIN DEALER</td>
                    <td>:</td>
                    <td>{{ $certificate->user->mainDealer ? strtoupper($certificate->user->mainDealer->name) : '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="telah-mengikuti">TELAH MENGIKUTI PELATIHAN</div>

        <div class="course-name">
            {{ $certificate->course->title }}
        </div>

        <div class="date">
            {{ \Carbon\Carbon::parse($certificate->issued_at)->translatedFormat('d-F-Y') }}
        </div>

        <div class="division-head">
            HONDA CUSTOMER CARE CENTER DIVISION HEAD
        </div>

        <div class="signature-name">
            TTD IQBAL GANTENG
        </div>
    </div>

</body>
</html>