<?php declare(strict_types=1);

namespace app\controllers;

use app\models\Status;
use app\models\Task;
use RuntimeException;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use function array_column;
use function array_map;
use function in_array;
use const SORT_ASC;

class TaskController extends Controller
{
    /**
     * @param string $userId
     * @param int $page
     * @param int $limit
     * @param string $sort
     *
     * @return Response
     * @throws \yii\mongodb\Exception
     */
    public function actionIndex(string $userId, int $page = 1, int $limit = 10, string $sort = Task::TITLE): Response
    {
        $query = Task::find()->where([Task::USER_ID => $userId]);

        if (in_array($sort, [Task::TITLE, Task::STATUS], true)) {
            $query->orderBy([$sort => SORT_ASC]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        $tasks = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->asJson(
            [
                'total' => $count,
                'page' => $page,
                'limit' => $limit,
                'tasks' => array_map(static fn($task) => $task->toArray(), $tasks)
            ]
        );
    }


    /**
     * @param string $userId
     * @param string $taskId
     *
     * @return Task
     * @throws NotFoundHttpException
     */
    public function actionView(string $userId, string $taskId): Task
    {
        $task = Task::findOne([Task::ID => $taskId, Task::USER_ID => $userId]) ?? throw new NotFoundHttpException(
            'Task not found.'
        );
        $task->status = Status::from($task->status)->getLabel();

        return $task;
    }

    /**
     * @param string $userId
     *
     * @return Task|array
     * @throws Exception
     */
    public function actionCreate(string $userId): Task|array
    {
        $task = new Task();
        $task->load(Yii::$app->request->post(), '');
        $task->user_id = $userId;
        $task->status = Status::NEW->value;
        if ($task->save()) {
            $task->status = Status::from($task->status)->getLabel();

            return $task;
        }

        return $task->errors;
    }

    /**
     * @param string $userId
     * @param string $taskId
     *
     * @return Task|array
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws RuntimeException
     */
    public function actionUpdate(string $userId, string $taskId): Task|array
    {
        $task = Task::findOne([Task::ID => $taskId, Task::USER_ID => $userId])
            ?? throw new NotFoundHttpException('Task not found.');
        $oldStatus = $task->status;
        $postData = Yii::$app->request->post();
        $task->load($postData, '');
        if ($oldStatus !== $task->status
            && !(Status::from($oldStatus)->canTransitionTo(Status::from($task->status)))
        ) {
            throw new RuntimeException('Can not update task status.');
        }
        if ($task->save()) {
            $task->status = Status::from($task->status)->getLabel();

            return $task;
        }

        return $task->errors;
    }

    /**
     * @param string $userId
     * @param string $taskId
     *
     * @return bool|int|null
     * @throws StaleObjectException
     */
    public function actionDelete(string $userId, string $taskId): bool|int|null
    {
        $task = Task::findOne([Task::ID => $taskId, Task::USER_ID => $userId]);
        if ($task && $task->status === Status::NEW->value) {
            return $task->delete();
        }
        throw new RuntimeException('Task cannot be deleted.');
    }

    /**
     * @param string $userId
     *
     * @return Response
     * @throws \yii\mongodb\Exception
     */
    public function actionUserStats(string $userId): Response
    {
        $pipeline = [
            ['$match' => [Task::USER_ID => $userId]],
            ['$group' => [Task::ID => '$status', 'count' => ['$sum' => 1]]],
        ];

        $stats = Task::getCollection()->aggregate($pipeline);

        $formattedStats = array_column($stats, 'count', Task::ID);

        return $this->asJson(
            [
                Task::USER_ID => $userId,
                'task_stats' => $formattedStats
            ]
        );
    }

    /**
     *
     * @return Response
     * @throws \yii\mongodb\Exception
     */
    public function actionStats(): Response
    {
        $pipeline = [
            ['$group' => [Task::ID => '$status', 'count' => ['$sum' => 1]]],
        ];

        $stats = Task::getCollection()->aggregate($pipeline);

        $formattedStats = array_column($stats, 'count', Task::ID);

        return $this->asJson(
            [
                'task_stats' => $formattedStats
            ]
        );
    }
}
