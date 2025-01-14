<?php

namespace backend\controllers;

use common\models\Course;
use common\models\CourseSearch;
use common\models\RegisterCart;
use common\models\Registerdetail;
use Yii;
use common\models\RegisterCourse;
use common\models\RegisterCourseSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use yii\data\Pagination;
use yii\db\Expression;
use yii\web\Session;

use kartik\growl\Growl;
use yii\helpers\Html;
USE yii\filters\VerbFilter;

/**
 * RegisterCourseController implements the CRUD actions for RegisterCourse model.
 */
class RegisterCourseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'addtocart','checkout'],
                'rules' => [


                    [
                        'actions' => ['index','addtocart','checkout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }



    public function actionIndex($sort ='box')
    {

        $totalCount = Course::find();
        if(!empty($_POST['search'])){
            $search='%'.$_POST['search'].'%';

            $totalCount = $totalCount->where('name LIKE(:search)', [
                ':search' => $search
            ]);
        }
        $totalCount = $totalCount->count();
        $pagination = new Pagination([
            'totalCount' => $totalCount,
            'pageSize' =>4
        ]);
        $course = Course::find()
            ->orderBy('id DESC')
            ->offset($pagination->offset)
            ->limit($pagination->limit);

        if (!empty($_POST['search'])) {
            $search = '%'.$_POST['search'].'%';

            $course = $course
                ->where('name LIKE(:search) OR price LIKE(:search) OR cod_id LIKE(:search)', [
                    ':search' => $search
                ]);
        }

        return $this->render('index', [

            'course' => $course->all(),
            'pagination' => $pagination,
            'sort' => $sort
        ]);
    }


    public function actionAddtocart($id=null)
    {
        $product = Course::findOne($id);
        $session = new Session();
        $session->open();
        $cart = [];

        if (!empty($session->get('coursecart'))) {
            $cart = $session->get('coursecart');
        }
        if (!empty($_POST)) {
            $data = [
                'id' => $product->id,
                'code' => $product->cod_id,
                'name' => $product->name,
                'price' => $product->price,

            ];

            $check_array = 0;
            foreach($cart as $c){
                if($c['id'] == $product->id){
                    $check_array = 1;
                }

            }

            if($check_array == 0){
                $cart[count($cart)] = $data;
                $session->set('coursecart', $cart);
            }else{

               Yii::$app->session->setFlash('danger', 'มีรายวิชานี้อยู่');


            }
        }

        return $this->render('//register-course/AddToCart', [
            'product' => $product,
            'cart' => $cart,
            'n' => 1,
            'sumQty' => 0,
            'sumPrice' => 0
        ]);


    }







    public function actionCartremove($index, $id) {
        $session = new Session();
        $session->open();
        $_SESSION['num']=10;
        $cart = $session['coursecart'];

        if (count($cart) > 0) {
            $cart[$index] = null;
            $newCart = [];

            foreach ($cart as $c) {
                if ($c != null) {
                    $newCart[] = $c;
                }
            }

            $session->set('coursecart', $newCart);

            return $this->redirect(['addtocart', 'id' => $id]);
        }
    }
    public function actionCheckout()
    {
        $session = new Session();
        $session->open();
        if (!empty($session->get('coursecart'))) {
            $cart = $session->get('coursecart');
        }else{
            Yii::$app->getSession()->setFlash('alert', [
                'type' => Growl::TYPE_WARNING,
                'duration' => 1800,
                'icon' => 'fa fa-remove fa-2x',
                'title' => Yii::t('app', Html::encode('เกิดข้อผิดพลาด !'), ['class' => 'text-center']),
                'message' => Yii::t('app',Html::encode('กรุณาเลือกคอร์สเรียนก่อน')),
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
            return $this->redirect(['addtocart']);
        }


        $RegisterCourse = new RegisterCourse();
      //  var_dump($cart);
        if (!empty($_POST)) {
            // save bill order
          // $RegisterCourse->register_date = new Expression('NOW()');
            $RegisterCourse->status = '0';
            $RegisterCourse->student_id = $_POST['RegisterCourse']['student_id'];

            if ($RegisterCourse->save()) {
                // loop read data from session to database

                foreach ($cart as $c) {
                    $billOrderDetail = new Registerdetail();
                    $billOrderDetail->register_course_id = $RegisterCourse->id;
                    $billOrderDetail->course_id = $c['id'];
                    $billOrderDetail->price = $c['price'];

                    $billOrderDetail->save();
                }
//
                // clear session
                $session->set('coursecart', null);

                return $this->redirect(['checkoutsuccess']);
            }
        }
//
        return $this->render('//register-course/Checkout', [
            'n' => 1,
            'cart' => $cart,
            'sumQty' => 0,
            'sumPrice' => 0,
            'RegisterCourse' => $RegisterCourse
        ]);
    }
    public function actionCheckoutsuccess() {
        return $this->render('//register-course/CheckoutSuccess');
    }
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }




protected function findModel($id)
    {
        if (($model = RegisterCourse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

public function actionShow()
{
    $RegisterCourse = RegisterCourse::find()->where(['status' => 0]);
    $countQuery = clone $RegisterCourse;
    $pages = new Pagination([
        'totalCount' => $countQuery->count(),
        'pageSize' =>10
    ]);
    $models = $RegisterCourse->offset($pages->offset)
        ->limit($pages->limit)
        ->all();




    return $this->render('show',[
        'models' => $models,
        'pages' => $pages,

    ]);
}
    public function actionSuccess()
    {
        $RegisterCourse = RegisterCourse::find()->where(['status' => 1]);
        $countQuery = clone $RegisterCourse;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' =>10
        ]);
        $models = $RegisterCourse->offset($pages->offset)
            ->limit($pages->limit)
            ->all();




        return $this->render('success',[
            'models' => $models,
            'pages' => $pages,

        ]);
    }

    public function actionDetail($id)
    {
        //return$id;
        $Detail =Registerdetail::findAll(['register_course_id'=>$id]);
     return $this->render('detail',[
         'Detail'=>$Detail
     ]);
    }
    public function actionCancel($id)
    {
        $cancel =RegisterCourse::findOne($id);

        $register = Registerdetail::find()->where(['register_course_id' => $cancel->id])->all();
        //$profile = Registerdetail::findOne(['register_course_id'=>$user->id]);
        foreach ($register as $pro) {

            $pro->delete();
        }
        $cancel->delete();



        return $this->redirect(['//register-course/show']);
        //return $this->redirect(['index']);
    }


}
