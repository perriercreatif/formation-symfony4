<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class PaginationService{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;

    public function __construct(ObjectManager $manager){
        $this->manager = $manager;
    }

    public function getData(){
        //1) calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        //2)demander au repository de trouver les elements

        //3) Renvoyer les elemnets en question
    }


    public function setCurrentPage($page){
        $this->currentPage = $page;
        return $this;
    }

    public function getCurrentPage(){
        return $this->currentPage;
    }

    public function setLimmit($limit){
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }

    public function setEntityClass($entityClass){
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass(){
        return $this->entityClass;
    }
}