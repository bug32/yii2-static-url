<?php

namespace bug32\staticUrl\controllers;

use bug32\staticUrl\components\StaticUrlRule;
use bug32\staticUrl\models\StaticUrl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BackendController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new StaticUrl();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new StaticUrl();
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                StaticUrlRule::clearCache();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            StaticUrlRule::clearCache();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        StaticUrlRule::clearCache();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = StaticUrl::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
    }
} 