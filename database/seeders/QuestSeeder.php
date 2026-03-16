<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class QuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quests = [
            [
                'key' => 'first_character_created',
                'name' => 'Primeiro Passo',
                'description' => 'Crie seu primeiro personagem para começar sua jornada no Nexus.',
                'reward_nex' => 50,
                'is_repeatable' => false,
            ],
            [
                'key' => 'first_dice_roll',
                'name' => 'Explorador de Masmorras',
                'description' => 'Realize sua primeira rolagem de dados em uma campanha.',
                'reward_nex' => 20,
                'is_repeatable' => false,
            ],
            [
                'key' => 'first_item_added',
                'name' => 'Mestre de Itens',
                'description' => 'Adicione seu primeiro item ao inventário do seu personagem.',
                'reward_nex' => 20,
                'is_repeatable' => false,
            ],
        ];

        foreach ($quests as $quest) {
            \App\Models\Quest::updateOrCreate(['key' => $quest['key']], $quest);
        }
    }
}
