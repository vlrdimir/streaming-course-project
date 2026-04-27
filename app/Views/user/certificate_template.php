<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - <?= $course['title'] ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
            width: 297mm;
            height: 210mm;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        
        body {
            font-family: Arial, sans-serif;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background-color: #c8f0b6;
            padding: 6mm;
        }
        
        .certificate-wrapper {
            width: 285mm;
            height: 198mm;
            background-color: #ffffff;
            background-image: url("<?= $imagePath ?>certificate-background.png");
            background-size: cover;
            background-position: center;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
            box-shadow: 20px 20px 60px #000000b3, inset -20px -20px 60px #7272723c;
        }
        
        .certificate-header {
            text-align: center;
            position: relative;
            margin: 0;
            display: block;
            padding-top: 12mm;
        }
        
        .logo {
            position: absolute;
            top: 6mm;
            left: 10mm;
            width: 16mm;
            height: 16mm;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
        }
        
        .certificate-title {
            font-family: "Playfair Display", serif;
            font-size: 24pt;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            margin-bottom: 5mm;
            line-height: 1.1;
        }
        
        .certificate-content {
            padding: 8mm 12mm 6mm 12mm;
            text-align: center;
            position: relative;
            margin: 0;
        }
        
        .recipient-label {
            margin-top: 24mm;
            font-size: 11pt;
            color: #1e293b;
            margin-bottom: 4mm;
            font-weight: 400;
        }
        
        .recipient-name {
            font-family: "Playfair Display", serif;
            font-size: 28pt;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 8mm;
            display: inline-block;
            padding-bottom: 1mm;
            text-transform: capitalize;
        }
        
        .achievement-text {
            font-size: 10pt;
            color: #1e293b;
            margin-bottom: 4mm;
            font-weight: 400;
        }
        
        .course-title {
            font-family: "Playfair Display", serif;
            font-size: 16pt;
            font-weight: 600;
            color: #4f46e5;
            margin-bottom: 8mm;
            padding: 0 6mm;
            line-height: 1.2;
        }
        
        .date-completed {
            font-size: 10pt;
            color: #1e293b;
            font-weight: 400;
            margin-bottom: 12mm;
        }
        
        .certificate-footer {
            position: absolute;
            bottom: 34mm;
            left: 12mm;
            right: 12mm;
        }
        
        .signatures {
            width: 100%;
            margin-bottom: 8mm;
            text-align: center;
            display: flex;
            justify-content: space-between;
        }
        
        .signature {
            display: inline-block;
            width: 30%;
            text-align: center;
            vertical-align: top;
        }
        
        .signature img {
            height: 24mm;
          
        }
        
        .signature-label {
            font-size: 8pt;
            color: #1e293b;
            font-weight: 500;
        }
        
        .certificate-number {
            font-size: 10pt;
            color: #1e293b;
            font-weight: 400;
            text-align: center;
            margin-top: 10mm;
        }
        
        /* Critical: Prevent any page breaks */
        .certificate-wrapper,
        .certificate-header,
        .certificate-content,
        .certificate-footer {
            page-break-inside: avoid;
            page-break-before: avoid;
            page-break-after: avoid;
        }
        
        .download {
            text-align: center;
            margin-top: 10mm;
        }
        
        .download button {
            padding: 8px 15px;
            background-color: transparent;
            border: 1px solid white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .download button:hover {
            background-color: rgba(0, 0, 0, 0.92);
            box-shadow: 0 0 30px 5px black;
        }
        
        .download button a {
            text-decoration: none;
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="certificate-header">
            <div class="logo">
                <!--<img src="</?= $imagePath ?>logo.png" alt="Logo">-->
            </div>
            <h1 class="certificate-title">Sertifikat Penyelesaian</h1>
        </div>
        
        <div class="certificate-content">
            <p class="recipient-label">Dengan ini menyatakan bahwa</p>
            <h2 class="recipient-name"><?= $user['name'] ?></h2>
            
            <p class="achievement-text">telah berhasil menyelesaikan kursus</p>
            
            <h3 class="course-title">"<?= $course['title'] ?>"</h3>
            
            <p class="date-completed">
                Diselesaikan pada tanggal <strong><?= $completedDate ?></strong>
            </p>
            
            <div class="certificate-footer">
                <div class="signatures">
                    <div class="signature">
                        <img src="<?= $imagePath ?>director-signature.png" alt="Tanda Tangan Instruktur">
                        <div class="signature-label">Instruktur Kursus</div>
                    </div>
                    
                    <div class="signature">
                        <div class="certificate-number">
                            No. Sertifikat: <?= $certificateNumber ?>
                        </div>
                    </div>
                    
                    <div class="signature">
                        <img src="<?= $imagePath ?>director-signature.png" alt="Tanda Tangan Direktur">
                        <div class="signature-label">Direktur Program</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
 
</body>
</html>