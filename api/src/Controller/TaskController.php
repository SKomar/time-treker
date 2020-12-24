<?php


namespace App\Controller;


use App\Manager\TaskManager;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("api/tasks", name="tasks_") */
class TaskController extends AbstractController
{
    /**
     * @var TaskRepository
     */
    private TaskRepository $taskRepository;
    /**
     * @var TaskManager
     */
    private TaskManager $taskManager;

    public function __construct(
        TaskRepository $taskRepository,
        TaskManager $taskManager
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskManager = $taskManager;
    }

    /**
     * @return JsonResponse
     * @Route("/", name="index", methods={"GET"})
     */
    public function getPosts(): JsonResponse
    {
        return new JsonResponse([
            'tasks' => $this->taskRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @Route("/", name="add", methods={"POST"})
     */
    public function addTask(Request $request): JsonResponse
    {
        try {
            $user = $this->taskManager->create(
                $request->getContent(),
                $this->getUser()
            );
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
        return new JsonResponse([
            'success' => true,
            'task_id' => $user->getId(),
        ]);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/{id}", name="get", methods={"GET"}, requirements = {"id"="\d+"})
     */
    public function getTask(int $id): JsonResponse
    {
        if (!$task = $this->taskRepository->find($id)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Task not found!',
            ]);
        }
        return new JsonResponse([
            'success' => true,
            'task' => $task->jsonSerialize(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @Route("/{id}", name="put", methods={"PUT"}, requirements = {"id"="\d+"})
     */
    public function updateTask(Request $request, int $id): JsonResponse
    {
        if (!$task = $this->taskRepository->find($id)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Task not found!',
            ]);
        }
        try {
            $task = $this->taskManager->update(
                $request->getContent(),
                $task
            );
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
        return new JsonResponse([
            'success' => true,
            'task_id' => $task->getId(),
        ]);
    }


    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements = {"id"="\d+"})
     */
    public function deleteTask(int $id): JsonResponse
    {
        if (!$task = $this->taskRepository->find($id)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Task not found!',
            ]);
        }
        $this->taskManager->remove($task);
        return new JsonResponse([
            'success' => true,
            'errors' => "Task deleted successfully",
        ]);
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse|JsonResponse
     * @Route("/download", name="download", methods={"GET"})
     */
    public function download(Request $request)
    {
        try {
            $task = $this->taskManager->download(
                $request->getContent()
            );
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
        $response = new BinaryFileResponse($task['file']);
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $task['filename'],
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}