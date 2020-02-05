<?php
namespace app\controllers;

use app\models\Site;
use app\models\Ticket;
use app\models\TicketComment;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class FeedbackController extends Controller {

    public function actionIndex() {
        $model = new Ticket();

        \Yii::$app->session->set("lastRef", \Yii::$app->request->referrer);

        $ticketsDataProvider = null;
        $ticketsClosedDataProvider = null;

        if(!\Yii::$app->getUser()->isGuest) {
            $ticketsDataProvider = new ActiveDataProvider([
                'query' => Ticket::find()->where(["is_deleted" => 0, "user_id" => \Yii::$app->getUser()->getId()])->andWhere(["<>", "status", 4])->orderBy(["id" => SORT_DESC])
            ]);
            $ticketsClosedDataProvider = new ActiveDataProvider([
                'query' => Ticket::find()->where(["is_deleted" => 0, "user_id" => \Yii::$app->getUser()->getId(), "status" => 4])->orderBy(["id" => SORT_DESC])
            ]);
        }

        return $this->render("index", [
            "model" => $model,
            "ticketsDataProvider" => $ticketsDataProvider,
            "ticketsClosedDataProvider" => $ticketsClosedDataProvider
        ]);
    }

    public function actionNew() {
        $ticket = new Ticket();

        $site = Site::find()->where(["name" => $_SERVER["HTTP_HOST"]])->one();
        $ticket->site_id = $site->id;
        $ticket->url = \Yii::$app->session->get("lastRef", null);

        if ($ticket->load(\Yii::$app->getRequest()->post()) && $ticket->save()) {
            return $this->redirect(['feedback/index']);
        }

        return $this->render("new", [
            "ticket" => $ticket
        ]);
    }

    public function actionView($id) {
        if(\Yii::$app->getUser()->isGuest) return $this->redirect(["site/index", "#" => "signin"]);
        $ticket = Ticket::find()->where(["id" => $id, "user_id" => \Yii::$app->getUser()->getId()])->one();
        if(!$ticket) {
            new ForbiddenHttpException("Нет доступа");
        }

        TicketComment::updateAll(["tm_read" => new Expression('NOW()')], "ticket_id = ".$ticket->id." AND tm_read is null AND user_id <> ".\Yii::$app->getUser()->id);

        if($ticket->status == 2) {
            $ticket->status = 3;
            $ticket->save(false);
        }

        $comments = TicketComment::find()->with("user")->where(["is_deleted" => 0, "ticket_id" => $ticket->id])->orderBy(["id" => SORT_ASC])->all();

        $comment = new TicketComment();

        return $this->render("view", [
            "ticket" => $ticket,
            "comments" => $comments,
            "comment" => $comment
        ]);
    }

    public function actionComment($id) {
        $ticket = Ticket::find()->where(["id" => $id, "user_id" => \Yii::$app->getUser()->getId()])->one();
        if(!$ticket) {
            new ForbiddenHttpException("Нет доступа");
        }

        $comment = new TicketComment();
        $comment->load(\Yii::$app->request->post());
        $comment->ticket_id = $id;
        $comment->save();

        if(!in_array($ticket->status, [6,7])) {
            $ticket->status = 0;
        }

        $ticket->save(false);

        return $this->redirect(["feedback/view", "id" => $id]);
    }

    public function actionClose($id) {
        $model = Ticket::find()->where(["id" => $id])->one();
        if($model->user_id != \Yii::$app->getUser()->getId()) {
            throw new ForbiddenHttpException("Нет доступа");
        }

        $model->tm_close = new Expression('NOW()');
        $model->status = 4;
        $model->save(false);

        return $this->redirect(['feedback/index']);
    }

    public function actionReopen($id) {
        $model = Ticket::find()->where(["id" => $id])->one();
        if($model->user_id != \Yii::$app->getUser()->getId()) {
            throw new ForbiddenHttpException("Нет доступа");
        }

        $model->tm_reopen = new Expression('NOW()');
        $model->status = 5;
        $model->save(false);
        return $this->redirect(['feedback/index']);
    }
}