<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class ProductController extends AbstractController
{
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/products', name: 'product_index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $this->repository->findAll(),
        ]);
    }

    #[Route('/product/{id<\d+>}', name: "product_show")]
    public function show(Product $product)
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/new', name: 'product_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $manager->persist($product);

            $manager->flush();

            $this->addFlash(
                'notice',
                'Product created successfully',
            );

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/{id<\d+>}/edit', name: 'product_edit')]
    public function edit(Request $request, Product $product, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $manager->flush();

            $this->addFlash(
                'notice',
                'Product updated successfully',
            );

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/{id<\d+>}/delete', name: 'product_delete')]
    public function delete(Request $request, Product $product, EntityManagerInterface $manager): Response
    {
        if ($request->isMethod('POST')) {
            $manager->remove($product);

            $manager->flush();

            $this->addFlash(
                'notice',
                'Product deleted successfully!',
            );

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/delete.html.twig', [
            'id' => $product->getId(),
        ]);
    }
}
