<?php

//apelido do arquivo (Alias)
namespace Models;

// faz a chamada da classe PDO
use \PDO;
use \PDOException;

class Database{

  # Host de conexão com o banco de dados (nome do serviço no docker-compose)
  const HOST = 'mysql';

  # Nome do banco de dados (criado automaticamente pelo Docker)
  const NAME = 'projeto';

  # Usuário do banco
  const USER = 'aluno';

  # Senha de acesso ao banco de dados
  const PASS = '123456';

  # Porta do banco de dados
  const PORT = '3306';


  # Nome da tabela a ser manipulada
  private ?string $table;

 # Instancia de conexão com o banco de dados
 private PDO $connection;

  # Define a tabela e instancia e conexão
  public function __construct(?string $table = null){
    $this->table = $table;
    $this->setConnection();
  }

  # Método responsável por criar uma conexão com o banco de dados
  private function setConnection(){
    try{
      $this->connection = new PDO(
        'mysql:host='.self::HOST.';dbname='.self::NAME.';port='.self::PORT.';charset=utf8mb4',
        self::USER,
        self::PASS,
        [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ]
      );
    }catch(PDOException $e){
      throw $e;
    }
  }

  # Método responsável por executar queries dentro do banco de dados
  public function execute($query,$params = []){
    try{
      $statement = $this->connection->prepare($query);
      $statement->execute($params);
      return $statement;
    }catch(PDOException $e){
      throw $e;
    }
  }
  

# CRUD
# C - Create
# R - Read
# U - Update
# D - Delete

/*
 ### C- Create:  Método responsável por inserir dados no banco
 ### Exemplo para a criar uma query de Inserção no Banco de Dados
  $values = array(
    'cidades_id' => 1,
    'cidades_nome' => 'Ceres',
    'cidades_uf' => 'GO'
  );

  INSERT INTO `cidades` 
  (`cidades_id`, `cidades_nome`,`cidades_uf`) 
  VALUES 
  (NULL, 'Ceres', 'GO');
*/

  public function insert($values){
    //Extração de chaves e valores do array
    $fields = array_keys($values); // pega as chaves do array
    $binds  = array_pad([],count($fields),'?'); // cria as posições de inserção da query

    //monta a query de inserção
    // Insert INTO CIDADES (cidades_nome, cidades_uf) VALUES (?,?)
    $query = 'INSERT INTO '.$this->table.' ('.implode(',',$fields).') VALUES ('.implode(',',$binds).')';
    
    //executa a query e passa o valores que serão inseridos no banco
    // Ex.: $values = ['Ceres', 'GO'];
    $this->execute($query,array_values($values));

    //retorna o ultimo id inserido.
    return $this->connection->lastInsertId();
  }

  /* 
    #####  R- Read : Método responsável por fazer uma consulta no banco
    ### Exemplo para a criar uma query de seleção em um Banco de Dados
    Exemplo:
     SELECT * from enderecos
     inner join cidades on enderecos_cidades_id = cidades_id 
     WHERE cidades_id = 1 
     order by cidades_nome 
     LIMIT 1
  */
  public function select($join = null, $where = null, $order = null, $limit = null,$fields = '*'){
    
    //Complementos da query de seleção
    $join  = !empty($join)  ? ' INNER JOIN '.$join : ''; //junção de tabelas
    $where = !empty($where) ? ' WHERE '.$where : ''; //clausula where
    $order = !empty($order) ? ' ORDER BY '.$order : ''; //ordenação
    $limit = !empty($limit) ? ' LIMIT '.$limit : ''; // limite de retorno de dados

    //monta a query
    $query = 'SELECT '.$fields.' FROM '.$this->table.' '.$join.' '.$where.' '.$order.' '.$limit;
    
    //Executa a query
    return $this->execute($query);
  }

  /*
  #####  U - Update : Método responsável por fazer uma atualização da tabela do banco
  ### Exemplo para a criar uma query de Atualização em uma linha da tabela do Banco de Dados
  Exemplo:
        UPDATE cidades set 
        cidades_nome = 'Ceres', 
        cidades_uf = 'GO'
        WHERE cidades_id = 1
  */
  public function update($where,$values){
    //Dados da query
    $fields = array_keys($values);

    //Monta a query
    $set = implode(' = ?, ', $fields) . ' = ?';
    $query = "UPDATE {$this->table} SET {$set} WHERE {$where}";
    
    //EXECUTAR A QUERY
    $this->execute($query,array_values($values));

    //RETORNA SUCESSO
    return true;
  }
/*
  #####  D- Delete : Método responsável por remover dados da tabela do banco
  Exemplo:
  
  delete from cidades
  where cidades_id = 1

*/
  public function delete($where){
    //Monta a query
    $query = 'DELETE FROM '.$this->table.' WHERE '.$where;
    
    //Executa a query
    $this->execute($query);
    
    //Retorna verdadeiro caso tenha sucesso na exclusão
    return true;
  }

}