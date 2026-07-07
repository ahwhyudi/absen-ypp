<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Digital - Yayasan Prasasti Perdamaian</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        video {
            transform: scaleX(-1);
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 font-sans text-gray-800 antialiased">
<div class="">

    @include('includes.components.header')
</div>
    <main class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center mb-8">
            <p class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-1">Waktu Kerja WIB</p>
            <h2 id="live-clock" class="text-4xl font-extrabold text-gray-900 tracking-tight">00:00:00</h2>
            <p id="live-date" class="text-sm text-gray-500 mt-1">Hari, 00 Bulan 2026</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="camera" class="w-5 h-5 text-amber-500"></i> Kamera Verifikasi Face Match
                    </h3>
                    <div
                        class="relative w-full aspect-video bg-gray-900 rounded-xl overflow-hidden shadow-inner flex items-center justify-center mb-4 border border-gray-200">
                        <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        <img id="photo-preview" class="hidden w-full h-full object-cover absolute top-0 left-0">
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 flex items-start gap-3">
                        <i data-lucide="map-pin" class="w-5 h-5 text-amber-500 shrink-0 mt-0.5"></i>
                        <div class="w-full">
                            <p class="text-xs font-semibold text-gray-400 uppercase">Koordinat GPS Anda</p>
                            <p id="location-coords" class="text-sm font-mono font-medium text-gray-700 mt-0.5">
                                Mendeteksi Satelit GPS...</p>
                        </div>
                    </div>
                    <div id="status-geofence"
                        class="p-3 bg-amber-50 text-amber-700 rounded-xl border border-amber-100 flex items-center gap-3 text-sm font-medium">
                        <i data-lucide="refresh-cw" class="w-4 h-4 animate-spin"></i>
                        <span>Melacak nama lokasi jalan & kecamatan...</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="file-check-2" class="w-5 h-5 text-amber-500"></i> Validasi Sistem Presensi
                    </h3>
                    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <h4 class="text-sm font-bold text-blue-900 flex items-center gap-2">
                            <i data-lucide="globe" class="w-4 h-4"></i> Sistem Presensi Seluler Fleksibel
                        </h4>
                        <p class="text-xs text-blue-700 mt-1 leading-relaxed">
                            Absensi dapat dilakukan dari mana saja (Fleksibel/WFH). Pastikan GPS Anda aktif agar sistem
                            dapat merekam lokasi Anda.
                        </p>
                    </div>
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Batas Absen Masuk</span>
                            <span class="text-sm font-semibold text-gray-800">08:00 WIB</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">

                            <span class="text-sm text-gray-500">
                                Status Absen Hari Ini
                            </span>

                            @if (!$attendance)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">
                                    Belum Absen
                                </span>
                            @elseif($attendance->check_in && !$attendance->check_out)
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">
                                    Masuk {{ substr($attendance->check_in, 0, 5) }}
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
                                    Pulang {{ substr($attendance->check_out, 0, 5) }}
                                </span>
                            @endif

                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <a href="tambah_cuti.php"
                                class="text-xs text-amber-600 font-bold flex items-center gap-1 hover:text-amber-700 transition-all">
                                <i data-lucide="calendar-days" class="w-4 h-4"></i> Tidak Bisa Hadir? Ajukan Cuti / Izin
                                Sakit Di Sini
                            </a>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <button id="btn-capture" onclick="takeSnapshot()"
                        class="w-full bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-semibold py-3 px-4 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i data-lucide="aperture" class="w-5 h-5 text-gray-500"></i> Ambil Foto / Jepret
                    </button>
                    @if (!$attendance)
                        <input type="hidden" id="tipe_absen" value="masuk">
                    @elseif($attendance->check_in && !$attendance->check_out)
                        <input type="hidden" id="tipe_absen" value="pulang">
                    @else
                        <input type="hidden" id="tipe_absen" value="selesai">
                    @endif

                    <button @if ($attendance && $attendance->check_out) disabled @endif id="btn-submit"
                        onclick="submitAttendance()" disabled
                        class="w-full bg-gray-300 text-gray-400 font-bold py-3 px-4 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 cursor-not-allowed">
                        <i data-lucide="fingerprint" class="w-5 h-5"></i> <span id="text-submit-btn">

                            @if (!$attendance)
                                Absen Masuk
                            @elseif($attendance->check_in && !$attendance->check_out)
                                Absen Pulang
                            @else
                                Absensi Selesai
                            @endif

                        </span></span>
                    </button>
                    <div
                        class="p-4 bg-gray-100 text-gray-500 text-center font-bold text-sm rounded-xl border border-gray-200 flex items-center justify-center gap-2">
                        <i data-lucide="lock" class="w-4 h-4"></i> Anda Sudah Melakukan Absen Masuk & Pulang
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bottom Nav Mobile -->
     @include('includes.components.navbar')
    
    <div class="h-14 sm:hidden"></div><!-- spacer for bottom nav -->

    <script>
        lucide.createIcons();

        let userLat = null,
            userLong = null,
            imageBlobData = null;
        const webcamElement = document.getElementById('webcam'),
            canvasElement = document.getElementById('canvas'),
            photoPreview = document.getElementById('photo-preview');
        const coordsText = document.getElementById('location-coords'),
            geofenceStatus = document.getElementById('status-geofence'),
            btnSubmit = document.getElementById('btn-submit');

        function updateClock() {
            const now = new Date();
            document.getElementById('live-clock').textContent = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
            document.getElementById('live-date').textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        setInterval(updateClock, 1000);

        async function setupWebcam() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                });
                webcamElement.srcObject = stream;
            } catch (err) {
                alert("Akses Kamera Ditolak / Tidak Ditemukan.");
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
            validateAttendanceRules();
        }

        function getLiveLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    userLat = position.coords.latitude;
                    userLong = position.coords.longitude;
                    coordsText.textContent = `${userLat.toFixed(6)}, ${userLong.toFixed(6)}`;

                    fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${userLat}&lon=${userLong}&zoom=18&addressdetails=1`
                        )
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.address) {
                                const jalan = data.address.road || data.address.suburb || "";
                                const kecamatan = data.address.city_district || data.address.county || "";

                                let alamatRingkas = "";
                                if (jalan) alamatRingkas += jalan + ", ";
                                if (kecamatan) alamatRingkas += kecamatan;

                                geofenceStatus.className =
                                    "p-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3 text-sm font-medium";
                                geofenceStatus.innerHTML =
                                    `<i data-lucide="map" class="w-4 h-4 text-emerald-500"></i> Lokasi: ${alamatRingkas}`;
                            } else {
                                geofenceStatus.className =
                                    "p-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3 text-sm font-medium";
                                geofenceStatus.innerHTML =
                                    `<i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i> Lokasi GPS Aktif`;
                            }
                            lucide.createIcons();
                        })
                        .catch(() => {
                            geofenceStatus.className =
                                "p-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3 text-sm font-medium";
                            geofenceStatus.innerHTML =
                                `<i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i> Lokasi GPS Terkunci`;
                            lucide.createIcons();
                        });

                    validateAttendanceRules();
                }, () => {
                    coordsText.textContent = "GPS Mati/Ditolak";
                    geofenceStatus.className =
                        "p-3 bg-rose-50 text-rose-700 rounded-xl border border-rose-100 flex items-center gap-3 text-sm font-medium";
                    geofenceStatus.innerHTML =
                        `<i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i> Gagal Mengunci GPS`;
                    lucide.createIcons();
                }, {
                    enableHighAccuracy: true
                });
            }
        }

        function validateAttendanceRules() {
            if (imageBlobData !== null) {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.className =
                        "w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer";
                }
            }
        }

        function submitAttendance() {
            const tipeAbsen = document.getElementById('tipe_absen').value;
            const label = tipeAbsen === 'pulang' ? 'Absen Pulang' : 'Absen Masuk';
            if (!confirm(`Konfirmasi ${label}\n\nApakah Anda yakin ingin mengirim ${label} sekarang?`)) {
                return;
            }
            const dataForm = new FormData();
            dataForm.append('latitude', userLat);
            dataForm.append('longitude', userLong);
            dataForm.append('image', imageBlobData);
            dataForm.append('tipe_absen', tipeAbsen);

            fetch("{{ route('attendance.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: dataForm
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.pesan);
                    if (data.status === 'sukses') {
                        window.location.reload();
                    }
                }).catch(() => alert("Eror Jaringan Server."));
        }

        window.onload = () => {
            setupWebcam();
            getLiveLocation();
        };
    </script>
</body>

</html>
