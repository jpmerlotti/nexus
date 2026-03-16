# Resumo Executivo: Nexus - Sistema de Campanhas de RPG com IA

## Visão Geral

O sistema visa gerenciar campanhas de RPG (Solo inicialmente, com suporte previsto para Multiplayer/Parties). A IA atua como o único Mestre de Jogo (Dungeon Master), gerando narrativas, reagindo às ações dos jogadores e gerenciando ativamente um mundo vivo (Entities) de forma procedural.

## Arquitetura de Banco de Dados e Modelagem

1. **Campanha (Campaign)**
   - Entidade central gerencial.
   - Configurações armazenadas em colunas explícitas (não JSON) para facilitar consultas, integridade e indexação.
   - Atributos esperados: Nível de Detalhamento da Narração, Dificuldade, Nível Inicial, Estilo de Jogo.
   - **Sistema de Progressão:** O sistema de curva de evolução da campanha será configurável no banco de dados, ditando se a evolução acontecerá por *Gestão de XP (Experiência)* ou *Milestone (Marcos Narrativos)*.

2. **Personagem do Jogador (Player Character)**
   - Entidade totalmente separada do mundo gerado pela IA.
   - Relacionamento: `User` tem muitos `Characters`.
   - Vínculo com Campanha: Tabela pivô (ex: `campaign_character`) para permitir a inclusão de múltiplos jogadores em uma mesma sessão no futuro ("Parties"/"Jams").
   - **Campos Mecânicos de Progressão:** O Personagem terá colunas robustas e estruturadas para manipular os atributos estatísticos básicos (Nível, Vida Máxima, Vida Atual, XP Atual, Força, Destreza, etc.).
   - **Curva de Evolução Integrada:** Embora a IA anuncie narrativamente a distribuição de XP ou o alcance de um Milestone, a mecânica real de "Subida de Nível" ou manipulação de pontos de atributos é ditada por parâmetros travados no banco de dados da Campanha, exigindo a confirmação do jogador em uma interface de ficha (Level Up UI).

3. **Elementos do Mundo (World Elements - Entidade Polimórfica)**
   - Tabela base (ex: `world_elements`) centraliza metadados. Busca unificada via IA.
   - Utilização de relacionamento polimórfico real estruturado no Laravel (`elementable_id`, `elementable_type`).
   - Tabelas filhas (`items`, `locations`, `npcs`) possuem atributos técnicos estruturados.
   - **Sistema de Posse Misto (Ownership):** Um `WorldElement` do tipo Item pode tanto pertencer polimorficamente a outro `WorldElement` (ex: uma espada dentro de um baú ou equipada por um NPC), quanto pertencer diretamente a um `Character` (via tabela pivô `character_inventory` ou polimorfismo expandido).

4. **Histórico da Narrativa (Story Logs / Chat)**
   - Tabela dedicada e relacional (ex: `campaign_messages` ou `story_logs`) para gravar o ping-pong exato de mensagens.
   - Organizado por turnos, distinguindo `role` do emissor (`user`, `assistant`, system logs como dano ambiental).
   - Será crucial para alimentar a "Memória Histórica" da IA e servir como Front-end para o jogador reler a sua história.

## Arquitetura da IA (Sistema Multi-Agente)

1. **O Pensador, Narrador e Escriba (Consolidados):** Fundidos em uma única chamada de LLM (*Structured Outputs / Tool Calling*) para reduzir a latência e o custo de chamadas sequenciais por turno. A IA responderá com a narração e o Array JSON de modificações no mundo simultaneamente.
2. **O Buscador (Retrieval & Redis):** Pré-processa o turno validando as entidades imediatas usando o Redis como "Memória de Curto Prazo" da sessão ativa (Contexto Quente). Reduz a necessidade de buscas em banco em cada turno de um combate ou diálogo longo.
3. **Mundo Procedural Assíncrono:** A "Geração Procedural de Contexto", responsável por movimentar facções e manter o mundo "vivo" longe dos olhos do jogador, ocorrerá em processos assíncronos via *Laravel Queues / Background Jobs* (Scheduler/Cron), sem impactar o tempo de resposta do "turno" ativo do jogador.

## Escopo Inicial de Implementação

- **Campaign**: Criação do Schema e CRUD com as configurações base, incluindo regras de progressão.
- **Character**: Modelo, Migrations, relacionamento com a Campanha e os campos estatísticos manipuláveis de atributos baseados no Level Up.
- **WorldElement**: Criação do Schema base polimórfico e exemplos das primeiras entidades filhas.
- **StoryLogs**: Criação da tabela para armazenar o ping-pong da narrativa.

## Módulo Adicional: Sistema Avançado de Personagens (Regras D&D 5e)

### Resumo Executivo

Reformulação profunda no sistema de personagens para que ele suporte rigorosamente a estrutura do D&D 5e. A premissa central é que a Inteligência Artificial, atuando como Mestre (DM), seja capaz não apenas de interpretar a ficha técnica, mas de prever e orquestrar eventos narrativos ligados à evolução mecânica do personagem (ex: Multiclasse ou liberação de Arquétipos no Nível 3).

### Requisitos Técnicos Preliminares

1. **Estrutura de Dados Modificada (Classes/Multiclasse):**
   - O campo JSON `classes` armazenará objetos complexos: `class_name` (Enum D&D), `level` (Int), `archetype` (String/Enum, nulo antes do nível de liberação), `source` (String descrevendo como foi obtida, ex: `starting`, `natural_growth`, `narrative_event`).

2. **Gatilhos de Progressão (Engine de Regras):**
   - Nem toda classe ganha Arquétipo no nível 3 (Clérigos escolhem Domínio no Nível 1; Bruxos/Feiticeiros no Nível 1; Magos no Nível 2).
   - O sistema precisa varrer as classes ativas de um jogador a cada "Level Up" para engatilhar a tela/ação de "Escolha de Arquétipo" ou notificar a IA.

3. **Restrições de Multiclasse:**
   - D&D 5e exige atributos mínimos (geralmente 13 no atributo principal da nova classe) para habilitar uma multiclasse.
   - O array de classes validará os pré-requisitos lógicos no Livewire.

4. **Orquestração da IA (Context Awareness):**
   - A IA terá acesso em sua injestão de contexto à trilha do jogador ("Guerreiro de Nv 2, sem arquétipo").
   - A IA precisará de injeção de prompt instrucionando-a a propor pequenos plots que encaminhem o jogador para a escolha de seu Arquétipo natural.

### Casos de Uso Esperados

- **Caso A (Level Up Padrão com Arquétipo):** O jogador atinge XP e sobe seu Ladino (Lv 2 -> 3). O TALL Stack bloqueia a simples adição de nível e o obriga a selecionar seu Arquétipo (Assassino, Ladrão, Arcane Trickster).
- **Caso B (Multiclasse Narrativa Dirigida pela IA):** O guerreiro faz um pacto com um demônio. A IA notifica o painel, sugerindo ao jogador adicionar 1 nível de Bruxo (Warlock) na próxima evolução, mesmo sem o requisito puro (Regra de Ouro do Mestre).
- **Caso C (Multiclasse Mecânica):** O jogador tenta pegar Feiticeiro no Nível 4, mas tem Carisma 10. O frontend bloqueia.

### Questões a Clarificar (Edge Cases)

1. **Gestão do "Momento" da Escolha:** Encodaremos todas as regras rígidas do D&D no código TALL Stack (um dicionário de classes e atributos mínimos) ou será interpretativo (o jogador adiciona sob a honra da regra)?
2. **Poder da IA sobre a Ficha:** A IA poderá modificar a ficha mecânica via API (Injetar bônus no JSON) ou será apenas "Sugeridora", deixando a gestão manual da ficha pro Jogador no frontend?
3. **Equipamento Inicial:** O jogador digita o inventário manualmente a partir do livro, ou faremos tabelas de "Kits Iniciais" dependentes da Classe e Background para popular o JSON automaticamente?

## Módulo Adicional: Interface Principal da Campanha (Chat & UX)

### Resumo Executivo

A interface principal da campanha será o hub central onde ocorre a interação entre o jogador e o Mestre de Jogo (IA). O design focará em uma experiência imersiva com um chat central, painéis utilitários e um roadmap tecnológico que prevê evolução de Livewire/Alpine para WebSockets (Reverb) visando o multiplayer. Embora o MVP tenha o foco principal no Desktop, a construção será Mobile-first.

### Requisitos Técnicos e Decisões de Arquitetura

1. **Interação e Sincronização de Estado (Function Calling):**
   - A IA utilizará intensivamente *Function Calling / Tools* nativos para manipular o estado do jogo estruturadamente (ex: atualizar HP ou inventário) e acionar sistemas externos (ex: rolagens de dados pelo sistema em vez de texto puro).

2. **Gerenciamento de Estado do Frontend (UX do Chat):**
   - **MVP:** Utilização de Livewire e Alpine.js. Em vez de streaming em tempo real, o sistema mostrará um indicador de digitação (três pontinhos animados) enquanto aguarda, exibindo o bloco formatado por inteiro quando a requisição finalizar.
   - **Futuro Próximo:** Migração para o Laravel Reverb com WebSockets para suportar respostas em tempo real, streaming de texto e sessões multiplayer perfeitamente sincronizadas.

3. **Gerenciamento de Memória da IA:**
   - Combinação de Resumos Históricos (Summaries) e RAG (Retrieval-Augmented Generation).
   - Utilização de cache agressivo para os "dados importantes e recentes" da sessão ativa, reduzindo a necessidade de buscas caras no Vector Database a cada turno.

4. **Modos de Jogo e Responsividade:**
   - O desenvolvimento será voltado para *Single-Player* no MVP, mas a arquitetura já preverá a inclusão de *Multiplayer* (Parties) futuramente.
   - O layout CSS (Tailwind) será desenvolvido utilizando a abordagem *Mobile-first*, organizando o conteúdo para telas menores, mesmo que o MVP planejado tenha sido validado e otimizado primariamente para **Desktop**.

### Componentes da Interface (Paineis e Funcionalidades)

A tela principal conterá o Chat ao centro e as seguintes abas/painéis de apoio:

- **Personagem:** Ficha estendida, atributos, proficiências e habilidades.
- **Inventário:** Itens, moedas e listagem de equipamentos em uso.
- **Magias:** Grimório e gerenciamento de *Spell Slots* (caso o personagem use magia).
- **Relacionamentos:** Afinidade e histórico com NPCs proeminentes do mundo.
- **Bestiário / Lore:** Enciclopédia de entidades e locais já descobertos.
- **Diário de Missões (Quest Log):** Rastreamento de missões ativas, completadas e boatos.
- **Mapa / Localização:** Descritivo rápido ou esquema da área atual com clima e hora.
- **Rolador de Dados / Log de Combate:** Visão tática separada da narrativa principal.
- **Status do Grupo (Party):** Resumo visual rápido de HP e condições para controle de aliados.
- **Ações Rápidas (Macros):** Atalhos convenientes (ex: *Percepção*, *Furtividade*, *Ataque Básico*).
- **Rastreador de Iniciativa:** Linha do tempo visual durante combates para indicar turnos.

## Módulo Adicional: Campanhas Pré-Configuradas (Presets)

Para auxiliar jogadores que não sabem por onde começar, o sistema oferecerá 12 campanhas pré-fabricadas. Elas servirão como *Seeds* detalhados (Prólogos) para a Inteligência Artificial iniciar a narrativa com contexto riquíssimo.

### Categoria Clássica: Iniciante (Fácil)

Focadas em introduzir as mecânicas do RPG, combate perdoável e plots diretos.

1. **O Mistério da Mina de Phandelver (Adaptação):** Os heróis são contratados para escoltar uma carroça até uma vila fronteiriça, mas descobrem uma conspiração goblinóide envolvendo antigas forjas mágicas.
2. **Sombras sobre a Feira da Colheita:** Durante o festival anual, crianças começam a desaparecer. Uma investigação leve leva a fadas preguiçosas pregando peças na floresta encantada.
3. **O Despertar do Rei Rato:** Os esgotos da grande capital estão transbordando com ratos gigantes mutantes após o vazamento de um laboratório alquímico. Uma clássica missão de extermínio e masmorra introdutória.

### Categoria Clássica: Intermediário (Médio)

Desafios táticos, dilemas morais, gestão de recursos moderada e oponentes inteligentes.
4. **Aliança Quebrada de Prata:** Duas cidades-estado élficas estão à beira da guerra civil após o roubo de uma relíquia sagrada. Os jogadores precisam atuar como diplomatas e espiões para descobrir o verdadeiro culpado.
5. **Maldição do Navio Fantasma "Leviatã":** Uma embarcação infestada de mortos-vivos ressurge na baía costeira trazendo uma névoa letal. Os jogadores devem invadir o navio no mar e quebrar a âncora necromântica.
6. **O Labirinto do Minotauro Louco:** Um rei exilado construiu um labirinto em constante mutação, cheio de armadilhas mortais e quebra-cabeças, escondendo um tesouro capaz de derrubar o atual império.

### Categoria Clássica: Avançado (Difícil / Letal)

Combate implacável, risco constante de morte permanente e tramas cósmicas complexas.
7. **Túmulo dos Deuses Esquecidos:** Uma exploração brutal a uma pirâmide invertida no deserto de areias negras. Envolve demônios de alto escalão, armadilhas insta-kill e escassez extrema de suprimentos.
8. **A Trama da Rainha Dragão:** Um culto dracônico já infiltrou toda a alta nobreza bélica do continente. Os heróis estão sendo caçados pelo estado e precisam liderar uma resistência armada contra Dragões Anciões.
9. **Noite Eterna sobre Ravenloft:** Os aventureiros são sugados para um semi-plano de terror governado por um vampiro milenar. Sobrevivência de terror psicológico, recursos escassos e a corrupção inevitável do alinhamento.

### Categoria "Folclore Abrasileirado" (Custom Settings)

Campanhas que utilizam o motor de regras do D&D, mas adaptam elementos da rica mitologia, história e folclore brasileiro para um cenário de Fantasia Sombria/Tropical.
10. **A Febre do Ouro Amaldiçoado (Fácil - Cenário Bandeirante):** Situado nas serras de "Minas de Ouro". O grupo investiga o caso do "Corpo-Seco", uma entidade necromântica que secou um rio inteiro e paralisou o garimpo local. Contém fadas tropicais e exploração de selva.
11. **O Uivo da Besta Pampa (Médio - Cenário Sulista/Gáucho):** Nas frias planícies do sul profundo, estâncias estão sendo atacadas durante as luas de sangue. Uma guilda de caçadores (os "Peões") contrata os heróis para investigar a lenda do Lobisomem de Sete Pueblos, onde até mesmo o Capitão local parece esconder uma maldição.
12. **O Império de Boitatá e o Fogo Fátuo (Difícil - Selva Profunda):** No coração de uma floresta equivalente à Amazônia mítica, bandeirantes arcanos tentam escravizar entidades da natureza. O grupo deve caçar a Mãe-do-Ouro e enfrentar xamãs corrompidos e a serpente de fogo primordial (com estatísticas de um Tarrasque/Dragão) para impedir a queima do pulmão do mundo.
