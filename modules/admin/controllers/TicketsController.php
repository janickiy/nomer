<?php
namespace app\modules\admin\controllers;

use app\models\Site;
use app\models\Ticket;
use app\models\TicketComment;
use app\models\TicketReply;
use app\models\User;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class TicketsController extends AdminController {

    public function actionIndex() {
        $statuses = [0, 1];

        if(\Yii::$app->getUser()->getId() == 1) {
            $statuses = [0, 1, 7];
        }

        $query = Ticket::find()->where(["is_deleted" => 0, "status" => $statuses])->with("user")->orderBy(["id" => SORT_DESC]);
        $ticketsNotRead = new ActiveDataProvider([
            'query' => $query
        ]);

        $tIds = ArrayHelper::getColumn($ticketsNotRead->getModels(), "id");

        $tickets = new ActiveDataProvider([
            'query' => Ticket::find()->where(["NOT IN", "id", $tIds])->andWhere(["is_deleted" => 0])->with("user")->orderBy(["id" => SORT_DESC])
        ]);


        return $this->render("index", [
            "tickets"       => $tickets,
            "ticketsNotRead" => $ticketsNotRead
        ]);
    }

    public function actionIgnore($id) {
        $ticket = Ticket::find()->where(["id" => $id])->one();
        $ticket->status = 6;
        $ticket->tm_close = new Expression('NOW()');
        $ticket->save(false);

        return $this->redirect(["tickets/index"]);
    }

    public function actionDevelop($id) {
        $ticket = Ticket::find()->where(["id" => $id])->one();
        $ticket->status = 7;
        $ticket->tm_close = new Expression('NOW()');
        $ticket->save(false);

        return $this->redirect(["tickets/index"]);
    }

    public function actionClose($id) {
        $ticket = Ticket::find()->where(["id" => $id])->one();
        $ticket->status = 4;
        $ticket->tm_close = new Expression('NOW()');
        $ticket->save(false);

        return $this->redirect(["tickets/index"]);
    }

    public function actionDelete($id) {
        $ticket = Ticket::find()->where(["id" => $id])->one();
        $ticket->is_deleted = true;
        $ticket->save(false);

        return $this->redirect(["tickets/index"]);
    }

    public function actionReopen($id) {
        $ticket = Ticket::find()->where(["id" => $id])->one();
        $ticket->status = 1;
        $ticket->save(false);

        return $this->redirect(["tickets/view", "id" => $id]);
    }

    public function actionCommentDelete($id) {
        $comment = TicketComment::find()->where(["id" => $id])->one();
        $comment->is_deleted = true;
        $comment->save();

        return $this->redirect(["tickets/view", "id" => $comment->ticket_id]);
    }

    public function actionAddReply($id) {
        $ticket = Ticket::find()->where(["id" => $id])->one();

        $text = \Yii::$app->request->post("text");

        if(trim($text) != "") {
            $reply = new TicketReply();
            $reply->subject_id = $ticket->subject_id;
            $reply->text = $text;
            $reply->save();
            return $this->renderAjax('_reply', ['reply' => $reply]);
        }

        return '';
    }

    public function actionView($id) {
        $ticket = Ticket::find()->where(["id" => $id])->one();

        TicketComment::updateAll(["tm_read" => new Expression('NOW()')], "ticket_id = ".$ticket->id." AND tm_read is null AND user_id <> ".\Yii::$app->getUser()->id);

        if($ticket->status == 0) {
            $ticket->status = 1;
        }

        $ticket->tm_read = new Expression('NOW()');
        $ticket->save(false);

        $comments = new ActiveDataProvider([
            "query" => TicketComment::find()->where(["ticket_id" => $ticket->id, "is_deleted" => 0])->orderBy(["id" => SORT_ASC])
        ]);

        $comment = new TicketComment();

        $replies = TicketReply::find()->all();

        return $this->render("view", [
            "ticket"    => $ticket,
            "comments"  => $comments,
            "comment"   => $comment,
            "replies"   => $replies
        ]);
    }

    public function actionComment($id) {
        $comment = new TicketComment();
        $comment->load(\Yii::$app->request->post());
        $comment->ticket_id = $id;

        $comment->save();

        $ticket = Ticket::find()->where(["id" => $id])->one();
        $user = User::find()->where(["id" => $ticket->user_id])->one();
        $site = Site::find()->where(["id" => $ticket->site_id])->one();

        $ticket->status = 2;
        $ticket->save(false);

        try {
            if(preg_match('/@/', $user->email)) {
                \Yii::$app->mailer->compose()
                    ->setHtmlBody("Администратор оставил новый ответ.<br><a href='https://".$site->name."/feedback'></a>")
                    ->setFrom('noreply@'.$site->name)
                    ->setTo($user->email)
                    ->setSubject($site->name." - новый отет на запрос")
                    ->send();
            }
        } catch (Exception $e) {}


        return $this->redirect(["tickets/view", "id" => $id]);
    }
}