<?= $this->extend('layouts/admin_template') ?>

<?= $this->section('content') ?>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">CAPAIAN IKU</h1>
                <h2 class="text-xl font-semibold text-gray-700">JURUSAN <?= strtoupper($nama_jurusan) ?></h2>
                <h3 class="text-lg font-medium text-gray-600">PRODI <?= strtoupper($nama_prodi) ?> (<?= $jenjang ?>)</h3>
            </div>
            <div>
                <label for="tahun" class="text-sm font-medium text-gray-700">TAHUN:</label>
                <select id="tahun" name="tahun" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                </select>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        <div class="flex justify-between items-end">
            
            <div>
                <h4 id="judul-triwulan" class="text-lg font-semibold text-gray-700 mb-3">
                    Kinerja Triwulan 1 (Januari - Maret 2025)
                </h4>
                
                <div id="triwulan-buttons" class="flex rounded-md shadow-sm">
                    <button data-tw="1" data-text="Kinerja Triwulan 1 (Januari - Maret 2025)"
                       class="tw-button active px-4 py-2 rounded-l-md text-sm font-semibold z-10
                              bg-purple-600 text-white border border-purple-700">
                        TW 1
                    </button>
                    <button data-tw="2" data-text="Kinerja Triwulan 2 (April - Juni 2025)"
                       class="tw-button inactive px-4 py-2 text-sm font-semibold -ml-px
                              bg-white text-gray-700 border border-gray-300 
                              hover:bg-gray-100">
                        TW 2
                    </button>
                    <button data-tw="3" data-text="Kinerja Triwulan 3 (Juli - September 2025)"
                       class="tw-button inactive px-4 py-2 text-sm font-semibold -ml-px
                              bg-white text-gray-700 border border-gray-300 
                              hover:bg-gray-100">
                        TW 3
                    </button>
                    <button data-tw="4" data-text="Kinerja Triwulan 4 (Oktober - Desember 2025)"
                       class="tw-button inactive px-4 py-2 rounded-r-md text-sm font-semibold -ml-px
                              bg-white text-gray-700 border border-gray-300 
                              hover:bg-gray-100">
                        TW 4
                    </button>
                </div>
            </div>

            <?php if (session()->get('role') == 'admin'): ?>
            <div>
                <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition duration-300">
                    <ion-icon name="lock-closed-outline" class="text-xl"></ion-icon>
                    <span>Kunci Data</span>
                </button>
            </div>
            <?php endif; ?>
        </div>
        </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <?php foreach ($iku_data as $iku): ?>
        <div class="bg-white p-5 rounded-lg shadow-md transition duration-300 hover:shadow-xl">
            <div class="flex justify-between items-center mb-2">
                <span class="text-lg font-bold text-gray-800"><?= $iku['kode'] ?></span>
                <div class="p-2 bg-purple-100 rounded-full">
                    <ion-icon name="<?= $iku['icon'] ?>" class="text-2xl text-purple-600"></ion-icon>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-3 h-10"><?= $iku['nama'] ?></p>
            <div class="mb-3">
                <span class="text-4xl font-bold text-gray-900"><?= $iku['persentase'] ?>%</span>
                <span class="text-sm text-green-500 font-semibold ml-2">+5%</span>
            </div>
            <?php
                // Mengambil angka IKU, misal "IKU 1.1" -> "1.1"
                $iku_code = str_replace('IKU ', '', $iku['kode']);
            ?>
            <a href="<?= base_url('admin/iku-detail/' . $iku_code . '/' . $jurusan_kode . '/' . rawurlencode($nama_prodi) . '/' . $jenjang) ?>" 
               class="w-full block text-center bg-gray-200 hover:bg-purple-600 hover:text-white text-purple-700 font-semibold py-2 px-4 rounded-lg transition duration-300">
                Detail
            </a>
        </div>
        <?php endforeach; ?>
        
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buttons = document.querySelectorAll(".tw-button");
            const judulTriwulan = document.getElementById("judul-triwulan");
            const activeClass = "bg-purple-600 text-white border-purple-700 z-10";
            const inactiveClass = "bg-white text-gray-700 border-gray-300 hover:bg-gray-100";

            buttons.forEach(button => {
                button.addEventListener("click", function(e) {
                    e.preventDefault(); 
                    
                    buttons.forEach(btn => {
                        btn.classList.remove(...activeClass.split(" "));
                        btn.classList.add(...inactiveClass.split(" "));
                    });
                    
                    this.classList.remove(...inactiveClass.split(" "));
                    this.classList.add(...activeClass.split(" "));
                    
                    const newTitle = this.getAttribute("data-text");
                    judulTriwulan.textContent = newTitle;
                });
            });
        });
    </script>

<?= $this->endSection() ?>