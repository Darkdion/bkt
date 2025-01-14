<?php

namespace backend\controllers;

use Yii;
use common\models\School;
use common\models\SchoolSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\growl\Growl;

/**
 * SchoolController implements the CRUD actions for School model.
 */
class SchoolController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all School models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SchoolSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=5;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single School model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new School model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new School();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('alert', [
                'type' => 'success',
                'duration' => 1000,
                'icon' => ' glyphicon glyphicon-th-large',
                'title' => Yii::t('app', Html::encode('บันทึกข้อมูล')),
                'message' => Yii::t('app',Html::encode('บันทึกโรงเรียนเรียบร้อย')),
                'positonY' => 'top',
                'positonX' => 'right', ]);
            return $this->redirect(['index', 'id' => $model->id]);
        } else {

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing School model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('alert', [
                'type' => Growl::TYPE_INFO,
                'duration' => 1200,
                'icon' => 'fa fa-building-o fa-2x',
                'title' => Yii::t('app', Html::encode('ปรับปรุง'), ['class' => 'text-center']),
                'message' => Yii::t('app',Html::encode('ปรับปรุงข้อมูลโรงเรียนเรียบร้อย !')),
                'showSeparator' => true,
                'delay' => 1,
                'pluginOptions' => [
                    'showProgressbar' => true,
                    'placement' => [
                        'from' => 'top',
                        'align' => 'right',
                    ]
                ]
            ]);


            return $this->redirect(['index', 'id' => $model->id]);
        } else {


            return $this->render('update', [
                'model' => $model,
            ]

            );

        }
    }

    /**
     * Deletes an existing School model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('alert', [
            'type' => Growl::TYPE_WARNING,
            'duration' => 1700,
            'icon' => 'fa fa-trash fa-2x',
            'title' => Yii::t('app', Html::encode('ลบข้อมูล')),
            'message' => Yii::t('app',Html::encode('ลบข้อมูลเรียบร้อย !')),
            'showSeparator' => true,
            'delay' => 1500,
            'pluginOptions' => [
                'showProgressbar' => true,
                'placement' => [
                    'from' => 'top',
                    'align' => 'right',
                ]
            ]
        ]);
        return $this->redirect(['index']);
    }

    /**
     * Finds the School model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return School the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = School::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
