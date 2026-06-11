# Glow Agenda — Visão Geral do Sistema

Documento de apoio para apresentar o funcionamento principal da aplicação ao restante da equipe.

## Objetivo da Plataforma

O sistema Glow Agenda centraliza o agendamento de serviços de beleza, permitindo que estabelecimentos, profissionais e clientes gerenciem a agenda em tempo real.

### Módulos principais

1. **Autenticação e perfis**
   - Controle de acesso via sessões PHP e verificação de perfil (`admin`, `profissional`, `cliente`).
   - Redirecionamentos automáticos: usuários autenticados caem em `/dashboard`; visitantes são levados para `/login`.
   - Cadastro opcional de clientes via `/cadastro` (pode ser habilitado quando desejado).

2. **Gestão de serviços e categorias**
   - Cadastro de serviços com duração, preço e vínculo a categorias.
   - Soft delete de serviços (ativar/desativar) com bloqueio de exclusão de agendamentos ativos.
   - Controle de categorias com verificação antes da exclusão.

3. **Gestão de agendamentos**
   - Painel administrativo/profissional para criar, editar, concluir ou cancelar agendamentos.
   - Portal do cliente com criação de compromissos, remarcação e cancelamento (respeitando antecedência mínima de 2 horas).
   - Status suportados: `aguardando`, `confirmado`, `em_andamento`, `concluido`, `cancelado`.

4. **Sugestão de horários disponíveis**
   - Endpoint `/cliente/agendamentos/disponibilidades` calcula horários livres considerando duração de cada serviço, agendamentos em andamento e cancelados (que liberam o slot).
   - Interface do formulário do cliente exibe botões dinâmicos com horários sugeridos.

5. **Experiência do cliente**
   - Dashboard mostra próximo atendimento, lista de compromissos futuros e histórico concluído.
   - Validações amigáveis com mensagens de alerta e indicação visual do status.

6. **Configuração de horários de trabalho**
   - Admin define o expediente de cada profissional por dia da semana em `/profissionais/{id}/horarios`.
   - Profissionais podem ajustar o próprio expediente pelo menu lateral em `/profissional/horarios`.
   - Dias podem ser desativados individualmente; horários são validados (HH:MM) e o backend garante que agendamentos respeitem o intervalo configurado.
   - Na ausência de configuração explícita, aplica-se o expediente padrão (08:00 às 20:00).

## Perfis e Fluxos de Acesso

| Perfil          | Como acessa                                    | Principais permissões |
|-----------------|-------------------------------------------------|------------------------|
| **Admin**       | `/login` (credenciais administradas internamente) | Gerencia usuários, serviços, categorias e agenda completa. |
| **Profissional**| `/login`                                        | Gerencia próprios agendamentos, pode criar compromissos para clientes. Serviços visíveis conforme associação. |
| **Cliente**     | `/login` após criação de conta (via `/cadastro` ou cadastro interno) | Agenda, remarca e cancela atendimentos próprios, consome sugestões de horários. |

### Fluxo de login e cadastro
- Clientes podem se auto-cadastrar em `/cadastro`; o registro cria o usuário com perfil `cliente`, define a senha informada e vincula/atualiza a ficha de cliente pelo e-mail.
- Quando um admin/profissional adiciona clientes internamente (`clientes/novo`), apenas a ficha é criada. Para conceder acesso, é necessário usar `/cadastro` com o mesmo e-mail ou vincular manualmente a ficha a um usuário existente.
- O login é unificado em `/login`; após autenticar, o redirecionamento leva a `dashboard` (admin/profissional) ou `cliente/agendamentos` (clientes). Recuperação de senha acontece via `/forgot-password` e `/reset-password`.

### Fluxo do cliente
1. Recebe o link público do sistema (ex.: `https://seu-dominio.com/`).
2. Não autenticado → redirecionado para `/login` (ou `/cadastro` para criar conta).
3. Após login, acessa `/dashboard` e pode iniciar novos agendamentos via `/cliente/agendamentos/novo`.
4. Remarcações e cancelamentos só são aceitos até 2 horas antes do horário original.

### Fluxo do profissional/admin
1. Login em `/login` com credenciais fornecidas pela administração.
2. Painel `/dashboard` destaca agenda do dia e estatísticas.
3. Criação de agendamentos com validação de conflitos por duração e expediente personalizado.
4. Ajuste do expediente próprio (profissionais) ou de qualquer profissional (admin) via página de horários de trabalho.

## Disponibilidade e Regras de Conflito

- Cálculo de disponibilidade considera expediente padrão de 08h às 20h (ajustável no código) e usa intervalos de 15 minutos.
- Agendamentos cancelados liberam o horário; agendamentos em andamento projetam o término a partir da duração do serviço.
- Conflitos são validados no backend antes de salvar/agendar.

## Gestão Externa de Estabelecimentos

- Domínio, hospedagem, certificados e integrações corporativas são administrados externamente (fora da aplicação).
- A aplicação utiliza os dados do estabelecimento armazenados no banco (logo, nome fantasia) para personalização das telas, mas qualquer governança adicional (ERP, CRM, relatórios financeiros) permanece em ferramentas externas.
- Para disponibilizar o sistema aos clientes, basta apontar o domínio público para a infraestrutura que hospeda esta aplicação e distribuir o link de acesso.

## Rotas Importantes

| Rota | Descrição |
|------|-----------|
| `/` | Redireciona para `login` ou `dashboard`, conforme autenticação. |
| `/login` | Autenticação de todos os perfis. |
| `/cadastro` | Cadastro opcional de clientes. |
| `/dashboard` | Painel inicial pós-login (varia por perfil). |
| `/servicos` | Gestão de serviços e categorias (admin/profissional). |
| `/agendamentos` | Agenda completa para admin/profissional. |
| `/cliente/agendamentos` | Lista de agendamentos do cliente. |
| `/cliente/agendamentos/novo` | Formulário de novo agendamento com sugestões de horários. |
| `/cliente/agendamentos/disponibilidades` | API interna para cálculo de horários disponíveis. |
| `/profissionais/{id}/horarios` | Ajuste de expediente por profissional (admin). |
| `/profissional/horarios` | Página para o profissional configurar o próprio expediente. |

## Estrutura Simplificada do Projeto

- `www/` — Código PHP (controllers, models, views, assets).
- `Config/` — Helpers, rotas e segurança (verificação de perfil).
- `Views/` — Templates MVC com componentes reutilizáveis.
- `public/assets/css/style.css` — Camada visual personalizada.

## Credenciais de Teste

- **Admin**
  - Email: `admin@glowagenda.com`
  - Senha: `admin123`

Outros perfis podem ser criados pelo painel administrativo conforme necessidade.
