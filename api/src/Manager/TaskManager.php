<?php


namespace App\Manager;


use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Serializer\DownloadTasksPayloadSerializer;
use App\Serializer\TaskPayloadSerializer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use RuntimeException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskManager
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @var TaskPayloadSerializer
     */
    private TaskPayloadSerializer $taskPayloadSerializer;
    /**
     * @var DownloadTasksPayloadSerializer
     */
    private DownloadTasksPayloadSerializer $downloadTasksPayloadSerializer;
    /**
     * @var TaskRepository
     */
    private TaskRepository $taskRepository;

    public function  __construct(
        TaskPayloadSerializer $taskPayloadSerializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        DownloadTasksPayloadSerializer $downloadTasksPayloadSerializer,
        TaskRepository $taskRepository
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->taskPayloadSerializer = $taskPayloadSerializer;
        $this->downloadTasksPayloadSerializer = $downloadTasksPayloadSerializer;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @param string $payload
     * @param UserInterface $user
     * @return Task
     * @throws Exception
     */
    public function create(string $payload, UserInterface $user): Task
    {
        $taskPayload = $this->taskPayloadSerializer->extract($payload);

        $errors = $this->validator->validate($taskPayload);
        if (count($errors) > 0) {
            throw new RuntimeException('Invalid payload: ' . $errors);
        }

        $task = (new Task())
            ->setTitle($taskPayload->getTitle())
            ->setComment($taskPayload->getComment())
            ->setDate(
                new DateTime(strtotime($taskPayload->getDate()))
            )
            ->setTimeSpent($taskPayload->getTimeSpent())
            ->setUser($user)
        ;

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    /**
     * @param string $payload
     * @param Task $task
     * @return Task
     * @throws Exception
     */
    public function update(string $payload, Task $task): Task
    {
        $taskPayload = $this->taskPayloadSerializer->extract($payload);

        $errors = $this->validator->validate($taskPayload);
        if (count($errors) > 0) {
            throw new RuntimeException('Invalid payload: ' . $errors);
        }

        $task->setTitle($taskPayload->getTitle())
            ->setComment($taskPayload->getComment())
            ->setDate(
                new DateTime(strtotime($taskPayload->getDate()))
            )
            ->setTimeSpent($taskPayload->getTimeSpent())
        ;
        $this->entityManager->flush();

        return $task;
    }

    /**
     * @param string $payload
     * @return array
     */
    public function download(string $payload): array
    {
        $taskPayload = $this->downloadTasksPayloadSerializer->extract($payload);

        $errors = $this->validator->validate($taskPayload);
        if (count($errors) > 0) {
            throw new RuntimeException('Invalid payload: ' . $errors);
        }

        $tasks = $this->taskRepository->search($taskPayload->toArray());
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValueByColumnAndRow(1,1,'ID');
        $sheet->setCellValueByColumnAndRow(2,1,'Title');
        $sheet->setCellValueByColumnAndRow(3,1,'Comment');
        $sheet->setCellValueByColumnAndRow(4,1,'Date');
        $sheet->setCellValueByColumnAndRow(5,1,'Time spent (minutes)');
        $row = 2;
        $totalTime = 0;
        foreach($tasks as $task) {
            $sheet->setCellValueByColumnAndRow(1,$row,$task->getId());
            $sheet->setCellValueByColumnAndRow(2,$row,$task->getTitle());
            $sheet->setCellValueByColumnAndRow(3,$row,$task->getComment());
            $sheet->setCellValueByColumnAndRow(4,$row,$task->getDate()->format('d-m-Y'));
            $sheet->setCellValueByColumnAndRow(5,$row,$task->getTimeSpent());
            $row++;
            $totalTime += $task->getTimeSpent();
        }
        $sheet->setCellValueByColumnAndRow(4,$row,'Total: ');
        $sheet->setCellValueByColumnAndRow(5,$row,$totalTime);
        $sheet->calculateColumnWidths();

        if ($taskPayload->getFormat() === 'csv') {
            $writer = new Csv($spreadsheet);
        } elseif ($taskPayload->getFormat() === 'pdf') {
            $writer = new Mpdf($spreadsheet);
        } else {
            $writer = new Xls($spreadsheet);
        }

        $fileName = uniqid('', true) . '.' . $taskPayload->getFormat();
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);
        return [
            'file'  =>  $temp_file,
            'filename'  =>  $fileName,
        ];
    }

    /**
     * @param Task $task
     */
    public function remove(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}