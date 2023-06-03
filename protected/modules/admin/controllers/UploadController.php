<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\imagine\Image;
use yii\web\Response;
use app\models\Media;
use yii\helpers\Url;
use app\components\Controller;
use yii\helpers\Html;

class UploadController extends Controller {

    public $fromSmugmug = false;

    public function init() {
        $this->enableCsrfValidation = false;
    }

    public function actionFile() {
        $data = [
            'status' => 'error',
            'reason' => 'File not received'
        ];

        if (isset($_FILES['file'])) {
            $file = [
                'orgname' => $_FILES["file"]["name"],
                'extension' => strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION))
            ];

            $data = $this->moveFileToFolder($file);
        }

        $this->echoResponse($data);
    }

    public function actionBrowse($folder) {
        $this->layout = "empty";
        return $this->render('@app/modules/admin/views/layouts/image-library', [
                    'folder' => $folder
        ]);
    }

    protected function moveFileToFolder($file) {
        //Get file type
        $file['type'] = Yii::$app->file->getFileType($file['extension']);
        //Create file name
        $file['filename'] = $this->createFileName($file);

        //Set path to folder
        $server_path_to_folder = $file['server_path_to_folder'] = Yii::getAlias('@webroot') . '/media/' . $file['type'] . '/uploads';

        //Check the existance of the folder
        $this->checkFolder($server_path_to_folder);

        //Set path to file
        $server_path_to_file = $file['server_path_to_file'] = $server_path_to_folder . '/' . $file['filename'];

        //Save the uploaded file to folder
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $server_path_to_file)) {
            $upload = new Media();
            $upload->type = $file['type'];
            $upload->folder = "uploads";
            $upload->orgname = $file['orgname'];
            $upload->name = $file['filename'];
            $upload->extension = $file['extension'];

            if ($upload->type == 'image') {
                $urlPrefix = Url::to('@media', true);
                if ($upload->extension == "svg") {
                    preg_match("#viewbox=[\"']\d* \d* (\d*) (\d*)#i", file_get_contents($server_path_to_file), $svgfile);
                    if ($svgfile) {
                        $upload->width = $svgfile[1];
                        $upload->height = $svgfile[2];
                    }

                    $file['link'] = $urlPrefix . '/' . $upload->type . '/' . $upload->folder . '/' . $upload->name;
                    $file['thumb'] = $urlPrefix . '/' . $upload->type . '/' . $upload->folder . '/' . $upload->name;
                }
                else {
                    list($width, $height) = getimagesize($server_path_to_file);
                    $upload->width = $file['width'] = $width;
                    $upload->height = $file['height'] = $height;
                    $file['link'] = $urlPrefix . '/' . $upload->type . '/' . $width . 'x' . $height . '/' . $upload->name;
                    $file['thumb'] = $urlPrefix . '/' . $upload->type . '/0x140/' . $upload->name;
                    $this->resize($file);
                }
            }

            if ($upload->save()) {
                $file['upload'] = $upload;
                $file['alt'] = Url::to(['default/image', 'id' => $upload->id]);
                $file['status'] = 'success';
                $file['css_class'] = 'fa-file-' . $upload->type . '-o';
                $file['reason'] = $upload->orgname . ' was uploaded successfully.';
                $file['id'] = $upload->id;
            }
            else
                $file['reason'] = Html::errorSummary($upload);
        }
        else
            $file['reason'] = 'Unable to move the uploaded file.';

        return $file;
    }

    public function echoResponse($file) { //Delete certain array elements before you output the array
        $response = [];
        $display = ["css_class", "id", "orgname", "status", "reason", "thumb", "alt", "width", "height", "link"];
        foreach ($file as $key => $value) {
            if (in_array($key, $display)) {
                $response[$key] = $value;
            }
        }
        $response['name'] = $file['filename'];
        $response['cdn'] = Yii::getAlias('@media') . '/' . $file['type'];
        $response['total'] = Media::find()->active()->count();

        echo json_encode($response);
        exit();
    }

    protected function createFileName($file) {
        $name = time() . '_' . md5($file['orgname']) . '.' . $file['extension'];
        return $name;
    }

    protected function checkFolder($pathtofolder) {
        if (!file_exists($pathtofolder))
            mkdir($pathtofolder, 0777, true);
    }

    public function actionResize($w, $h, $name) {
        $webroot = Yii::getAlias('@webroot');

        //Set path to folder
        $pathtofile = $file['server_path_to_folder'] = $webroot . '/media/image/uploads/' . $name;

        //Find from media
        $model = Media::find()->where(['name' => $name])->one();

        if (!$model || !file_exists($pathtofile) || ($w == 0 && $h == 0)) {
            throw new NotFoundHttpException('The requested image does not exist.');
            exit();
        }

        //Dimensions
        $width = $w;
        $height = $h;

        //Calculate the value for 'auto'
        if ($width == 0)
            $width = round($h * $model->width / $model->height);
        else if ($height == 0)
            $height = round($w * $model->height / $model->width);
        else {
            // Find the outbound dimensions
            $scaled_hei = round($width * $model->height / $model->width);
            if ($scaled_hei < $height)
                $width = round($height * $model->width / $model->height);
            else
                $height = $scaled_hei;
        }

        //Path to subfolder
        $path_to_subfolder = $webroot . '/media/image/' . $w . 'x' . $h;
        $path_to_resize = $path_to_subfolder . '/' . $name;

        //Check the existance of the subfolder
        $this->checkFolder($path_to_subfolder);

        //Image quality
        $quality = 100;

        //Use imagine to resize the image
        Image::thumbnail($pathtofile, $width, $height)->save($path_to_resize, ['quality' => $quality]);

        $type = "image/" . $model->extension;
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', $type);
        $response->format = Response::FORMAT_RAW;
        if (!is_resource($response->stream = fopen($path_to_resize, 'r')))
            throw new \yii\web\ServerErrorHttpException('file access failed: permission deny');

        return $response->send();
    }

    public function resize($file) {
        $file_name = $file['filename'];
        $original_file = $file['server_path_to_file'];

        $width = 10;
        $height = 10;
        //Path to subfolder
        $path_to_subfolder = Yii::getAlias('@webroot') . '/media/' . $file['type'] . '/10x10/';

        //Check the existance of the subfolder
        $this->checkFolder($path_to_subfolder);

        //Image quality
        $quality = 80;

        //Use imagine to resize the image
        Image::thumbnail($original_file, $width, $height)->save($path_to_subfolder . '/' . $file_name, ['quality' => $quality]);
    }

}
