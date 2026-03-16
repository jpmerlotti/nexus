# Plano Técnico de Execução: Interface Principal da Campanha (Chat & UX)

Este documento detalha os passos atômicos necessários para implementar a Interface Principal da Campanha (`Campaign Screen`), baseada nos requisitos do `brainstorm.md`. O foco é estabelecer a fundação estrutural com Livewire, Alpine.js e TailwindCSS, visando um MVP funcional single-player, mas estruturado para expansão futura (Reverb/Multiplayer).

## Fase 1: Fundação Estrutural e Layout Base

Nesta fase, criamos os componentes estruturais vazios que comporão o layout central e os painéis laterais. A abordagem será mobile-first, usando TailwindCSS.

1. **Criar Rota e Controller/Componente Base da Campanha:**
    * Definir rota roteiro: `/campaigns/{campaign}/play`.
    * Criar o componente Livewire principal: `php artisan make:livewire Campaign\PlayCampaign`.
    * Este componente será o contêiner mestre e gerenciará o estado global da tela (qual painel está aberto no mobile, por exemplo).

2. **Desenvolver o Layout Base (Grid/Flexbox):**
    * Criar a estrutura HTML/CSS no `play-campaign.blade.php`.
    * **Mobile:** Layout em coluna (Chat ocupa a tela principal), com botões (Bottom Navigation ou Hamburger Menu) para abrir os painéis laterais (Offcanvas ou modais/gavetas deslizantes).
    * **Desktop:** Layout em Grid ou Flex. Sugestão: Sidebar Esquerda (Menus de Navegação entre Paineis), Centro (Área de Chat expandida), Sidebar Direita (Painel de Contexto Ativo - ex: Ficha do Personagem ou Inventário).

3. **Criar Componentes Livewire/Blade para os Painéis Laterais (Cascas Vazias):**
    * Criar componentes separados para manter o código limpo. Podem ser apenas componentes Blade normais se não precisarem de estado independente imediato, ou Livewire se precisarem de complexidade.
    * `php artisan make:livewire Campaign\Panels\CharacterPanel`
    * `php artisan make:livewire Campaign\Panels\InventoryPanel`
    * `php artisan make:livewire Campaign\Panels\SpellsPanel` (Opcional no MVP, dependendo da classe)
    * `php artisan make:livewire Campaign\Panels\QuestLogPanel`
    * `php artisan make:livewire Campaign\Panels\MapPanel` (ou apenas um placeholder de localização na UI)
    * `php artisan make:livewire Campaign\Panels\DiceRollerPanel`

## Fase 2: Implementação do Chat Central (UX)

O coração da experiência. Foco na usabilidade do texto e na indicação visual de que a IA está "pensando".

1. **Modelagem e Acesso a Histórico de Mensagens:**
    * Garantir que o Model `Campaign` (ou `StoryLog`) consegue recuperar o histórico de mensagens da sessão.
    * O componente Livewire `PlayCampaign` carregará as mensagens mais recentes no *mount()*.

2. **Interface de Exibição das Mensagens (Chat UI):**
    * Criar componente visual Blade para a mensagem do Jogador (alinhada à direita, visual distinto).
    * Criar componente visual Blade para a mensagem do Mestre/IA (alinhada à esquerda, visual mais narrativo/textual).
    * Criar componente visual Blade para Mensagens de Sistema/Rolagens de Dados (centralizadas ou com destaque visual diferenciado).

3. **Barra de Digitação e Envio (Input Area):**
    * Criar o `<textarea>` responsivo na parte inferior do chat.
    * Vincular via `wire:model` ao componente Livewire.
    * Implementar a ação de envio (`wire:click="sendMessage"` ou Enter prevent default via Alpine).

4. **Implementação do "Typing Indicator" (Alpine.js & Livewire):**
    * Criar um estado no Livewire (ex: `public bool $isAiThinking = false;`).
    * Ao enviar a mensagem, o Livewire seta `isAiThinking = true`.
    * A interface (usando Alpine para animação suave ou apenas condicional Blade) exibe uma bolha de chat com "três pontinhos animados" enquanto analisa.
    * Quando a resposta longa e formatada da IA retornar pela mesma requisição Livewire, `$isAiThinking` volta a ser falso e o bloco de texto renderiza por inteiro, junto com a animação de rolagem (scroll to bottom).

5. **Scroll Automático (Alpine.js):**
    * Utilizar Alpine.js (`x-data`, `x-init`, `$watch`) no container do chat para garantir que a rolagem desça automaticamente sempre que o array de mensagens for atualizado.

## Fase 3: Integração Básica dos Painéis Laterais

Nesta fase, preenchemos as "cascas vazias" com dados estáticos ou dados básicos puxados dos modelos recém-criados.

1. **Integração do Painel de Personagem (`CharacterPanel`):**
    * Recuperar o perfil do `Character` selecionado para a `Campaign`.
    * Renderizar os blocos visuais de HP, XP (barra de progresso), Classe(s), Nível e atributos (Força, Destreza, etc.).

2. **Integração do Painel de Rolagem de Dados (`DiceRollerPanel`):**
    * Criar botões rápidos para rolagens comuns (d20, d6, etc.).
    * Ação no Livewire para gerar o número aleatório e, crucialmente, despachar essa rolagem não apenas para a tela local, mas para o "log" ou para o prompt da IA no próximo turno.

3. **Barra Superior / Status Rápido:**
    * Criar uma barra de cabeçalho fixa com título da campanha, botão rápido de "Pause" (ou menu hamburguer no mobile), e um rastreador de HP/Status global.

## Fase 4: O "Esqueleto" de Function Calling (Back-end Bridge)

Preparar o terreno para que a IA consiga atualizar a tela sem recarregar.

1. **Estabelecer Infraestrutura de "Comandos Estruturados":**
    * Ao invés de apenas injetar texto na UI, o método `sendMessage` no backend deve estar preparado para processar a resposta estruturada da IA.
    * Exemplo de ciclo: Usuário envia mensagem -> Backend chama LLM -> LLM responde JSON: `{text: 'O goblin morre', actions: [{type: 'update_hp', target: 'goblin_1', value: 0}]}`.
    * O Livewire processa as `actions` internamente, atualizando suas propriedades ou Models no banco de dados.

2. **Reatividade Baseada em Eventos Livewire:**
    * O componente mestre `PlayCampaign` dispara eventos como `Dispatch('characterUpdated')` ou `Dispatch('inventoryChanged')` após processar as actions da IA.
    * Os painéis (ex: `CharacterPanel`) escutam esses eventos (`#[On('characterUpdated')]`) e re-renderizam para refletir as novas estatísticas sem refresh manual.
