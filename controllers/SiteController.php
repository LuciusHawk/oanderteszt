<?php

namespace app\controllers;

use app\models\Monitor;
use mirocow\eav\models\EavEntity;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{

    private $ignoredAttributes = array('id', 'entityModel', 'categoryId');

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Lists all Monitor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $monitor = new Monitor();
        $monitors = $monitor->getAllMonitors();
        $attributes = array();
        foreach ($monitors as $monitor) {
            $attr = $monitor->getMonitorAttributes();
            $attributes = array_unique(array_merge($attributes, $attr));
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $monitors,
            'sort' => [
                'attributes' => $attributes,
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);


        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Monitor model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = new Monitor();
        $model->createMonitor($this->findModel($id));
        $attributes = array();
        foreach ($model as $key => $value) {
            if (!in_array($key, $this->ignoredAttributes)) {
                $attributes[] = $key;
            }
        }

        return $this->render('view', [
            'model' => $model,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Creates a new Monitor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Monitor();
        $model->createMonitor();
        $attributes = array();
        foreach ($model as $key => $value) {
            if (!in_array($key, $this->ignoredAttributes)) {
                $attributes[$key] = $value;
            }
        }
        if ($post = Yii::$app->request->post('MonitorForm')) {
            foreach ($post as $key => $value) {
                $model->$key = $value;
            }
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Updates an existing Monitor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = new Monitor();
        $model->createMonitor($this->findModel($id));
        $attributes = array();
        foreach ($model as $key => $value) {
            if (!in_array($key, $this->ignoredAttributes)) {
                $attributes[$key] = $value;
            }
        }
        if ($post = Yii::$app->request->post('MonitorForm')) {
            foreach ($post as $key => $value) {
                $model->$key = $value;
            }
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Deletes an existing Monitor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //$this->findModel($id)->delete();
        $model = new Monitor();
        $model->createMonitor($this->findModel($id));
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Finds the Monitor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Monitor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EavEntity::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
