<?php declare(strict_types=1);

namespace app\controllers;

use app\models\Task;
use app\models\User;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use function array_map;
use function in_array;
use const SORT_ASC;

class UserController extends Controller
{
    /**
     * @param int $page
     * @param int $limit
     * @param string $sort
     *
     * @return Response
     * @throws \yii\mongodb\Exception
     */
    public function actionIndex(int $page = 1, int $limit = 10, string $sort = User::FIRST_NAME): Response
    {
        $query = User::find();

        if (in_array($sort, [User::FIRST_NAME, User::LAST_NAME, User::EMAIL], true)) {
            $query->orderBy([$sort => SORT_ASC]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        $users = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->asJson(
            [
                'total' => $count,
                'page' => $page,
                'limit' => $limit,
                'users' => array_map(static fn($user) => $user->toArray(), $users)
            ]
        );
    }

    /**
     * @param string $userId
     *
     * @return User
     * @throws NotFoundHttpException
     */
    public function actionView(string $userId): User
    {
        return User::findOne($userId) ?? throw new NotFoundHttpException('User not found.');
    }

    /**
     * @return User|array
     * @throws Exception
     */
    public function actionCreate(): User|array
    {
        $user = new User();
        $user->load(Yii::$app->request->post(), '');
        if ($user->save()) {
            return $user;
        }

        return $user->errors;
    }

    /**
     * @param string $userId
     *
     * @return User|array
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $userId): User|array
    {
        $user = User::findOne($userId) ?? throw new NotFoundHttpException('User not found.');
        $user->load(Yii::$app->request->post(), '');
        if ($user->save()) {
            return $user;
        }

        return $user->errors;
    }

    /**
     * @param string $userId
     *
     * @return bool|int|null
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     */
    public function actionDelete(string $userId): bool|int|null
    {
        $user = User::findOne($userId) ?? throw new NotFoundHttpException('User not found.');
        Task::deleteAll([Task::USER_ID => $userId]);

        return $user->delete();
    }
}
