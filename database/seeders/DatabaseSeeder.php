<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Endereco;
use App\Models\Imovel;
use App\Models\Inquilino;
use App\Models\Locacao;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {



        User::factory(2)->create();
        Imovel::factory(30)->create();

        $imoveisIds = DB::table('imoveis')->pluck('id');
        foreach ($imoveisIds as $imovelId) {
            Endereco::factory()->create([
                'imovel_id' => $imovelId,
            ]);
        }

        Endereco::factory(30)->create();
        Inquilino::factory(30)->create();
        Locacao::factory(30)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@user.com',
        ]);
    }
}
