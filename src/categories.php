<?php

use App\Entity\Category;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

//segunda rotas para direcionar a pagina de listagem de categorias
$map->get('categories.list','/categories', function ($request, $response) use ($view, $entityManager){
    $repository = $entityManager->getRepository(Category::class);
    $categories = $repository->findAll();
    return $view->render($response, 'categories/list.phtml', [
        'categories' => $categories
    ]);
});

//rota para tela de criação de categoria
$map->get('categories.create','/categories/create', function ($request, $response) use ($view){
    return $view->render($response, 'categories/create.phtml');
});

//rota para insercao de categoria
$map->post('categories.store','/categories/store',
    function (ServerRequestInterface $request, $response) use ($view, $entityManager, $generator){
        $data = $request->getParsedBody();
        $category = new Category();
        $category->setName($data['name']);

        //persist muda o estado da entidade, faz com que o doctrine conheça essa entidade
        $entityManager->persist($category);
        //esse metodo vai propagar no banco de dados
        $entityManager->flush();

        $uri = $generator->generate('categories.list');
        return new Response\RedirectResponse($uri);
    });

//rota para tela de edicao de categoria
$map->get('categories.edit','/categories/{id}/edit', function (ServerRequestInterface $request, $response) use ($view, $entityManager){
    $id = $request->getAttribute('id');
    $repository = $entityManager->getRepository(Category::class);
    $category = $repository->find($id);
    return $view->render($response, 'categories/edit.phtml', [
        'category' => $category
    ]);
});


//rota para atualizacao de categoria
$map->post('categories.update','/categories/{id}/update',
    function (ServerRequestInterface $request, $response) use ($view, $entityManager, $generator){
        $id = $request->getAttribute('id');
        $repository = $entityManager->getRepository(Category::class);
        $category = $repository->find($id);

        $data = $request->getParsedBody();

        $category->setName($data['name']);

        //esse metodo vai propagar no banco de dados
        $entityManager->flush();

        $uri = $generator->generate('categories.list');
        return new Response\RedirectResponse($uri);
    });

//rota para exclusão de categoria
$map->get('categories.remove','/categories/{id}/remove',
    function (ServerRequestInterface $request, $response) use ($view, $entityManager, $generator){
        $id = $request->getAttribute('id');
        $repository = $entityManager->getRepository(Category::class);
        $category = $repository->find($id);


        //o metodo remove não remove o objeto do banco, ele só informa ao banco que queremos
        // remover algo do banco, somente com o metodo flush() é que executamos a exclusão
        $entityManager->remove($category);
        $entityManager->flush();

        $uri = $generator->generate('categories.list');
        return new Response\RedirectResponse($uri);
    });