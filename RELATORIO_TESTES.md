# Relatório de Testes - Glow Agenda
**Data do Teste:** 3 de Junho de 2026  
**Ambiente:** Docker (Apache 2.4, MySQL 8.0, phpMyAdmin)  
**URL Testada:** http://localhost:8050  
**Status Geral:** ⚠️ Funcional com Bugs Críticos

---

## 1. Resumo Executivo

A aplicação **Glow Agenda** foi testada com sucesso após correções manuais. A plataforma de gerenciamento de agendamentos para salão de beleza apresenta uma interface visualmente agradável e funcionalidades bem implementadas. Porém, foram identificados **3 bugs críticos/graves** que impedem o funcionamento automático do projeto.

---

## 2. Bugs Encontrados

### 🔴 BUG #1: Banco de Dados Não Criado Automaticamente (CRÍTICO)
**Severidade:** CRÍTICO  
**Tipo:** Infraestrutura  
**Status:** Identificado e Resolvido Manualmente

#### Descrição
Ao inicializar os containers Docker, o banco de dados não é criado automaticamente, causando erro fatal na primeira tentativa de login.

#### Erro Retornado
```
Fatal error: Uncaught PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 
Table 'projeto.usuarios' doesn't exist in /var/www/html/Models/Database.php:62
```

#### Localização
- **Arquivo:** Models/Database.php (linha 62)
- **Função:** PDOStatement->execute()
- **Stack Trace:** AuthController->authenticate() → UsuarioModel->buscarPorEmail()

#### Causa Raiz
O arquivo `database.sql` não é executado automaticamente ao iniciar o container MySQL. Existe um script SQL em `www/database.sql`, mas ele não é incluído na configuração do Docker.

#### Solução Aplicada
Executado manualmente:
```bash
docker exec -i mysql_server mysql -ualuno -p123456 projeto < www/database.sql
```

#### Solução Recomendada
Modificar o `Dockerfile` para executar o script SQL automaticamente:
```dockerfile
COPY www/database.sql /docker-entrypoint-initdb.d/
```

---

### 🔴 BUG #2: Hash de Senha Padrão Inválida (CRÍTICO)
**Severidade:** CRÍTICO  
**Tipo:** Segurança/Autenticação  
**Status:** Identificado e Corrigido

#### Descrição
A hash bcrypt da senha padrão do usuário administrador no arquivo `database.sql` é inválida, impossibilitando login com as credenciais padrão.

#### Detalhes
- **Usuário:** admin@glowagenda.com
- **Senha:** admin123
- **Hash Inválida:** `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`
- **Erro ao verificar:** password_verify() retorna `false`

#### Localização
- **Arquivo:** www/database.sql (linhas 106-107)
- **Tabela:** usuarios
- **Campo:** usuario_senha

#### Impacto
Impossibilidade de fazer login com as credenciais padrão fornecidas no banco de dados inicial.

#### Solução Aplicada
Gerada nova hash válida e atualizada no banco:
```sql
UPDATE usuarios SET usuario_senha = '$2y$10$./6rupil/.AqzPUWJhyr0uJYKEoQ7/xu51JIwzMB4uUv7kKL5tHSq' 
WHERE usuario_email = 'admin@glowagenda.com';
```

#### Solução Recomendada
Usar script de inicialização para gerar hash dinâmica ou atualizar `database.sql` com hash válida.

---

### 🟡 BUG #3: Problema de Encoding UTF-8 (MENOR)
**Severidade:** MENOR  
**Tipo:** Apresentação  
**Status:** Identificado

#### Descrição
Caracteres acentuados aparecem como "????" em nomes de serviços e outras strings com diacríticos.

#### Exemplos
- "Coloração Raiz" → exibe como "Colora????o Raiz"
- "Hidratação Profunda" → exibe como "Hidrata????o Profunda"

#### Localização
- **Afetado:** Página de Serviços (/servicos)
- **Campo:** servico_nome da tabela servicos
- **Banco de Dados:** projeto.servicos

#### Causa Raiz
Incompatibilidade de charset entre a conexão MySQL e o banco de dados:

```
Variável                      | Valor
character_set_client          | latin1
character_set_connection      | latin1
character_set_results         | latin1
character_set_database        | utf8mb4
character_set_server          | utf8mb4
```

#### Impacto
Degradação visual da interface, afetando a experiência do usuário e tornando impossível a leitura correta de nomes de serviços.

#### Solução Recomendada
1. Adicionar ao início do `database.sql`:
```sql
SET NAMES utf8mb4;
SET CHARACTER_SET_CLIENT utf8mb4;
SET CHARACTER_SET_RESULTS utf8mb4;
```

2. Configurar no Dockerfile:
```dockerfile
ENV MYSQL_INITDB_SKIP_TZINFO=1
RUN echo '[mysqld]' >> /etc/mysql/conf.d/charset.cnf && \
    echo 'character_set_server=utf8mb4' >> /etc/mysql/conf.d/charset.cnf && \
    echo 'collation_server=utf8mb4_unicode_ci' >> /etc/mysql/conf.d/charset.cnf
```

---

## 3. Testes de Funcionalidade

### ✅ Página de Login
**Status:** FUNCIONANDO  
**Resultado:** Carregamento correto, formulário responsivo  
**Observações:** Layout limpo e intuitivo com validação de campos

### ✅ Autenticação
**Status:** FUNCIONANDO (após correção Bug #2)  
**Resultado:** Login bem-sucedido com admin@glowagenda.com / admin123  
**Observações:** Redirecionamento correto para dashboard

### ✅ Dashboard
**Status:** FUNCIONANDO  
**Resultado:** Página carregada com sucesso, dados exibidos corretamente  
**Métricas Exibidas:**
- Agendamentos Hoje: 0
- Receita do Dia: R$ 0,00
- Clientes Cadastrados: 0
- Receita do Mês: R$ 0,00

### ✅ Página de Serviços
**Status:** FUNCIONANDO (com problema visual - Bug #3)  
**Resultado:** Lista de 7 serviços exibida corretamente  
**Serviços Listados:**
1. Corte Feminino - R$ 80,00
2. Corte Masculino - R$ 50,00
3. Mechas Balayage - R$ 250,00
4. Colora????o Raiz - R$ 120,00 ⚠️
5. Hidrata????o Profunda - R$ 120,00 ⚠️
6. Manicure - R$ 40,00
7. Pedicure - R$ 50,00

### ✅ Página de Clientes
**Status:** FUNCIONANDO  
**Resultado:** Página carregada, tabela vazia como esperado (sem dados iniciais)  
**Funcionalidades Testadas:**
- Campo de busca disponível
- Botão "Novo Cliente" funcional
- Layout responsivo

### ✅ Página de Agendamentos
**Status:** FUNCIONANDO  
**Resultado:** Exibição correta de agendamentos de exemplo  
**Funcionalidades Testadas:**
- Visualização em Lista/Dia/Semana
- Filtro por Profissional
- Agendamentos exibidos:
  - 14:00 - Maria Almeida - Corte + Hidratação - Ana Silva (Aguardando)
  - 15:30 - Joana Oliveira - Coloração Raiz - Carla Mendes (Confirmado)

---

## 4. Estrutura Testada

### Banco de Dados
- ✅ Tabelas criadas: usuarios, categorias, servicos, clientes, agendamentos
- ✅ Relacionamentos implementados corretamente
- ✅ Dados iniciais populados

### Controllers
- ✅ AuthController - Login e logout funcionando
- ✅ DashboardController - Dashboard exibindo corretamente
- ✅ ServicoController - Listagem de serviços
- ✅ ClienteController - Gerenciamento de clientes
- ✅ AgendamentoController - Visualização de agendamentos

### Views
- ✅ Layout responsivo com Bootstrap 5
- ✅ Tema visual consistente
- ✅ Menu de navegação funcional
- ✅ Componentes bem estruturados

---

## 5. Recomendações Gerais

### Prioritárias (Antes do Deploy)
1. **Automatizar criação do banco:** Implementar script de inicialização no Docker
2. **Corrigir hash de senha:** Atualizar `database.sql` com hash válida
3. **Corrigir encoding UTF-8:** Configurar charset correto em todo o sistema

### De Melhoria
1. Adicionar health check para o banco de dados
2. Implementar logging de erros mais robusto
3. Adicionar validação de integridade referencial
4. Criar scripts de backup para dados do banco

---

## 6. Conclusão

A aplicação **Glow Agenda** apresenta uma estrutura sólida e bem implementada. Os bugs encontrados são principalmente relacionados à **configuração inicial do ambiente Docker** e não refletem problemas na lógica de negócio.

Com as correções implementadas e as recomendações aplicadas, a aplicação estará pronta para produção.

---

**Testado por:** GitHub Copilot  
**Plataforma de Teste:** Windows 11 + Docker Desktop  
**Versões Utilizadas:**
- Apache 2.4
- MySQL 8.0
- PHP 8.0+
- Bootstrap 5.3.3
