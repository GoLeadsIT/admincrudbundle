<?php

namespace Goleadsit\AdminCrudBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;

class CrudController extends AbstractController {

    public function index(Request $request, string $entityFQN, array $paths): Response {
        $query = $this->getDoctrine()->getRepository($entityFQN)->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        $params = [
            'headerTitle'    => substr($entityFQN, strrpos($entityFQN, '\\') + 1),
            'headerSubtitle' => '(' . $pagination->getTotalItemCount() . ')',
            'paths'          => $paths,
            'values'         => $pagination
        ];

        if(!empty($paths['new'])) {
            $params['headerButton'] = [
                'path' => $paths['new'],
                'icon' => 'fa-plus',
                'text' => 'Añadir'
            ];
        }

        return $this->renderTemplate($entityFQN, 'index', $params);
    }

    public function new(Request $request, string $entityFQN, string $form, array $paths) {
        $entity = new $entityFQN();
        $form = $this->createForm($form, $entity);
        $form->handleRequest($request);

        try {
            if($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirectToRoute($paths['edit'], [
                    'id' => $entity->getId()
                ]);
            }
        } catch(UniqueConstraintViolationException $e) {
            $message = substr($e->getMessage(), strpos($e->getMessage(), 'SQLSTATE'));
            $message = substr($message, 0, strpos($message, ' for key'));
            $this->addFlash('warning', 'No se ha podido insertar porque los datos ya existen en la base de datos! <br> <i>' . $message . '</i>');
        }

        $params = [
            'headerTitle'    => substr($entityFQN, strrpos($entityFQN, '\\') + 1),
            'headerSubtitle' => 'Nuevo/a',
            'entity'         => $entity,
            'form'           => $form->createView(),
            'type'           => 'new'
        ];

        if(!empty($paths['index'])) {
            $params['headerButton'] = [
                'path' => $paths['index'],
                'icon' => 'fa-arrow-left',
                'text' => 'Volver'
            ];
        }

        return $this->renderTemplate($entityFQN, 'new', $params);
    }

    public function show(string $entityFQN, int $id, $form, $paths): Response {
        $entity = $this->getDoctrine()->getManager()->getRepository($entityFQN)->find($id);

        if(!$entity) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm($form, $entity, ['disabled' => true]);

        $params = [
            'headerTitle'    => substr($entityFQN, strrpos($entityFQN, '\\') + 1),
            'headerSubtitle' => $id . ' / ' . $entity,
            'form'           => $form->createView(),
            'type'           => 'show'
        ];

        if(!empty($paths['index'])) {
            $params['headerButton'] = [
                'path' => $paths['index'],
                'icon' => 'fa-arrow-left',
                'text' => 'Volver'
            ];
        }

        return $this->renderTemplate($entityFQN, 'show', $params);
    }

    public function edit(Request $request, string $entityFQN, int $id, $form, $paths): Response {
        $entity = $this->getDoctrine()->getManager()->getRepository($entityFQN)->find($id);

        if(!$entity) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm($form, $entity);
        $form->handleRequest($request);

        try {
            if($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute($paths['edit'], [
                    'id' => $id
                ]);
            }
        } catch(UniqueConstraintViolationException $e) {
            $message = substr($e->getMessage(), strpos($e->getMessage(), 'SQLSTATE'));
            $message = substr($message, 0, strpos($message, ' for key'));
            $this->addFlash('warning', 'No se ha podido editar porque los datos que intentas insertar ya existen en la base de datos! <br> <i>' . $message . '</i>');
        }

        $params = [
            'headerTitle'    => substr($entityFQN, strrpos($entityFQN, '\\') + 1),
            'headerSubtitle' => 'Editar',
            'form'           => $form->createView(),
            'type'           => 'edit'
        ];

        if(!empty($paths['index'])) {
            $params['headerButton'] = [
                'path' => $paths['index'],
                'icon' => 'fa-arrow-left',
                'text' => 'Volver'
            ];
        }

        return $this->renderTemplate($entityFQN, 'edit', $params);
    }

    public function delete(Request $request, string $entityFQN, int $id, $paths): Response {
        $entity = $this->getDoctrine()->getManager()->getRepository($entityFQN)->find($id);

        if(!$entity) {
            throw $this->createNotFoundException();
        }

        if($request->isMethod('DELETE')) {
            if($this->isCsrfTokenValid('delete' . $entity->getId(), $request->request->get('_token'))) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($entity);
                $em->flush();
            }
        }
        else if($request->isMethod('GET')) {
            return $this->renderTemplate($entityFQN, 'delete', [
                'headerTitle'    => substr($entityFQN, strrpos($entityFQN, '\\') + 1),
                'headerSubtitle' => 'Eliminar',
                'paths'          => $paths,
                'entity'         => $entity
            ]);
        }

        return $this->redirectToRoute($paths['index']);
    }

    public function renderTemplate(string $entityFQN, string $action, array $parameters = []): Response {
        $directory = strtolower(substr($entityFQN, strrpos($entityFQN, '\\') + 1));
        $template = $action . '.html.twig';

        try {
            return $this->render($directory . '/' . $template, $parameters);
        } catch(LoaderError $error) {
            $parameters['form_template'] = $directory . '/' . 'form.html.twig';

            return $this->render('@GoleadsitAdminCrud/' . $template, $parameters);
        }
    }
}
