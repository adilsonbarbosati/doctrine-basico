<?php

use App\Entity\Category;
use App\Entity\Post;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

//baseado na psr-7, todas as variaveis e gerar uma request de acordo com a prs-7
$request = ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

//criamos um container para as nossa rotas, ele armazenar todas a rotas e configurações
$routerContainer = new RouterContainer();


$generator = $routerContainer->getGenerator();
//agora acessamos o container e pegamos o mapa, pra dai começar a criar as rotas
$map = $routerContainer->getMap();

$view = new \Slim\Views\PhpRenderer(__DIR__.'/../templates/');

$entityManager = getEntityManager();

require_once 'categories.php';
require_once 'posts.php';

$map->get('home', '/', function (ServerRequestInterface $request, $response) use ($view, $entityManager){
    $postsRepository = $entityManager->getRepository(Post::class);
    $categoryRepository = $entityManager->getRepository(Category::class);

    $categories = $categoryRepository->findAll();
    $data = $request->getQueryParams();
    if (isset($data['search']) and $data['search'] != ""){
       $queryBuilder = $postsRepository->createQueryBuilder('p');
       $queryBuilder->join('p.categories', 'c')
           ->where($queryBuilder->expr()->eq('c.id', $data['search']));
       $posts = $queryBuilder->getQuery()->getResult();
    }
    if (!isset($data['search']) or  !$data['search']!=""){
        $posts = $postsRepository->findAll();
    }
    return $view->render($response, 'home.phtml',[
        'posts' => $posts,
        'categories' => $categories
    ]);
});

//temos que criar um combinar , ele ve se a requisiçao que enviamos para o servidor combina com
// alguma rota criada
$matcher = $routerContainer->getMatcher();

//se combinar a gente recebe a nossa rota senao retorna false
$route = $matcher->match($request);

//pegar todos os atributos da rota e passar para a nossa requisição
//
foreach ($route->attributes as $key => $value){
    //com o withattributes podem ter acesso a nossa requisição
    $request = $request->withAttribute($key, $value);
}

//vai mandar nossa request e response para ser executada
$callable = $route->handler;


/** @var Response $response */
//estamos retornado a resposta
$response = $callable($request, new Response());

if ($response instanceof Response\RedirectResponse){
    header("location: {$response->getHeader("location")[0]}");

}elseif ($response instanceof Response){
    //mostra o texto que vai ser renderizado
    echo $response->getBody();
}
