<div class="policy-content">
    <h2><span class="iconify" data-icon="solar:shield-bold-duotone"></span>Pasal 1 - Pendahuluan dan Komitmen Keamanan</h2>
    <p>Kebijakan Keamanan Data ini merupakan dokumen resmi yang menguraikan langkah-langkah, standar, dan prosedur keamanan yang diterapkan oleh PT VersaMorph Teknologi Indonesia ("Perusahaan") untuk melindungi data dan sistem yang digunakan dalam pengoperasian Platform StreamDonate. Perusahaan berkomitmen untuk menyediakan infrastruktur keamanan yang robust dan terkini untuk melindungi seluruh informasi dan aset digital yang berada dalam pengawasan kami.</p>

    <h2><span class="iconify" data-icon="solar:lock-bold-duotone"></span>Pasal 2 - Standar Enkripsi</h2>
    
    <h3>2.1 Enkripsi Data</h3>
    <p>Perusahaan menerapkan standar enkripsi tertinggi untuk melindungi data sensitif:</p>
    <ul>
        <li><strong>TLS (Transport Layer Security):</strong> Seluruh komunikasi antara Pengguna dan Platform dilindungi dengan protokol TLS 1.3, memastikan bahwa data yang ditransmisikan tidak dapat disadap atau dimanipulasi oleh pihak yang tidak berwenang;</li>
        <li><strong>Enkripsi End-to-End:</strong> Untuk channel komunikasi sensitif, kami menerapkan enkripsi end-to-end yang memastikan hanya pengirim dan penerima yang dapat membaca isi pesan;</li>
        <li><strong>AES-256:</strong> Seluruh data at rest (data yang disimpan) dienkripsi menggunakan standar AES-256 (Advanced Encryption Standard) yang merupakan standar enkripsi yang diakui secara internasional.</li>
    </ul>

    <h3>2.2 Manajemen Kunci Enkripsi</h3>
    <p>Kunci enkripsi dikelola dengan standar keamanan tertinggi:</p>
    <ul>
        <li>Kunci enkripsi disimpan secara terpisah dari data yang dienkripsi;</li>
        <li>Penerapan prinsip least privilege untuk akses kunci enkripsi;</li>
        <li>Rotasi kunci secara berkala sesuai dengan best practice keamanan;</li>
        <li>Penyimpanan kunci di Hardware Security Module (HSM) yang tersertifikasi.</li>
    </ul>

    <h2><span class="iconify" data-icon="solar:server-bold-duotone"></span>Pasal 3 - Infrastruktur Keamanan</h2>
    
    <h3>3.1 Arsitektur Keamanan</h3>
    <p>Infrastruktur Platform dibangun dengan prinsip security-by-design:</p>
    <ul>
        <li><strong>DMZ (Demilitarized Zone):</strong> Penggunaan DMZ untuk memisahkan jaringan publik dari sistem internal;</li>
        <li><strong>Firewall:</strong> Penerapan multiple layer firewall (network firewall dan web application firewall) untuk melindungi against ancaman eksternal;</li>
        <li><strong>Load Balancer:</strong> Distribusi traffic yang aman untuk mencegah serangan DDoS dan memastikan ketersediaan layanan;</li>
        <li><strong>Private Network:</strong> Komunikasi internal antar komponen sistem menggunakan jaringan privat yang terenkripsi.</li>
    </ul>

    <h3>3.2 Monitoring dan Logging</h3>
    <p>Sistem monitoring keamanan beroperasi 24/7:</p>
    <ul>
        <li><strong>SIEM (Security Information and Event Management):</strong> Pengumpulan dan analisis log keamanan secara real-time untuk mendeteksi anomali dan ancaman;</li>
        <li><strong>Intrusion Detection System (IDS):</strong> Monitoring terus-menerus untuk mendeteksi aktivitas mencurigakan atau percobaan intrusi;</li>
        <li><strong>Real-time Alerts:</strong> Notifikasi immediately kepada tim keamanan untuk setiap insiden yang terdeteksi.</li>
    </ul>

    <h2><span class="iconify" data-icon="solar:user-check-bold-duotone"></span>Pasal 4 - Kontrol Akses</h2>
    
    <h3>4.1 Prinsip Keamanan Akses</h3>
    <p>Perusahaan menerapkan prinsip keamanan akses berikut:</p>
    <ul>
        <li><strong>Principle of Least Privilege:</strong> Setiap pengguna dan sistem hanya diberikan akses minimum yang diperlukan untuk menjalankan fungsinya;</li>
        <li><strong>Role-Based Access Control (RBAC):</strong> Hak akses ditentukan berdasarkan peran dan tanggung jawab masing-masing;</li>
        <li><strong>Multi-Factor Authentication (MFA):</strong> Wajib untuk semua akses administratif dan akses ke sistem sensitif;</li>
        <li><strong>Session Management:</strong> Batas waktu sesi yang ketat dan mekanisme logout otomatis.</li>
    </ul>

    <h3>4.2 Autentikasi</h3>
    <p>Standar autentikasi yang diterapkan:</p>
    <ul>
        <li>Password harus memenuhi kompleksitas minimum (minimal 8 karakter, kombinasi huruf besar, huruf kecil, angka, dan karakter khusus);</li>
        <li>Penimpanan password menggunakan algoritma hashing yang kuat (bcrypt dengan salt yang unik);</li>
        <li>Rate limiting untuk mencegah brute force attack;</li>
        <li>Verifikasi email dan/atau nomor telepon untuk akun baru.</li>
    </ul>

    <h2><span class="iconify" data-icon="solar:bug-bold-duotone"></span>Pasal 5 - Manajemen Kerentanan</h2>
    
    <h3>5.1 Vulnerability Assessment</h3>
    <p>Perusahaan secara berkala melakukan:</p>
    <ul>
        <li><strong>Penetration Testing:</strong> Simulasi serangan oleh tim keamanan internal dan auditor eksternal minimal dua kali setahun;</li>
        <li><strong>Vulnerability Scanning:</strong> Scanning otomatis untuk mengidentifikasi kerentanan perangkat lunak secara berkelanjutan;</li>
        <li><strong>Code Review:</strong> Review kode sumber secara sistematis untuk mengidentifikasi dan memperbaiki kerentanan sebelum deployment;</li>
        <li><strong>Dependency Check:</strong> Pemindaian terhadap library dan dependency untuk memastikan tidak ada kerentanan yang diketahui.</li>
    </ul>

    <h3>5.2 Patch Management</h3>
    <p>Prosedur manajemen patch:</p>
    <ul>
        <li>Patch keamanan diterapkan dalam waktu 72 jam setelah rilis untuk kerentanan kritis;</li>
        <li>Patch regular diterapkan sesuai jadwal pemeliharaan yang telah ditentukan;</li>
        <li>Testing menyeluruh sebelum penerapan patch di lingkungan produksi;</li>
        <li>Prosedur rollback yang siap jika ditemukan isu setelah penerapan.</li>
    </ul>

    <h2><span class="iconify" data-icon="solar:shield-check-bold-duotone"></span>Pasal 6 - Ketersediaan dan Disaster Recovery</h2>
    
    <h3>6.1 High Availability</h3>
    <p>Infrastruktur dirancang untuk memastikan ketersediaan layanan:</p>
    <ul>
        <li>Arsitektur multi-server dengan load balancing;</li>
        <li>Redundansi di seluruh komponen kritis;</li>
        <li>Automatic failover untuk memastikan kontinuitas layanan;</li>
        <li>Target uptime 99.9% per tahun.</li>
    </ul>

    <h3>6.2 Disaster Recovery</h3>
    <p>Rencana pemulihan bencana meliputi:</p>
    <ul>
        <li><strong>Backup:</strong> Pencadangan data secara berkala ( setiap jam untuk data kritikal, setiap hari untuk data non-kritikal) dengan enkripsi;</li>
        <li><strong>RTO (Recovery Time Objective):</strong> Maksimal 4 jam untuk pemulihan sistem kritikal;</li>
        <li><strong>RPO (Recovery Point Objective):</strong> Maksimal 1 jam kehilangan data untuk sistem kritikal;</li>
        <li><strong>Drill:</strong> Pengujian rencana pemulihan secara berkala untuk memastikan kesiapan.</li>
    </ul>

    <h2><span class="iconify" data-icon="solar:mail-bold-duotone"></span>Pasal 7 - Informasi Kontak</h2>
    <p>Untuk pertanyaan terkait keamanan data, silakan hubungi:</p>
    <ul>
        <li><strong>Email:</strong> security@tiptipan.id</li>
        <li><strong>Telepon:</strong> +62 21 5678 9012</li>
    </ul>

    <div class="policy-info-box">
        <div class="policy-info-box-icon">
            <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
        </div>
        <div class="policy-info-box-content">
            <div class="policy-info-box-title">Komitmen Kami</div>
            <div class="policy-info-box-text">PT VersaMorph Teknologi Indonesia terus memperbarui dan meningkatkan standar keamanan kami untuk melindungi data Anda dari ancaman yang terus berkembang.</div>
        </div>
    </div>
</div>
