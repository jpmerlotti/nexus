---
description: Diretrizes e padrões para criação e manutenção de componentes no Livewire v4.
---
# Contexto e Papel

Você é um especialista em Laravel Livewire v4. Esta skill orienta a criação e refatoração de componentes usando os padrões da nova versão do Livewire, que aboliu a necessidade do pacote Livewire Volt e introduziu nativamente Componentes de Arquivo Único (Single-File Components).

**Gatilho de Ativação:** `/livewire-v4 [descrição da ação/componente]` ou acionamento automático sempre que o escopo lidar com regras do Livewire.

## Regras de Comportamento e Limites (Guardrails)

1. **Foco no Single-File Format:** Por padrão, crie componentes em um único arquivo. Esse formato usa a tag `<?php` no topo do arquivo `.blade.php`, contendo uma classe anônima que estende `Livewire\Component`, acompanhada do HTML logo abaixo.
2. **NUNCA utilize sintaxe do Livewire Volt:** O pacote Volt (`Livewire\Volt`) não é mais utilizado. Evite totalmente o uso de funções procedurais do Volt como `use function Livewire\Volt\{state, mount, rules};`. Use propriedades e métodos de classe tradicionais dentro da classe anônima.
3. **Componentes de Página (Pages):** Para componentes de página cheia, utilize o prefixo `pages::` ao criá-los (`make:livewire pages::entidade.acao`), organizando-os no diretório `resources/views/pages/`.
4. **Simbolismo de Arquivos:** Preserve o caractere `⚡` gerado automaticamente pelo Livewire v4 no nome do arquivo (ex: `⚡create.blade.php`), a menos que as configurações do projeto o tenham desativado.
5. **Formato Multi-File (MFC):** Use o formato de múltiplos arquivos (arquivos `.php` e `.blade.php` separados via `--mfc`) apenas quando o componente se tornar excessivamente complexo ou a pedido expresso do usuário.

## I/O (Input/Output)

**Input:** Solicitação para criar ou modificar uma rota/página/componente interativo.
**Output:** Os arquivos são gerados via Artisan (`php artisan make:livewire`), o código no estilo Livewire v4 é escrito/refatorado diretamente no arquivo resultante e o usuário é informado sobre o sucesso da implementação.
