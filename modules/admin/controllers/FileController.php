<?php

namespace admin\controllers;

use admin\storage\FileQuery;
use admin\models\StorageFile;
use yii\web\BadRequestHttpException;
use yii\web\yii\web;

class FileController extends \luya\web\Controller
{
    public function actionDownload($id, $hash, $fileName)
    {
        // find file in file query
        $fileData = (new FileQuery())->where(['id' => $id, 'hash_name' => $hash, 'is_deleted' => 0])->one();
        // proceed when file exists
        if ($fileData) {
            // get file source from storage system
            $fileSourcePath = $fileData->serverSource;
            // verify again against database to add counter
            $model = StorageFile::findOne($fileData->id);
            // proceed when model exists
            if ($model && file_exists($fileSourcePath)) {
                // update the model count stats
                $count = $model->passthrough_file_stats + 1;
                $model->passthrough_file_stats = $count;
                $model->update(false);
                // return header informations
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($fileData->name).'"');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fileSourcePath));
                ob_clean();
                flush();
                readfile($fileSourcePath);
                exit;
            }
        }
        
        // throw bad request exception.
        throw new BadRequestHttpException("Unable to find requested file.");
    }
}
