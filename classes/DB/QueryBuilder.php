<?php



namespace Shop\DB;


class QueryBuilder {
        
        /**
         * Текущий запрос
         * 
         * @var string
         */
        protected $query_text;
        
        public function getQueryText():string {
            return $this->query_text;
        }
}
