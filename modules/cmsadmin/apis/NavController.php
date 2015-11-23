<?php

namespace cmsadmin\apis;

use Yii;
use cmsadmin\models\Property;
use cmsadmin\models\Nav;
use cmsadmin\models\NavItem;

/**
 * example.com/admin/api-cms-nav/create-page
 * example.com/admin/api-cms-nav/create-item-page
 * example.com/admin/api-cms-nav/create-module
 * example.com/admin/api-cms-nav/create-item-module.
 *
 * @author nadar
 */
class NavController extends \admin\base\RestController
{
    private function postArg($name)
    {
        return Yii::$app->request->post($name, null);
    }

    public function actionGetProperties($navId)
    {
        $data = [];
        foreach (Property::find()->select(['admin_prop_id', 'value'])->where(['nav_id' => $navId])->asArray()->all() as $row) {
            if (is_numeric($row['value'])) {
                $row['value'] = (int) $row['value'];
            }
            $data[] = $row;
        }

        return $data;
    }

    public function actionSaveProperties($navId)
    {
        $rows = [];
        foreach (Yii::$app->request->post() as $id => $value) {
            $rows[] = [
                'nav_id' => $navId,
                'admin_prop_id' => $id,
                'value' => $value,
            ];
        }

        foreach ($rows as $atrs) {
            $model = \cmsadmin\models\Property::find()->where(['admin_prop_id' => $atrs['admin_prop_id'], 'nav_id' => $navId])->one();

            if ($model) {
                if (empty($atrs['value'])) {
                    $model->delete();
                } else {
                    // update
                    $model->value = $atrs['value'];
                    $model->update(false);
                }
            } else {
                $model = new \cmsadmin\models\Property();
                $model->attributes = $atrs;
                $model->insert(false);
            }
        }
    }

    public function actionToggleHidden($navId, $hiddenStatus)
    {
        $item = Nav::find()->where(['id' => $navId])->one();

        if ($item) {
            $item->is_hidden = $hiddenStatus;
            $item->update(false);

            return true;
        }

        return false;
    }

    public function actionToggleHome($navId, $homeState)
    {
        $item = Nav::find()->where(['id' => $navId])->one();

        if ($homeState == 1) {
            Nav::updateAll(['is_home' => 0]);
            $item->setAttributes([
                'is_home' => 1,
            ]);
        } else {
            $item->setAttributes([
                'is_home' => 0,
            ]);
        }

        return $item->update(false);
    }

    public function actionToggleOffline($navId, $offlineStatus)
    {
        $item = Nav::find()->where(['id' => $navId])->one();

        if ($item) {
            $item->is_offline = $offlineStatus;
            $item->update(false);

            return true;
        }

        return false;
    }

    public function actionDetail($navId)
    {
        return \cmsadmin\models\Nav::findOne($navId);
    }

    public function actionDelete($navId)
    {
        $model = \cmsadmin\models\Nav::find()->where(['id' => $navId])->one();
        if ($model) {
            $model->is_deleted = 1;

            foreach (NavItem::find()->where(['nav_id' => $navId])->all() as $navItem) {
                $navItem->setAttribute('alias', date('Y-m-d-H-i').'-'.$navItem->alias);
                $navItem->update(false);
            }

            return $model->update(false);
        }
    }

    public function actionResort()
    {
        $navItemId = $this->postArg('nav_item_id');
        $newSortIndex = $this->postArg('new_sort_index');

        $response = \cmsadmin\models\Nav::resort($navItemId, $newSortIndex);
    }

    /**
     * creates a new nav entry for the type page (nav_id will be created.
     *
     * @param array $_POST:
     */
    public function actionCreatePage()
    {
        $model = new \cmsadmin\models\Nav();
        $create = $model->createPage($this->postArg('parent_nav_id'), $this->postArg('nav_container_id'), $this->postArg('lang_id'), $this->postArg('title'), $this->postArg('alias'), $this->postArg('layout_id'), $this->postArg('description'));

        if ($create !== true) {
            Yii::$app->response->statusCode = 422;
        }

        return $create;
    }

    /**
     * creates a new nav_item entry for the type page (it means nav_id will be delivered).
     */
    public function actionCreatePageItem()
    {
        $model = new \cmsadmin\models\Nav();
        $create = $model->createPageItem($this->postArg('nav_id'), $this->postArg('lang_id'), $this->postArg('title'), $this->postArg('alias'), $this->postArg('layout_id'), $this->postArg('description'));
        if ($create !== true) {
            Yii::$app->response->statusCode = 422;
        }

        return $create;
    }

    public function actionCreateModule()
    {
        $model = new \cmsadmin\models\Nav();
        $create = $model->createModule($this->postArg('parent_nav_id'), $this->postArg('nav_container_id'), $this->postArg('lang_id'), $this->postArg('title'), $this->postArg('alias'), $this->postArg('module_name'), $this->postArg('description'));
        if ($create !== true) {
            Yii::$app->response->statusCode = 422;
        }

        return $create;
    }

    public function actionCreateModuleItem()
    {
        $model = new \cmsadmin\models\Nav();
        $create = $model->createModuleItem($this->postArg('nav_id'), $this->postArg('lang_id'), $this->postArg('title'), $this->postArg('alias'), $this->postArg('module_name'), $this->postArg('description'));
        if ($create !== true) {
            Yii::$app->response->statusCode = 422;
        }

        return $create;
    }

    /* redirect */

    public function actionCreateRedirect()
    {
        $model = new \cmsadmin\models\Nav();
        $create = $model->createRedirect($this->postArg('parent_nav_id'), $this->postArg('nav_container_id'), $this->postArg('lang_id'), $this->postArg('title'), $this->postArg('alias'), $this->postArg('redirect_type'), $this->postArg('redirect_type_value'), $this->postArg('description'));
        if ($create !== true) {
            Yii::$app->response->statusCode = 422;
        }

        return $create;
    }
    
    public function actionCreateRedirectItem()
    {
        $model = new \cmsadmin\models\Nav();
        $create = $model->createRedirectItem($this->postArg('nav_id'), $this->postArg('lang_id'), $this->postArg('title'), $this->postArg('alias'), $this->postArg('redirect_type'), $this->postArg('redirect_type_value'), $this->postArg('description'));
        if ($create !== true) {
            Yii::$app->response->statusCode = 422;
        }
    
        return $create;
    }
}
