<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Digital - YPP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Mirroring kamera agar seperti cermin */
        #webcam {
            transform: scaleX(-1);
        }

        /* Animasi loading pulse kustom */
        .gps-pulse {
            animation: gpsPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes gpsPulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 font-sans text-gray-800 antialiased flex flex-col min-h-screen relative">

    @include('includes.components.header')

    <main class="flex-1 max-w-3xl mx-auto w-full px-4 py-6 sm:py-8 lg:px-8 space-y-6">

        <div
            class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div>
                <h2 id="live-clock" class="text-4xl font-extrabold text-gray-900 tracking-tight font-mono">00:00:00</h2>
                <p id="live-date" class="text-sm font-medium text-gray-500 mt-1 uppercase tracking-wider">Memuat
                    tanggal...</p>
            </div>
            <div class="flex flex-col items-center sm:items-end gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status Hari Ini</span>

                @if (!$attendance)
                    <span
                        class="bg-rose-100 text-rose-700 ring-1 ring-rose-600/20 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm">
                        Belum Absen Masuk
                    </span>
                    <input type="hidden" id="tipe_absen" value="masuk">
                @elseif($attendance->check_in && !$attendance->check_out)
                    <span
                        class="bg-amber-100 text-amber-700 ring-1 ring-amber-600/20 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm">
                        Sudah Masuk ({{ substr($attendance->check_in, 0, 5) }})
                    </span>
                    <input type="hidden" id="tipe_absen" value="pulang">
                @else
                    <span
                        class="bg-emerald-100 text-emerald-800 ring-1 ring-emerald-600/20 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm flex items-center gap-1.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i> Presensi Selesai
                    </span>
                    <input type="hidden" id="tipe_absen" value="selesai">
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <div
                class="relative w-full aspect-[4/3] sm:aspect-video bg-slate-900 flex items-center justify-center group">
                <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas id="canvas" class="hidden"></canvas>
                <img id="photo-preview" class="hidden w-full h-full object-cover absolute top-0 left-0 z-10">

                <div class="absolute inset-0 pointer-events-none border-[6px] border-black/10 z-20"></div>
                <div
                    class="absolute inset-0 m-auto w-48 h-48 sm:w-64 sm:h-64 border-2 border-white/30 rounded-full pointer-events-none z-20 flex items-center justify-center">
                    <div class="w-1 h-1 bg-amber-500 rounded-full animate-ping"></div>
                </div>
            </div>

            <div class="p-5 sm:p-6 space-y-5">

                <div class="flex items-start gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                    <div
                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                    </div>
                    <div class="w-full overflow-hidden">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Satelit GPS
                            Aktif</p>
                        <p id="location-coords" class="text-xs font-mono text-gray-500 mb-1">Mendeteksi koordinat...</p>
                        <div id="status-geofence"
                            class="text-sm font-semibold text-amber-600 flex items-center gap-1.5 gps-pulse">
                            <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i> Mencari nama lokasi...
                        </div>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <button id="btn-capture" onclick="takeSnapshot()"
                        class="w-full bg-white hover:bg-gray-50 text-gray-800 border-2 border-gray-200 font-bold py-3.5 px-4 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer focus:ring-4 focus:ring-gray-100">
                        <i data-lucide="camera" class="w-5 h-5 text-gray-500"></i> Jepret Foto Selfie
                    </button>

                    <button id="btn-submit" onclick="konfirmasiAbsen()" disabled
                        class="w-full bg-gray-200 text-gray-400 font-bold py-3.5 px-4 rounded-xl transition-all flex items-center justify-center gap-2 cursor-not-allowed">
                        <i data-lucide="fingerprint" class="w-5 h-5"></i>
                        <span id="text-submit-btn">
                            @if (!$attendance)
                                Kirim Absen Masuk
                            @elseif($attendance->check_in && !$attendance->check_out)
                                Kirim Absen Pulang
                            @else
                                Anda Sudah Selesai Absen
                            @endif
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center pt-2 pb-8">
            <a href="{{ route('leave-request.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-amber-600 font-semibold transition-colors">
                <i data-lucide="calendar-off" class="w-4 h-4"></i> Berhalangan Hadir? Ajukan Izin / Cuti
            </a>
        </div>

    </main>

    @include('includes.components.navbar')

    {{-- ========================================================= --}}
    {{-- MODAL KONFIRMASI ABSEN                                    --}}
    {{-- ========================================================= --}}
    <div id="confirm-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 ease-in-out opacity-0 translate-y-4">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('confirm-modal')"></div>
        <div
            class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 z-10 text-center transform scale-95 transition-all duration-300 ease-in-out">
            <div
                class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto ring-8 ring-amber-50/50 mb-4">
                <i data-lucide="fingerprint" class="w-8 h-8 text-amber-600"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 mb-1">Konfirmasi Presensi</h4>
            <p class="text-sm text-gray-500 mb-6">Apakah foto selfie dan lokasi GPS Anda sudah sesuai? Tindakan ini akan
                menyimpan data kehadiran Anda.</p>

            <div id="area-laporan" class="hidden mb-6 text-left">
                <label for="note_out"
                    class="block text-xs font-bold text-gray-700 uppercase mb-1.5 flex items-center justify-between">
                    <span>Laporan Pekerjaan Hari Ini</span>
                    <span class="text-rose-500 text-[10px]">*Wajib</span>
                </label>
                <textarea id="note_out" rows="3" placeholder="Ceritakan singkat apa saja yang Anda kerjakan hari ini..."
                    class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all resize-none"></textarea>
                <p id="error-note" class="text-[11px] text-rose-500 font-medium mt-1.5 hidden flex items-center gap-1">
                    <i data-lucide="alert-circle" class="w-3 h-3"></i> Laporan pekerjaan tidak boleh kosong!
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="closeModal('confirm-modal')"
                    class="w-full bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer">Batal</button>
                <button type="button" onclick="submitAttendance()"
                    class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm">Ya,
                    Kirim</button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL SUCCESS / ERROR NOTIFICATION                        --}}
    {{-- ========================================================= --}}
    <div id="notif-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300 ease-in-out opacity-0 translate-y-4">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('notif-modal')"></div>
        <div
            class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 z-10 text-center transform scale-95 transition-all duration-300 ease-in-out">
            <div id="notif-icon-bg"
                class="w-16 h-16 rounded-full flex items-center justify-center mx-auto ring-8 mb-4">
                <i id="notif-icon" data-lucide="check" class="w-8 h-8"></i>
            </div>
            <h4 id="notif-title" class="text-lg font-bold text-gray-900 mb-1">Berhasil</h4>
            <p id="notif-message" class="text-sm text-gray-500 mb-6">Pesan.</p>
            <button type="button" id="notif-btn" onclick="closeAndReload()"
                class="w-full text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm">
                Tutup
            </button>
        </div>
    </div>

    <script>
        lucide.createIcons();

        let userLat = null,
            userLong = null,
            imageBlobData = null,
            isSuccess = false;

        const webcamElement = document.getElementById('webcam'),
            canvasElement = document.getElementById('canvas'),
            photoPreview = document.getElementById('photo-preview');
        const coordsText = document.getElementById('location-coords'),
            geofenceStatus = document.getElementById('status-geofence'),
            btnSubmit = document.getElementById('btn-submit');

        // Jam Live WIB
        function updateClock() {
            const now = new Date();
            document.getElementById('live-clock').textContent = now.toLocaleTimeString('id-ID', {
                hour12: false,
                timeZone: 'Asia/Jakarta'
            });
            document.getElementById('live-date').textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                timeZone: 'Asia/Jakarta'
            });
        }
        setInterval(updateClock, 1000);

        // Setup Kamera
        async function setupWebcam() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "user"
                    },
                    audio: false
                });
                webcamElement.srcObject = stream;
            } catch (err) {
                showNotifModal('error', 'Kamera Error',
                    'Sistem tidak dapat mengakses kamera Anda. Pastikan izin kamera sudah diberikan.');
            }
        }

        function takeSnapshot() {
            const context = canvasElement.getContext('2d');
            canvasElement.width = webcamElement.videoWidth;
            canvasElement.height = webcamElement.videoHeight;
            context.drawImage(webcamElement, 0, 0, canvasElement.width, canvasElement.height);
            imageBlobData = canvasElement.toDataURL('image/jpeg');

            photoPreview.src = imageBlobData;
            photoPreview.classList.remove('hidden');

            // Ubah tombol jepret jadi "Ulangi"
            document.getElementById('btn-capture').innerHTML =
                '<i data-lucide="refresh-ccw" class="w-5 h-5 text-gray-500"></i> Ulangi Foto';
            lucide.createIcons();

            validateAttendanceRules();
        }

        // Setup Lokasi
        function getLiveLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    userLat = position.coords.latitude;
                    userLong = position.coords.longitude;
                    coordsText.textContent = `${userLat.toFixed(6)}, ${userLong.toFixed(6)}`;
                    geofenceStatus.classList.remove('gps-pulse');

                    fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${userLat}&lon=${userLong}&zoom=18&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            const alamat = (data.address.road || "") + (data.address.road && data.address
                                .city_district ? ", " : "") + (data.address.city_district || "");
                            geofenceStatus.className =
                                "text-sm font-semibold text-emerald-600 flex items-center gap-1.5";
                            geofenceStatus.innerHTML =
                                `<i data-lucide="check-circle-2" class="w-4 h-4"></i> ${alamat || 'Lokasi Terkunci'}`;
                            lucide.createIcons();
                        }).catch(() => {
                            geofenceStatus.className =
                                "text-sm font-semibold text-emerald-600 flex items-center gap-1.5";
                            geofenceStatus.innerHTML =
                                `<i data-lucide="check-circle-2" class="w-4 h-4"></i> Lokasi GPS Terkunci`;
                            lucide.createIcons();
                        });

                    validateAttendanceRules();
                }, () => {
                    coordsText.textContent = "GPS Diblokir";
                    geofenceStatus.classList.remove('gps-pulse');
                    geofenceStatus.className = "text-sm font-semibold text-rose-600 flex items-center gap-1.5";
                    geofenceStatus.innerHTML =
                        `<i data-lucide="alert-triangle" class="w-4 h-4"></i> Gagal Melacak Lokasi`;
                    lucide.createIcons();
                    showNotifModal('error', 'Akses Lokasi Ditolak',
                        'Mohon izinkan akses lokasi (GPS) pada browser Anda untuk melakukan absensi.');
                }, {
                    enableHighAccuracy: true
                });
            }
        }

        // Validasi Tombol
        function validateAttendanceRules() {
            const tipeAbsen = document.getElementById('tipe_absen').value;
            // Aktifkan jika foto ada, GPS ada, dan status BUKAN selesai
            if (imageBlobData !== null && userLat !== null && tipeAbsen !== 'selesai') {
                btnSubmit.disabled = false;
                btnSubmit.className =
                    "w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer ring-4 ring-amber-500/20";
            }
        }

        // ==========================================
        // SISTEM MODAL
        // ==========================================
        function konfirmasiAbsen() {
            const tipeAbsen = document.getElementById('tipe_absen').value;
            const areaLaporan = document.getElementById('area-laporan');
            const inputLaporan = document.getElementById('note_out');

            // Reset error state
            document.getElementById('error-note').classList.add('hidden');
            inputLaporan.classList.remove('border-rose-500', 'ring-rose-500/20', 'ring-2');

            // Logika memunculkan textarea
            if (tipeAbsen === 'pulang') {
                areaLaporan.classList.remove('hidden');
                inputLaporan.value = ''; // Kosongkan isian sebelumnya (jika ada)
            } else {
                areaLaporan.classList.add('hidden');
            }

            openModal('confirm-modal');
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            const box = modal.querySelector('div.bg-white');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'translate-y-4');
                modal.classList.add('opacity-100', 'translate-y-0');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }, 10);
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            const box = modal.querySelector('div.bg-white');
            modal.classList.remove('opacity-100', 'translate-y-0');
            modal.classList.add('opacity-0', 'translate-y-4');
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function showNotifModal(type, title, message) {
            const bg = document.getElementById('notif-icon-bg');
            const icon = document.getElementById('notif-icon');
            const btn = document.getElementById('notif-btn');

            document.getElementById('notif-title').innerText = title;
            document.getElementById('notif-message').innerText = message;

            if (type === 'success') {
                isSuccess = true;
                bg.className =
                    "w-16 h-16 rounded-full flex items-center justify-center mx-auto ring-8 ring-emerald-50/50 bg-emerald-50 mb-4";
                icon.className = "w-8 h-8 text-emerald-600";
                icon.setAttribute('data-lucide', 'check-circle-2');
                btn.className =
                    "w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm";
            } else {
                isSuccess = false;
                bg.className =
                    "w-16 h-16 rounded-full flex items-center justify-center mx-auto ring-8 ring-rose-50/50 bg-rose-50 mb-4";
                icon.className = "w-8 h-8 text-rose-600";
                icon.setAttribute('data-lucide', 'alert-triangle');
                btn.className =
                    "w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors cursor-pointer shadow-sm";
            }
            lucide.createIcons();
            openModal('notif-modal');
        }

        function closeAndReload() {
            closeModal('notif-modal');
            if (isSuccess) window.location.reload();
        }

       function submitAttendance() {
            const tipeAbsen = document.getElementById('tipe_absen').value;
            let noteOutValue = '';

            // Validasi khusus absen pulang
            if (tipeAbsen === 'pulang') {
                const inputLaporan = document.getElementById('note_out');
                noteOutValue = inputLaporan.value.trim();

                // Cegah submit kalau laporannya kosong
                if (!noteOutValue) {
                    document.getElementById('error-note').classList.remove('hidden');
                    document.getElementById('error-note').classList.add('flex');
                    inputLaporan.classList.add('border-rose-500', 'ring-rose-500/20', 'ring-2');
                    inputLaporan.focus();
                    return; // Stop eksekusi, biarkan modal tetap terbuka
                }
            }

            // Tutup modal konfirmasi
            closeModal('confirm-modal');
            
            // Ubah tombol jadi loading
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Sedang Mengirim...`;
            btnSubmit.className = "w-full bg-gray-400 text-white font-bold py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 cursor-wait";
            lucide.createIcons();

            const dataForm = new FormData();
            dataForm.append('latitude', userLat);
            dataForm.append('longitude', userLong);
            dataForm.append('image', imageBlobData);
            dataForm.append('tipe_absen', tipeAbsen);
            
            // Jika pulang, sertakan laporannya ke form data
            if (tipeAbsen === 'pulang') {
                dataForm.append('note_out', noteOutValue);
            }

            fetch("{{ route('attendance.store') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
                body: dataForm
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'sukses') {
                    showNotifModal('success', 'Presensi Berhasil!', data.pesan);
                } else {
                    showNotifModal('error', 'Gagal', data.pesan || 'Terjadi kesalahan sistem.');
                    validateAttendanceRules();
                    btnSubmit.innerHTML = `<i data-lucide="fingerprint" class="w-5 h-5"></i> Coba Lagi`;
                    lucide.createIcons();
                }
            }).catch(() => {
                showNotifModal('error', 'Koneksi Terputus', 'Gagal menghubungi server. Periksa koneksi internet Anda.');
                validateAttendanceRules();
            });
        }
        
        window.onload = () => {
            setupWebcam();
            getLiveLocation();
            updateClock();
        };
    </script>
</body>

</html>
