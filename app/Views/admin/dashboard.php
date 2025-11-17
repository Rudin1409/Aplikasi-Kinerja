<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Selamat Datang, <?= session()->get('nama_lengkap') ?>!</h1>
        <p class="text-gray-600">Berikut adalah ringkasan data dari sistem APKP Polsri.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 <?= (session()->get('role') == 'admin') ? 'lg:grid-cols-4' : 'lg:grid-cols-3' ?> gap-6">
        
        <a href="<?= base_url('admin/jurusan-capaian') ?>" 
           class="block bg-white p-6 rounded-lg shadow-md border-l-4 border-purple-500 
                  transition duration-300 hover:shadow-xl hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Jurusan</p>
                    <span class="text-3xl font-bold text-gray-800">10</span>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <ion-icon name="business-outline" class="text-3xl text-purple-600"></ion-icon>
                </div>
            </div>
        </a>

        <a href="<?= base_url('admin/prodi') ?>"
           class="block bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500
                  transition duration-300 hover:shadow-xl hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Program Studi</p>
                    <span class="text-3xl font-bold text-gray-800">41</span>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <ion-icon name="school-outline" class="text-3xl text-blue-600"></ion-icon>
                </div>
            </div>
        </a>

        <?php if (session()->get('role') == 'admin'): ?>
        <a href="<?= base_url('admin/user') ?>"
           class="block bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500
                  transition duration-300 hover:shadow-xl hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Total User</p>
                    <span class="text-3xl font-bold text-gray-800">120</span>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <ion-icon name="people-outline" class="text-3xl text-green-600"></ion-icon>
                </div>
            </div>
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/iku') ?>"
           class="block bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-500
                  transition duration-300 hover:shadow-xl hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">Indikator Kinerja (IKU)</p>
                    <span class="text-3xl font-bold text-gray-800">8</span>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <ion-icon name="document-text-outline" class="text-3xl text-yellow-600"></ion-icon>
                </div>
            </div>
        </a>

    </div>

    <!-- Grafik Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        
        <!-- Grafik Capaian per IKU (Line Chart) -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                <ion-icon name="analytics-outline" class="text-2xl mr-2 text-purple-600"></ion-icon>
                Capaian per Indikator (IKU)
            </h2>
            <div style="height: 300px; max-height: 300px;">
                <canvas id="ikuLineChart"></canvas>
            </div>
        </div>

        <!-- Grafik Capaian per Jurusan (Bar Chart) -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                <ion-icon name="bar-chart-outline" class="text-2xl mr-2 text-blue-600"></ion-icon>
                Capaian per Jurusan
            </h2>
            <div style="height: 300px; max-height: 300px;">
                <canvas id="jurusanBarChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Grafik Capaian per Triwulan (Area Chart) - Full Width -->
    <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
            <ion-icon name="trending-up-outline" class="text-2xl mr-2 text-green-600"></ion-icon>
            Capaian Kinerja per Triwulan
        </h2>
        <div style="height: 280px; max-height: 280px; width: 100%;">
            <canvas id="trendAreaChart"></canvas>
        </div>
        <div class="mt-2 text-xs text-gray-500 flex flex-wrap gap-4">
            <span><strong>Triwulan 1:</strong> Jan–Mar</span>
            <span><strong>Triwulan 2:</strong> Apr–Jun</span>
            <span><strong>Triwulan 3:</strong> Jul–Sep</span>
            <span><strong>Triwulan 4:</strong> Okt–Des</span>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        // Data dari controller PHP
        const ikuLabels = <?= json_encode($grafikData['labels']) ?>;
        const ikuValues = <?= json_encode($grafikData['values']) ?>;
        
        const jurusanLabels = <?= json_encode(array_column($capaianJurusan, 'nama')) ?>;
        const jurusanValues = <?= json_encode(array_column($capaianJurusan, 'capaian')) ?>;

        // 1. Grafik Line Chart - Capaian per IKU
        const ctxLine = document.getElementById('ikuLineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ikuLabels,
                datasets: [{
                    label: 'Capaian (%)',
                    data: ikuValues,
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(147, 51, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 2. Grafik Bar Chart - Capaian per Jurusan
        const ctxBar = document.getElementById('jurusanBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: jurusanLabels,
                datasets: [{
                    label: 'Capaian (%)',
                    data: jurusanValues,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)'
                    ],
                    borderWidth: 0,
                    borderRadius: 6,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Capaian: ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // 3. Grafik Area Chart - Capaian per Triwulan
        const ctxArea = document.getElementById('trendAreaChart').getContext('2d');
        new Chart(ctxArea, {
            type: 'line',
            data: {
                // Triwulan labels (4 quarters)
                labels: ['Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4'],
                datasets: [{
                    label: 'Capaian Rata-rata (%)',
                    // Rata-rata bulanan digabung per triwulan: Q1(72,75,78)=75, Q2(80,82,85)=82, Q3(83,86,88)=86, Q4(87,90,92)=90
                    data: [75, 82, 86, 90],
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(16, 185, 129)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Capaian: ' + context.parsed.y + '%';
                            },
                            afterBody: function() {
                                return 'Penjelasan Triwulan:\n1: Jan–Mar | 2: Apr–Jun | 3: Jul–Sep | 4: Okt–Des';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>

<?= $this->endSection() ?>