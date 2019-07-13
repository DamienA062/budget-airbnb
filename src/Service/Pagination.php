<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Effectue une pagination en fonction de l'entité que l'on souhaite paginer
 */
class Pagination
{
    /** 
     * Nom de l'entité sur laquelle on fait la pagination
     * 
     * @var string
    */
    private $entityClass;

    /** 
     * Nombre d'enregistrements à récup, par défaut 10
     * 
     * @var integer
    */
    private $limit = 10;

    /** 
     * Page sur laquelle on se trouve, par défaut 1
     * 
     * @var integer
    */
    private $currentPage = 1;

    /** 
     * @var string
    */
    private $route;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Initialisation de notre objet pagination
     * $templatePath est défini dans services.yaml
     *
     * @param ObjectManager $manager
     * @param Environment $twig
     * @param RequestStack $requestStack
     * @param string $templatePath
     */
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $requestStack, string $templatePath)
    {
        $this->manager = $manager;
        $this->twig = $twig;
        //Récupère la route où est appelé notre pagination object
        $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
        //$templatePath est défini dans services.yaml
        $this->templatePath = $templatePath;
    }

    /**
     * Permet le rendu de la barre de pagination
     *
     * @return void
     */
    public function render()
    {
        //Appel le moteur twig pour lui faire afficher le template pagination
        $this->twig->display('admin/partials/pagination.html.twig', [
            //page actuelle
            'currentPage' => $this->currentPage,
            //nombre total de pages
            'pages' => $this->getPages(),
            //route
            'route' => $this->route
        ]);
    }

    /**
     * Récupère le nombre total de page en fonction de l'entité
     *
     * @return integer
     */
    public function getPages()
    {
        if(empty($this->entityClass))
        {
            throw new \Exception('Aucune entité spécifiée pour la pagination, avez vous peut-être oublié d\'utiliser la méthode setEntityClass() ?');
        }

        //Récupération du nombre total d'entrées de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        //Calcul du nombre total de page à afficher
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    /**
     * Récupère les données en fonction de la limite et de l'offset
     *
     * @return array
     */
    public function getData()
    {
        if(empty($this->entityClass))
        {
            throw new \Exception('Aucune entité spécifiée pour la pagination, avez vous peut-être oublié d\'utiliser la méthode setEntityClass() ?');
        }

        //Calcul de l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        //Récupération du repository et mise en place de la requête
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        return $data;
    }

    /**
     * Get the value of entityClass
     */ 
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set the value of entityClass
     *
     * @return  self
     */ 
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get the value of limit
     */ 
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of currentPage
     */ 
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of currentPage
     *
     * @return  self
     */ 
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get the value of route
     */ 
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the value of route
     *
     * @return  self
     */ 
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get the value of templatePath
     */ 
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Set the value of templatePath
     *
     * @return  self
     */ 
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }
}