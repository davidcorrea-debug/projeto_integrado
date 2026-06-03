SET NAMES utf8mb4;
DELETE FROM servicos;
INSERT INTO `servicos` (`servico_nome`, `servico_descricao`, `servico_preco`, `servico_duracao`, `categoria_id`) VALUES
    ('Corte Feminino',     'Corte moderno com finalização simples.',     80.00,  60,  1),
    ('Corte Masculino',    'Corte e acabamento masculino.',              50.00,  30,  1),
    ('Mechas Balayage',    'Iluminação suave para visual natural.',     250.00, 180,  2),
    ('Coloração Raiz',     'Retoque de raiz com tinta profissional.',   120.00,  90,  2),
    ('Hidratação Profunda','Restauração de fios danificados.',          120.00,  90,  3),
    ('Manicure',           'Cuidados completos para as unhas das mãos.', 40.00,  45,  4),
    ('Pedicure',           'Cuidados completos para as unhas dos pés.',  50.00,  60,  5);
