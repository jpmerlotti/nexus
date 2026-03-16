<?php

namespace App\Enums;

enum CharacterBackground: string
{
    case ACOLYTE = 'acolyte';
    case CHARLATAN = 'charlatan';
    case CRIMINAL = 'criminal';
    case ENTERTAINER = 'entertainer';
    case FOLK_HERO = 'folk_hero';
    case GUILD_ARTISAN = 'guild_artisan';
    case HERMIT = 'hermit';
    case NOBLE = 'noble';
    case OUTLANDER = 'outlander';
    case SAGE = 'sage';
    case SAILOR = 'sailor';
    case SOLDIER = 'soldier';
    case URCHIN = 'urchin';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACOLYTE => __('Acólito'),
            self::CHARLATAN => __('Charlatão'),
            self::CRIMINAL => __('Criminoso'),
            self::ENTERTAINER => __('Artista'),
            self::FOLK_HERO => __('Herói do Povo'),
            self::GUILD_ARTISAN => __('Artesão de Guilda'),
            self::HERMIT => __('Eremita'),
            self::NOBLE => __('Nobre'),
            self::OUTLANDER => __('Forasteiro'),
            self::SAGE => __('Sábio'),
            self::SAILOR => __('Marinheiro'),
            self::SOLDIER => __('Soldado'),
            self::URCHIN => __('Órfão'),
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::ACOLYTE => __('Você dedicou a vida a servir um templo de um deus específico ou a um panteão de deuses.'),
            self::CHARLATAN => __('Você sempre teve um jeito com as pessoas. Sabe o que as motiva e pode descobrir os desejos dos seus corações.'),
            self::CRIMINAL => __('Você tem um histórico na quebra da lei e sobreviveu nos cantos escuros das cidades.'),
            self::ENTERTAINER => __('Você vive para se apresentar. A magia da sua arte pode encantar, inspirar e cativar multidões.'),
            self::FOLK_HERO => __('Você vem de origens humildes, do povo comum, mas seu destino fala mais alto. O povo local já o vê como seu campeão.'),
            self::GUILD_ARTISAN => __('Você é membro de uma guilda de artesãos. Sua maestria lhe garante o respeito da comunidade mercantil.'),
            self::HERMIT => __('Você viveu a maior parte do seu tempo em isolamento – em uma comunidade reclusa ou completamente sozinho.'),
            self::NOBLE => __('Você entende os meandros de riqueza, poder e privilégio. Uma família de posses e muita herança corre em suas veias.'),
            self::OUTLANDER => __('Você cresceu nos ermos selvagens, longe da civilização e do conforto das sociedades avançadas.'),
            self::SAGE => __('Você passou anos absorvendo os conhecimentos do multiverso, vasculhando pergaminhos velhos e grimórios poeirentos.'),
            self::SAILOR => __('Você velejou por oceanos inteiros por muito tempo, enfrentando tempestades, krakens e saqueadores.'),
            self::SOLDIER => __('Sua vida inteira sempre foi pautada na guerra e na disciplina. O soar do berrante e o choque de escudo o definem.'),
            self::URCHIN => __('Sua vida sempre foi de pobreza e luta cruel nas ruas. Você aprendeu sozinho a arte de sobreviver no lixo dos grandes.'),
        };
    }
}
