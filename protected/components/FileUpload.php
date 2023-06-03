<?php

namespace app\components;

use Yii;
use yii\helpers\Url;
use app\models\Media;

class FileUpload extends \yii\base\Component {
    /* ----------------------------------------
      Rules for the file uploaded to each page
      NOTE: THIS CHANGES FOR EVERY WEBSITE
      ---------------------------------------- */

    protected $rules = [
        "logo" => [
            'sizes' => [
                'big' => [500, 500]
            ]
        ],
         "box" => [
            'sizes' => [
                'big' => [1920, 'auto']
            ]
        ],
        "flute_image" => [
            'sizes' => [
                'big' => [500, 500]
            ]
        ]
    ];

    /* ----------------------------------------
      Uploaded File formats
      ---------------------------------------- */
    protected $formats = [
        'image' => [
            'format' => ['jpg', 'png', 'jpeg', 'gif', 'bmp', 'svg'],
            'size' => 10
        ],
        'video' => [
            'format' => ['mp4', 'm4v', 'm4p', 'webm', 'mkv', 'flv', 'ogg', 'ogv', 'avi', 'qt', 'mov', 'wmv', 'rm', 'rmvb', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'm2v', '3gp', '3g2'],
            'size' => 10
        ],
        'audio' => [
            'format' => ['aiff', 'aac', 'amr', 'au', 'awb', 'dct', 'dss', 'dvf', 'flac', 'gsm', 'iklax', 'ivs', 'm4a', 'mmf', 'mp3', 'mpc', 'msv', 'oga', 'opus', 'ra', 'wav', 'wma', 'wv', 'vox', 'sln', 'tta'],
            'size' => 40
        ],
        'pdf' => [
            'format' => ['pdf'],
            'size' => 20
        ],
        'word' => [
            'format' => ['docx', 'docm', 'dotx', 'dotm', 'docb', 'doc', 'dot', 'rtf', 'txt'],
            'size' => 5
        ],
        'excel' => [
            'format' => ['xlsx', 'xlsm', 'xltx', 'xltm', 'xls', 'xlt', 'xlm', 'xlsb', 'xla', 'xlam', 'xll', 'xlw'],
            'size' => 5
        ],
        'powerpoint' => [
            'format' => ['pptx', 'pptm', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm'],
            'size' => 5
        ],
        'pdf' => [
            'format' => ['pdf'],
            'size' => 20
        ],
    ];

    /* --------------------------------------------
      Functions to expose rules, helpers & formats
      --------------------------------------------- */

    public function getRules($page) {
        if (isset($this->rules[$page]))
            $rule = $this->rules[$page];
        else
            $rule = $this->rules['uploads'];

        //If upload file type doesn't exist, then set "image" as default
        if (!isset($rule['type']))
            $rule['type'] = ['image'];
        else if (!is_array($rule['type']))
            $rule['type'] = (array) $rule['type'];

        //If allow is not an array then type cast it to an array
        if (isset($rule['allow']) && !is_array($rule['allow']))
            $rule['allow'] = (array) $rule['allow'];

        //Set thumb size for images if not specified
        if (in_array('image', $rule['type']) && !isset($rule['sizes']['thumb']))
            $rule['sizes']['thumb'] = ['auto', 140];

        //Set maximum upload size of this file type
        $rule['maxsize'] = $this->getMaxSize($rule['type']);

        //Is allowed to check for image size validation
        if (!isset($rule['validate']))
            $rule['validate'] = true;

        return $rule;
    }

    /* ------------------------------------------------
      Functions to get media link & thumbnail as HTML
      ------------------------------------------------- */

    public function getUrl($media, $resize = '', $absolute = true) {
        //Fetch record if number is passed
        if (is_string($media))
            $media = Media::findOne($media);

        //Variables
        $resize = ($isArray = is_array($resize)) ? (isset($resize[0]) ? $resize[0] : '') : $resize;

        //If media exists
        if ($media) {
            if ($media->type == 'image') { // All images
                $urlPrefix = Url::to('@media', $absolute);
                if ($media->extension == "svg") {
                    $file = $urlPrefix . '/' . $media->type . '/' . $media->folder . '/' . $media->name;
                    $thumb = $urlPrefix . '/' . $media->type . '/' . $media->folder . '/' . $media->name;
                    $cropped = $urlPrefix . '/' . $media->type . '/' . $media->folder . '/' . $media->name;
                } else {
                    $file = $urlPrefix . '/' . $media->type . '/' . $media->width . 'x' . $media->height . '/' . $media->name;
                    $thumb = $urlPrefix . '/' . $media->type . '/0x140/' . $media->name;
                    $cropped = $urlPrefix . '/' . $media->type . '/' . $resize . '/' . $media->name;
                }
            } else {
                $file = Url::to('@media', $absolute) . '/' . $media->type . '/' . $media->folder . '/' . $media->name;
                $thumb = '';
            }
            if ($isArray) {
                return [
                    'file' => $file,
                    'thumb' => $thumb,
                    'resize' => ''
                ];
            } else
                return $cropped;
        }
        return $isArray ? ['file' => '', 'thumb' => '', 'resize' => ''] : '';
    }

    public function getBase64($url) {
        //Convert images to base64 to reduce HTTP calls
        $path = Yii::getAlias('@webroot') . $url;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function thumbHtml($media) {
        $link = $this->getUrl($media, '0x140');
        return $link == "" ? "" : '<img src="' . $link . '" height="140" width="' . round($media->width * 140 / $media->height) . '" />';
    }

    public function getMaxSize($types) {
        $sizes = [];
        foreach ($types as $t) {
            if (isset($this->formats[$t]))
                $sizes[$t] = $this->formats[$t]['size'];
        }
        return $sizes;
    }

    protected function getBaseSize($rule) {
        $temp = [0, 0, false];
        foreach ($rule['sizes'] as $key => $arr) {
            if (isset($arr[2]) && $arr[2] == 'restrict') {
                $temp[0] = $arr[0];
                $temp[1] = $arr[1];
                $temp[2] = true;
                break 1;
            }
            if (((int) $arr[0] >= (int) $temp[0]) && ((int) $arr[1] >= (int) $temp[1])) {
                $temp[0] = $arr[0];
                $temp[1] = $arr[1];
            }
        }
        return $temp;
    }

    public function getTextForUser($rule, $filetypes) {
        if (in_array('image', $filetypes)) {
            if (!$rule['validate'])
                return "";

            if (isset($rule['sizes'])) {
                $temp = $this->getBaseSize($rule);
                if ($temp[2] || ($temp[0] == $temp[1]))
                    return 'The image dimension should be <span>' . $temp[0] . ' x ' . $temp[1] . '</span>';
                else if ($temp[1] == 'auto')
                    return 'The image width should be a minimum of <span>' . $temp[0] . '</span>px';
                else if ($temp[0] == 'auto')
                    return 'The image height should be a minimum of <span>' . $temp[1] . '</span>px';
                else
                    return 'The image dimension should be a minimum of <span>' . $temp[0] . ' x ' . $temp[1] . '</span>';
            }
        }
        return '';
    }

    public function getFormats($rule) {
        $extensions = [];
        foreach ($rule['type'] as $t) {
            if (isset($rule['allow']))
                $extensions[$t] = $rule['allow'];
            else if (isset($this->formats[$t]))
                $extensions[$t] = $this->formats[$t]['format'];
        }
        return $extensions;
    }

    public function getFileType($ext) {
        foreach ($this->formats as $f => $arr) {
            if (in_array($ext, $arr['format']))
                return $f;
        }

        return "";
    }

    public function asBackground($image, $size = 'uploads', $options = ['class' => 'bsz loading']) {
        $link = is_string($image) ? $image : $this->geturl($image, $size);
        if ($link == "")
            return "";

        //Construct HTML
        $alt = "";
        if (isset($image->alt))
            $alt = $image->alt;

        $bp = "";
        if (isset($image->position))
            $bp = ' style="background-position: ' . $image->position . '";';

        $html = '<div';
        foreach ($options as $k => $v) {
            $html .= ' ' . $k . '="' . $v . '"';
        }
        $html .= '><div class="bgimage"' . $bp . '></div><img data-src="' . $link . '" alt="' . $alt . '"></div>';

        return $html;
    }

    public function asImageTag($image, $size = 'uploads', $options = ['class' => 'sizer']) {
        $link = is_string($image) ? $image : $this->geturl($image, $size);

        if ($link == "")
            return "";

        //Construct HTML
        $html = $alt = "";
        if (isset($image->alt))
            $alt = $image->alt;

        $html .= '<div class="image"><div';
        foreach ($options as $k => $v) {
            $html .= ' ' . $k . '="' . $v . '"';
        }
        $html .= ' style="padding-top: ' . ($image->extension != 'svg' ? ($image->height * 100 / $image->width) : '100') . '%"></div>' . $this->asBackground($image) . '</div>';

        return $html;
    }

}
