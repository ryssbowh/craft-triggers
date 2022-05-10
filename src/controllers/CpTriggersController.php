<?php 

namespace Ryssbowh\CraftTriggers\controllers;

use Ryssbowh\CraftTriggers\Triggers;
use Ryssbowh\CraftTriggers\exceptions\TriggerException;
use Ryssbowh\CraftTriggers\models\Trigger;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use craft\web\View;
use yii\base\Event;
use yii\web\Response;

class CpTriggersController extends Controller
{
    const EVENT_EDIT_TRIGGER = 'event-edit-trigger';

    /**
     * All actions require permission 'accessPlugin-triggers' and 'manageTriggers'
     */
    public function beforeAction($action): bool
    {
        $this->requirePermission('accessPlugin-triggers');
        $this->requirePermission('manageTriggers');
        return true;
    }

    /**
     * Triggers dashboard action
     * 
     * @return Response
     */
    public function actionIndex()
    {
        return $this->renderTemplate('triggers/triggers', [
            'triggers' => Triggers::$plugin->triggers->allTriggers
        ]);
    }

    /**
     * Add trigger action
     * 
     * @return Response
     */
    public function actionAdd(?Trigger $trigger = null)
    {
        if (!$trigger) {
            $trigger = new Trigger;
        }
        $triggers = array_map(function ($trigger) {
            return $trigger->type;
        }, Triggers::$plugin->triggers->registeredTriggers);
        $this->trigger(self::EVENT_EDIT_TRIGGER, new Event);
        return $this->renderTemplate('triggers/add-trigger', [
            'triggers' => ['' => \Craft::t('triggers', 'Select')] + $triggers,
            'conditions' => Triggers::$plugin->triggers->registeredConditions,
            'actions' => Triggers::$plugin->triggers->registeredActions,
            'errors' => $trigger->errors,
            'trigger' => $trigger
        ]);
    }

    /**
     * Edit trigger action
     * 
     * @param  int    $id
     * @return Response
     */
    public function actionEdit(int $id)
    {
        $trigger = Triggers::$plugin->triggers->getTriggerById($id);
        return $this->actionAdd($trigger);
    }

    /**
     * Save trigger action
     * 
     * @return Response
     */
    public function actionSave()
    {
        $handle = $this->request->getBodyParam('handle');
        $id = $this->request->getBodyParam('id');
        if ($id) {
            $trigger = Triggers::$plugin->triggers->getTriggerById($id);
        } else if ($handle) {
            $trigger = Triggers::$plugin->triggers->getRegisteredTrigger($handle);
        } else {
            throw TriggerException::handleMissing();
        }
        $params = $this->request->getBodyParams();
        unset($params['action']);
        unset($params['CRAFT_CSRF_TOKEN']);
        $trigger->populateFromData($params);
        if (Triggers::$plugin->triggers->save($trigger)) {
            \Craft::$app->session->setNotice(\Craft::t('triggers', 'Trigger saved.'));
            return $this->redirect(UrlHelper::cpUrl('triggers'));
        }
        return $this->actionAdd($trigger);
    }

    /**
     * Delete trigger action
     *
     * @param  int $id
     * @return Response
     */
    public function actionDelete(int $id)
    {
        $trigger = Triggers::$plugin->triggers->getTriggerById($id);
        if (Triggers::$plugin->triggers->delete($trigger)) {
            $message = \Craft::t('triggers', 'Trigger has been deleted.');
            if ($this->request->isAjax) {
                return $this->asJson([
                    'message' => $message
                ]);
            }
        } else {
            $message = \Craft::t('triggers', 'Error while deleting trigger.');
            if ($this->request->isAjax) {
                $this->response->setStatusCode(400);
                return $this->asJson([
                    'message' => $message
                ]);
            }
        }
        \Craft::$app->session->setNotice($message);
        return $this->redirect(UrlHelper::cpUrl('triggers/triggers'));
    }

    /**
     * Trigger config action
     * 
     * @return Reponse
     */
    public function actionTriggerConfig()
    {
        \Craft::$app->language = \Craft::$app->user->identity->preferences['language'];
        $handle = $this->request->getRequiredParam('handle');
        $trigger = Triggers::$plugin->triggers->getRegisteredTrigger($handle);
        if ($trigger->hasConfig()) {
            $view = \Craft::$app->view;
            $html = $view->renderTemplate('triggers/trigger-config', [
                'trigger' => $trigger
            ], View::TEMPLATE_MODE_CP);
            return $this->asJson([
                'html' => $html,
                'instructions' => $trigger->instructions,
                'tip' => $trigger->tip,
                'headHtml' => $view->getHeadHtml(),
                'footHtml' => $view->getBodyHtml(),
            ]);
        }
        return $this->asJson([]);
    }

    /**
     * New action action
     * 
     * @return Response
     */
    public function actionNewAction()
    {
        \Craft::$app->language = \Craft::$app->user->identity->preferences['language'];
        $handle = $this->request->getRequiredParam('handle');
        $action = Triggers::$plugin->triggers->getRegisteredAction($handle);
        $action->handle = $handle;
        $action->validate();
        $view = \Craft::$app->view;
        $html = $view->renderTemplate('triggers/action', [
            'action' => $action
        ], View::TEMPLATE_MODE_CP);
        return $this->asJson([
            'html' => $html,
            'headHtml' => $view->getHeadHtml(),
            'footHtml' => $view->getBodyHtml(),
        ]);
    }

    /**
     * Valdiate action action
     * 
     * @return Response
     */
    public function actionValidateAction()
    {
        \Craft::$app->language = \Craft::$app->user->identity->preferences['language'];
        $namespace = $this->request->getRequiredParam('namespace');
        $handle = $this->request->getRequiredParam('handle');
        $actionData = $this->request->getBodyParam('actionData', []);
        $action = Triggers::$plugin->triggers->getRegisteredAction($handle);
        $actionData['handle'] = $handle;
        $action->populateFromData($actionData);
        $action->validate();
        $view = \Craft::$app->view;
        $html = $view->renderTemplate('triggers/action-config', [
            'action' => $action,
            'namespace' => 'actions' . $namespace
        ], View::TEMPLATE_MODE_CP);
        return $this->asJson([
            'html' => $html,
            'headHtml' => $view->getHeadHtml(),
            'footHtml' => $view->getBodyHtml(),
            'description' => $action->description,
            'errors' => $action->hasErrors()
        ]);
    }

    /**
     * New condition action
     * 
     * @return Response
     */
    public function actionNewCondition()
    {
        \Craft::$app->language = \Craft::$app->user->identity->preferences['language'];
        $handle = $this->request->getRequiredParam('handle');
        $condition = Triggers::$plugin->triggers->getRegisteredCondition($handle);
        $condition->handle = $handle;
        $condition->validate();
        $view = \Craft::$app->view;
        $html = $view->renderTemplate('triggers/condition', [
            'condition' => $condition
        ], View::TEMPLATE_MODE_CP);
        return $this->asJson([
            'html' => $html,
            'headHtml' => $view->getHeadHtml(),
            'footHtml' => $view->getBodyHtml(),
        ]);
    }

    /**
     * Validate condition action
     * 
     * @return Response
     */
    public function actionValidateCondition()
    {
        \Craft::$app->language = \Craft::$app->user->identity->preferences['language'];
        $namespace = $this->request->getRequiredParam('namespace');
        $conditionData = $this->request->getBodyParam('condition', []);
        $handle = $this->request->getRequiredParam('handle');
        $condition = Triggers::$plugin->triggers->getRegisteredCondition($handle);
        $conditionData['handle'] = $handle;
        $condition->populateFromData($conditionData);
        $condition->validate();
        $view = \Craft::$app->view;
        $html = $view->renderTemplate('triggers/condition-config', [
            'condition' => $condition,
            'namespace' => $namespace
        ], View::TEMPLATE_MODE_CP);
        return $this->asJson([
            'html' => $html,
            'headHtml' => $view->getHeadHtml(),
            'footHtml' => $view->getBodyHtml(),
            'description' => $condition->description,
            'errors' => $condition->hasErrors()
        ]);
    }
}