<?php
class DbModel { 
        public string $dsn;
        public ?string $user;
        public ?string $passwd;
        public ?array $options;
}

class SqlModel {
        public string $sql;
        public ?array $options;    
}
?>