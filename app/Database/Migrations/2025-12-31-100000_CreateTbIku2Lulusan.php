<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbIku2Lulusan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_triwulan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nim' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'status_aktivitas' => [
                'type' => 'ENUM',
                'constraint' => ['Bekerja', 'Melanjutkan Pendidikan', 'Wirausaha', 'Mencari Kerja', 'Belum Memungkinkan Bekerja'],
                'default' => 'Mencari Kerja',
            ],
            'nama_tempat' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'pendapatan' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'masa_tunggu_bulan' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'link_bukti' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('nim', 'tb_m_mahasiswa', 'nim', 'CASCADE', 'CASCADE');
        // Assuming triwulan table exists and has id. If not, remove FK or check first. 
        // Based on previous errors, triwulan table exists (we queried it).
        // $this->forge->addForeignKey('id_triwulan', 'triwulan', 'id', 'CASCADE', 'CASCADE'); 

        $this->forge->createTable('tb_iku_2_lulusan');
    }

    public function down()
    {
        $this->forge->dropTable('tb_iku_2_lulusan');
    }
}
